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
use Illuminate\Support\Facades\Storage;
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
            $purchaseRequests = PurchaseRequest::with(['user', 'items', 'location', 'category'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Untuk user lain, gunakan method helper untuk filter
            $allPRs = PurchaseRequest::with(['user', 'items', 'location', 'category'])
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
        
        // Get active PR categories
        $prCategories = \App\Models\Access_PR\PrCategory::active()->orderBy('name')->get();
        
        // Get active master locations
        $masterLocations = \App\Models\MasterLocation::getActiveLocations();

        return view('Access_PR.Purchase_Request.create', compact('approvalLevels', 'defaultApprovalFlow', 'prCategories', 'masterLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:pr_categories,id',
            'due_date' => 'nullable|date|after:today',
            'description' => 'required|string|max:1000',
            'location_id' => 'required|exists:master_locations,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,jpg,png,pdf|max:2048' // max 2MB per file
        ]);

        DB::beginTransaction();
        try {
            // Calculate total estimated price
            $totalEstimatedPrice = collect($request->items)->sum(function($item) {
                return (float)($item['estimated_price'] ?? 0);
            });

            // Get approval flow from selected category
            $category = \App\Models\Access_PR\PrCategory::findOrFail($request->category_id);
            $approvalFlow = $category->approval_rules;

            // Get location for PR number generation
            $location = \App\Models\MasterLocation::findOrFail($request->location_id);

            // Generate PR Number based on location code
            $prNumber = PurchaseRequest::generatePRNumber($location->code);
            
            // Create Purchase Request
            $purchaseRequest = PurchaseRequest::create([
                'pr_number' => $prNumber,
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'request_date' => Carbon::now()->toDateString(),
                'due_date' => $request->due_date,
                'description' => $request->description,
                'location_id' => $request->location_id,
                'status' => 'SUBMITTED',
                'approval_flow' => array_values($approvalFlow), // Reindex array
                'approvals' => [],
                'notes' => $request->notes,
                'total_estimated_price' => $totalEstimatedPrice
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

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $attachmentPaths = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('purchase-requests/attachments', 'public');
                    $attachmentPaths[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
                
                // Update purchase request with attachment info
                $purchaseRequest->update([
                    'attachments' => $attachmentPaths
                ]);
            }

            DB::commit();
            
            return redirect()->route('purchase-request.show', $purchaseRequest)
                ->with('success', 'Purchase Request berhasil dibuat dengan nomor: ' . $prNumber . 
                    ($totalEstimatedPrice > 5000000 ? ' (CEO approval tersedia opsional karena total > Rp 5.000.000)' : ''));
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal membuat Purchase Request: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['user', 'items', 'statusUpdates.updatedBy', 'location', 'category']);
        
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
        
        // Get PR categories and Master Locations
        $prCategories = \App\Models\Access_PR\PrCategory::getActiveCategories();
        $masterLocations = \App\Models\MasterLocation::getActiveLocations();

        $purchaseRequest->load('items');

        return view('Access_PR.Purchase_Request.edit', compact('purchaseRequest', 'approvalLevels', 'prCategories', 'masterLocations'));
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
            'location_id' => 'required|exists:master_locations,id',
            'category_id' => 'required|exists:pr_categories,id',
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
                'location_id' => $request->location_id,
                'category_id' => $request->category_id,
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

    public function addAttachment(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Check authorization - only owner or admin can add attachments
        if (Auth::user()->id !== $purchaseRequest->user_id && Auth::user()->level !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Allow adding files in most statuses except COMPLETED or CANCELLED
        if (in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED'])) {
            return redirect()->back()->with('error', 'Tidak dapat menambah file pada PR dengan status ' . $purchaseRequest->status);
        }

        $currentAttachments = $purchaseRequest->attachments ?? [];
        $currentCount = count($currentAttachments);

        $request->validate([
            'attachments' => 'required|array',
            'attachments.*' => 'file|mimes:jpeg,jpg,png,pdf|max:2048' // max 2MB per file
        ]);

        // Check maximum file limit
        $newFileCount = count($request->file('attachments'));
        if ($currentCount + $newFileCount > 5) {
            return redirect()->back()->with('error', 'Maksimal hanya dapat mengunggah 5 file. Saat ini sudah ada ' . $currentCount . ' file.');
        }

        $currentFileLogs = $purchaseRequest->file_logs ?? [];
        
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('purchase-requests/attachments', 'public');
            
            $attachmentData = [
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];
            
            $currentAttachments[] = $attachmentData;
            
            // Add to file logs
            $currentFileLogs[] = [
                'action' => 'added',
                'message' => 'File "' . $file->getClientOriginalName() . '" ditambahkan oleh ' . Auth::user()->name,
                'timestamp' => now()->setTimezone('Asia/Jakarta')->toISOString(),
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name
            ];
        }
        
        $purchaseRequest->update([
            'attachments' => $currentAttachments,
            'file_logs' => $currentFileLogs
        ]);

        return redirect()->back()->with('success', 'File berhasil ditambahkan.');
    }

    public function deleteAttachment(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Check authorization - only owner or admin can delete attachments
        if (Auth::user()->id !== $purchaseRequest->user_id && Auth::user()->level !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Allow deleting files in most statuses except COMPLETED or CANCELLED
        if (in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED'])) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus file pada PR dengan status ' . $purchaseRequest->status);
        }

        $request->validate([
            'file_index' => 'required|integer|min:0'
        ]);

        $attachments = $purchaseRequest->attachments ?? [];
        $fileLogs = $purchaseRequest->file_logs ?? [];
        $fileIndex = $request->file_index;
        
        if (!isset($attachments[$fileIndex])) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fileToDelete = $attachments[$fileIndex];
        
        // Delete physical file from storage
        if (isset($fileToDelete['path']) && Storage::disk('public')->exists($fileToDelete['path'])) {
            Storage::disk('public')->delete($fileToDelete['path']);
        }

        // Add to file logs
        $fileLogs[] = [
            'action' => 'deleted',
            'message' => 'File "' . ($fileToDelete['original_name'] ?? 'Unknown') . '" dihapus oleh ' . Auth::user()->name,
            'timestamp' => now()->setTimezone('Asia/Jakarta')->toISOString(),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name
        ];

        // Remove from attachments array
        unset($attachments[$fileIndex]);
        $attachments = array_values($attachments); // Reindex array

        $purchaseRequest->update([
            'attachments' => $attachments,
            'file_logs' => $fileLogs
        ]);

        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }
}
