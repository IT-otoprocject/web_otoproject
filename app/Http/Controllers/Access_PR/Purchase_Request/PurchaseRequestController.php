<?php

namespace App\Http\Controllers\Access_PR\Purchase_Request;

use App\Http\Controllers\Controller;
use App\Models\Access_PR\Purchase_Request\PurchaseRequest;
use App\Models\Access_PR\Purchase_Request\PurchaseRequestItem;
use App\Models\Access_PR\Purchase_Request\PurchaseRequestStatusUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Admin dan purchasing bisa lihat semua PR
        if ($user->level === 'admin' || 
            ($user->divisi === 'PURCHASING' && in_array($user->level, ['manager', 'spv', 'staff']))) {
            $purchaseRequests = PurchaseRequest::with(['user', 'items'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Untuk user lain, gunakan method helper untuk filter
            $allPRs = PurchaseRequest::with(['user', 'items'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function($pr) use ($user) {
                    return $pr->canBeViewedByUser($user);
                });
            
            // Convert collection to paginator
            $perPage = 15;
            $currentPage = request()->get('page', 1);
            $currentItems = $allPRs->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $purchaseRequests = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $allPRs->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('Access_PR.Purchase_Request.index', compact('purchaseRequests'));
    }

    public function create()
    {
        $user = Auth::user();
        $approvalLevels = PurchaseRequest::getAvailableApprovalLevels();
        $defaultApprovalFlow = PurchaseRequest::getApprovalFlowByDivisi($user->divisi);

        return view('Access_PR.Purchase_Request.create', compact('approvalLevels', 'defaultApprovalFlow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'due_date' => 'nullable|date|after:today',
            'description' => 'required|string|max:1000',
            'location' => 'required|in:HQ,BRANCH,OTHER',
            'approval_flow' => 'required|array|min:1',
            'approval_flow.*' => 'required|string|in:dept_head,ga,finance_dept,ceo,cfo',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Generate PR Number
            $prNumber = PurchaseRequest::generatePRNumber($request->location);
            
            // Create Purchase Request
            $purchaseRequest = PurchaseRequest::create([
                'pr_number' => $prNumber,
                'user_id' => Auth::id(),
                'request_date' => Carbon::now()->toDateString(),
                'due_date' => $request->due_date,
                'description' => $request->description,
                'location' => $request->location,
                'status' => 'SUBMITTED',
                'approval_flow' => $request->approval_flow,
                'approvals' => [],
                'notes' => $request->notes
            ]);

            // Create Purchase Request Items
            foreach ($request->items as $item) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'estimated_price' => $item['estimated_price'],
                    'notes' => $item['notes']
                ]);
            }

            DB::commit();
            
            return redirect()->route('purchase-request.show', $purchaseRequest)
                ->with('success', 'Purchase Request berhasil dibuat dengan nomor: ' . $prNumber);
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal membuat Purchase Request: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['user', 'items', 'statusUpdates.updatedBy']);
        
        // Check authorization menggunakan method helper dari model
        $user = Auth::user();
        
        if (!$purchaseRequest->canBeViewedByUser($user)) {
            abort(403, 'Unauthorized');
        }

        $approvalStatus = $purchaseRequest->getApprovalStatus();
        $canApprove = $purchaseRequest->canBeApprovedByUser($user) && $purchaseRequest->status !== 'REJECTED';
        
        // User purchasing hanya bisa update status jika PR sudah Fully Approved
        $canUpdateStatus = ($user->level === 'admin' || $user->level === 'ceo' || $user->level === 'cfo') || 
                          ($user->divisi === 'PURCHASING' && 
                           in_array($user->level, ['manager', 'spv', 'staff']) && 
                           $purchaseRequest->isFullyApproved() && 
                           $purchaseRequest->status === 'APPROVED');

        return view('Access_PR.Purchase_Request.show', compact(
            'purchaseRequest', 
            'approvalStatus', 
            'canApprove',
            'canUpdateStatus'
        ));
    }

    public function approve(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        
        // Check if PR is already rejected
        if ($purchaseRequest->status === 'REJECTED') {
            return back()->with('error', 'Purchase Request sudah ditolak dan tidak bisa diproses lagi.');
        }
        
        // Gunakan method yang baru untuk check authorization
        if (!$purchaseRequest->canBeApprovedByUser($user)) {
            return back()->with('error', 'Anda tidak dapat menyetujui PR ini saat ini.');
        }

        $approvals = $purchaseRequest->approvals ?? [];
        
        // Get current approval level yang sedang pending
        $currentApprovalLevel = $purchaseRequest->getCurrentApprovalLevel();
        
        // Update approval untuk level yang sedang pending
        $approvals[$currentApprovalLevel] = [
            'approved' => true,
            'approved_at' => Carbon::now('Asia/Jakarta')->toISOString(),
            'approved_by' => $user->id,
            'approved_by_name' => $user->name,
            'approved_by_divisi' => $user->divisi,
            'approved_by_level' => $user->level,
            'notes' => $request->notes
        ];

        // Check if all levels are approved
        $flow = $purchaseRequest->approval_flow;
        $allApproved = true;
        foreach ($flow as $level) {
            if (!isset($approvals[$level]['approved']) || !$approvals[$level]['approved']) {
                $allApproved = false;
                break;
            }
        }

        // Update status PR
        $newStatus = $allApproved ? 'APPROVED' : 'SUBMITTED';

        $purchaseRequest->update([
            'approvals' => $approvals,
            'status' => $newStatus
        ]);

        if ($allApproved) {
            return back()->with('success', 'Purchase Request berhasil disetujui dan telah mencapai persetujuan final!');
        } else {
            return back()->with('success', 'Purchase Request berhasil disetujui. Menunggu persetujuan dari level berikutnya.');
        }
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        
        // Check if PR is already rejected
        if ($purchaseRequest->status === 'REJECTED') {
            return back()->with('error', 'Purchase Request sudah ditolak sebelumnya.');
        }
        
        // Check authorization - gunakan method yang sama seperti approve
        if (!$purchaseRequest->canBeApprovedByUser($user)) {
            return back()->with('error', 'Anda tidak dapat menolak PR ini.');
        }

        $approvals = $purchaseRequest->approvals ?? [];
        $currentApprovalLevel = $purchaseRequest->getCurrentApprovalLevel();
        
        $approvals[$currentApprovalLevel] = [
            'approved' => false,
            'approved_at' => Carbon::now('Asia/Jakarta')->toISOString(),
            'approved_by' => $user->id,
            'approved_by_name' => $user->name,
            'approved_by_divisi' => $user->divisi,
            'approved_by_level' => $user->level,
            'notes' => $request->notes
        ];

        // Status langsung menjadi REJECTED dan tidak bisa diproses lagi
        $purchaseRequest->update([
            'approvals' => $approvals,
            'status' => 'REJECTED'
        ]);

        return back()->with('success', 'Purchase Request telah ditolak. Status ini final dan tidak dapat diubah lagi.');
    }

    public function updateStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'update_type' => 'required|in:VENDOR_SEARCH,PRICE_COMPARISON,PO_CREATED,GOODS_RECEIVED,GOODS_RETURNED,CLOSED',
            'description' => 'required|string|max:1000',
            'data' => 'nullable|array'
        ]);

        // Only admin, CEO, CFO and purchasing can update status, and PR must be fully approved
        $user = Auth::user();
        $canUpdate = (in_array($user->level, ['admin', 'ceo', 'cfo'])) || 
                    ($user->divisi === 'PURCHASING' && 
                     in_array($user->level, ['manager', 'spv', 'staff']) && 
                     $purchaseRequest->isFullyApproved() && 
                     $purchaseRequest->status === 'APPROVED');
        
        if (!$canUpdate) {
            abort(403, 'Unauthorized. Hanya Admin, CEO, CFO, atau divisi Purchasing yang dapat mengupdate status pada PR yang sudah Fully Approved.');
        }

        PurchaseRequestStatusUpdate::create([
            'purchase_request_id' => $purchaseRequest->id,
            'update_type' => $request->update_type,
            'description' => $request->description,
            'data' => $request->data,
            'updated_by' => Auth::id()
        ]);

        if ($request->update_type === 'CLOSED') {
            $purchaseRequest->update(['status' => 'COMPLETED']);
        }

        return back()->with('success', 'Status update berhasil ditambahkan.');
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Only creator can edit and only if not yet submitted
        if ($purchaseRequest->user_id !== Auth::id() || $purchaseRequest->status !== 'DRAFT') {
            abort(403, 'Unauthorized');
        }

        $approvalLevels = PurchaseRequest::getAvailableApprovalLevels();

        $purchaseRequest->load('items');

        return view('Access_PR.Purchase_Request.edit', compact('purchaseRequest', 'approvalLevels'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Only creator can edit and only if not yet submitted
        if ($purchaseRequest->user_id !== Auth::id() || $purchaseRequest->status !== 'DRAFT') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'due_date' => 'nullable|date|after:today',
            'description' => 'required|string|max:1000',
            'location' => 'required|in:HQ,BRANCH,OTHER',
            'approval_flow' => 'required|array|min:1',
            'approval_flow.*' => 'required|string|in:manager,spv,headstore,ga,finance_dept,ceo,cfo',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Update Purchase Request
            $purchaseRequest->update([
                'due_date' => $request->due_date,
                'description' => $request->description,
                'location' => $request->location,
                'approval_flow' => $request->approval_flow,
                'notes' => $request->notes
            ]);

            // Delete existing items and create new ones
            $purchaseRequest->items()->delete();
            
            foreach ($request->items as $item) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'estimated_price' => $item['estimated_price'],
                    'notes' => $item['notes']
                ]);
            }

            DB::commit();
            
            return redirect()->route('purchase-request.show', $purchaseRequest)
                ->with('success', 'Purchase Request berhasil diupdate.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal mengupdate Purchase Request: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        // Only admin or creator (if draft) can delete
        if (!in_array(Auth::user()->level, ['admin']) && 
            !($purchaseRequest->user_id === Auth::id() && $purchaseRequest->status === 'DRAFT')) {
            abort(403, 'Unauthorized');
        }

        $purchaseRequest->delete();
        
        return redirect()->route('purchase-request.index')
            ->with('success', 'Purchase Request berhasil dihapus.');
    }
}
