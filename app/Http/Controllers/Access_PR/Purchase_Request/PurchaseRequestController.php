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
use Barryvdh\DomPDF\Facade\Pdf;

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

        // For GA users, compute PRs pending GA decision (generate or mark non-asset GA)
        $gaAssetPendingCount = 0;
        $isGA = ($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff']));
        if ($isGA) {
            // Attach computed flags per PR
            foreach ($purchaseRequests as $pr) {
                // Items that still require GA decision: no item-level GA flag and no pr_item_assets records
                $pendingItems = $pr->items->filter(function($it) use ($pr) {
                    $hasAssets = \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::where('purchase_request_item_id', $it->id)->exists();
                    return is_null($it->is_asset_hcga) && !$hasAssets;
                });
                $pr->ga_pending_item_count = $pendingItems->count();
                $pr->has_any_assets = \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::where('purchase_request_id', $pr->id)->exists();
                $pr->needs_ga_action = ($pr->ga_pending_item_count > 0) && $pr->areAllItemsCompleted();
                if ($pr->needs_ga_action) { $gaAssetPendingCount++; }
            }
        }

        return view('Access_PR.Purchase_Request.index', compact('purchaseRequests', 'gaAssetPendingCount'));
    }

    // GA marks specific items as non-asset at GA level (without generating numbers)
    public function markItemsNonAssetGA(Request $request, PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if (!($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff']))) {
            return back()->with('error', 'Hanya tim GA yang dapat menentukan status asset/non-asset GA.');
        }
        if (!$purchaseRequest->areAllItemsCompleted()) {
            return back()->with('error', 'Keputusan GA hanya dapat diinput setelah purchasing selesai.');
        }

        $request->validate([
            'non_asset_ga_item_ids' => 'required|array',
            'non_asset_ga_item_ids.*' => 'integer|exists:purchase_request_items,id',
        ]);

        DB::beginTransaction();
        try {
            $itemIds = $request->input('non_asset_ga_item_ids', []);
            $items = $purchaseRequest->items()->whereIn('id', $itemIds)->get();
            foreach ($items as $item) {
                $item->update(['is_asset_hcga' => false]);
            }
            DB::commit();
            return back()->with('success', 'Barang dipilih telah ditandai Non-Asset (GA).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan Non-Asset GA: ' . $e->getMessage());
        }
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
            // Calculate total estimated price (quantity × unit price for each item)
            $totalEstimatedPrice = collect($request->items)->sum(function($item) {
                $quantity = (float)($item['quantity'] ?? 0);
                $unitPrice = (float)($item['estimated_price'] ?? 0);
                return $quantity * $unitPrice;
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
    $purchaseRequest->load(['user', 'items.paymentMethod', 'statusUpdates.updatedBy', 'location', 'category']);
        
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

    // Generate PDF untuk Purchase Request
    public function print(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['user', 'items', 'location']);

        $user = Auth::user();
        if (!$purchaseRequest->canBeViewedByUser($user)) {
            abort(403, 'Unauthorized');
        }

        $approvalStatus = $purchaseRequest->getApprovalStatus();

        // Hitung total estimasi (fallback jika field total_estimated_price kosong)
        $calculatedTotal = $purchaseRequest->items->sum(function($item) {
            return ($item->quantity ?? 0) * ($item->estimated_price ?? 0);
        });
        $total = $purchaseRequest->total_estimated_price ?? $calculatedTotal;

        $pdf = Pdf::loadView('Access_PR.Purchase_Request.print', [
            'purchaseRequest' => $purchaseRequest,
            'approvalStatus' => $approvalStatus,
            'total' => $total,
        ])->setPaper('a4');

        // Sanitize filename: remove characters not allowed in Content-Disposition (e.g. '/' and '\\')
        $baseName = $purchaseRequest->pr_number ?: ('PR-' . $purchaseRequest->id);
        $safeBase = preg_replace('/[^A-Za-z0-9._-]+/', '_', $baseName);
        if (!$safeBase) {
            $safeBase = 'PR_' . $purchaseRequest->id;
        }
        $filename = 'PR_' . $safeBase . '.pdf';
        return $pdf->stream($filename);
    }

    public function approve(Request $request, PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        $currentApprovalLevel = $purchaseRequest->getCurrentApprovalLevel();
        $isFATApproval = $currentApprovalLevel === 'finance_dept' && $user->divisi === 'FAT';
        
        // Validation rules - different for FAT approval
        $rules = ['notes' => 'nullable|string|max:500'];
        
        if ($isFATApproval) {
            $rules['fat_department'] = 'required|string|max:100';
            $rules['other_department'] = 'nullable|required_if:fat_department,LAINNYA|string|max:100';
            // Per-item classification: asset or non-asset
            $rules['fat_item_types'] = 'required|array';
            $rules['fat_item_types.*'] = 'required|in:asset,non_asset';
        }
        
        $request->validate($rules);

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
        $approvalData = [
            'approved' => true,
            'approved_at' => Carbon::now('Asia/Jakarta')->toISOString(),
            'approved_by' => $user->id,
            'approved_by_name' => $user->name,
            'approved_by_divisi' => $user->divisi,
            'approved_by_level' => $user->level,
            'notes' => $request->notes
        ];
        
        // Add FAT-specific data if this is FAT approval
        if ($isFATApproval) {
            $fatDepartment = $request->fat_department;
            if ($fatDepartment === 'LAINNYA') {
                $fatDepartment = $request->other_department;
            }
            
            $approvalData['fat_department'] = $fatDepartment;
            // Persist per-item asset flags
            $itemTypes = $request->input('fat_item_types', []);
            $anyAsset = false;
            foreach ($purchaseRequest->items as $item) {
                if (isset($itemTypes[$item->id])) {
                    $isAsset = $itemTypes[$item->id] === 'asset';
                    $item->update(['is_asset' => $isAsset]);
                    if ($isAsset) $anyAsset = true;
                }
            }
            // Set PR-level flag for display: if any item asset, mark asset, else cost
            $approvalData['fat_approval_type'] = $anyAsset ? 'asset' : 'cost';
        }
        
        $approvals[$currentApprovalLevel] = $approvalData;

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

        $updateData = [
            'approvals' => $approvals,
            'status' => $newStatus
        ];
        
        // Add FAT fields to PR record if this is FAT approval
        if ($isFATApproval) {
            $fatDepartment = $request->fat_department;
            if ($fatDepartment === 'LAINNYA') {
                $fatDepartment = $request->other_department;
            }
            
            $updateData['fat_department'] = $fatDepartment;
            // If any item is asset, set asset; else cost
            $anyAsset = $purchaseRequest->items()->where('is_asset', true)->exists();
            $updateData['fat_approval_type'] = $anyAsset ? 'asset' : 'cost';
        }

        $purchaseRequest->update($updateData);

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
            'update_type' => 'required|in:ITEMS_PROCESSED,VENDOR_SEARCH,PRICE_COMPARISON,PO_CREATED,GOODS_RECEIVED,GOODS_RETURNED,COMPLAIN,CLOSED',
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
            // Calculate total estimated price (quantity × unit price for each item)
            $totalEstimatedPrice = collect($request->items)->sum(function($item) {
                $quantity = (float)($item['quantity'] ?? 0);
                $unitPrice = (float)($item['estimated_price'] ?? 0);
                return $quantity * $unitPrice;
            });

            // Update Purchase Request
            $purchaseRequest->update([
                'due_date' => $request->due_date,
                'description' => $request->description,
                'location_id' => $request->location_id,
                'category_id' => $request->category_id,
                'notes' => $request->notes,
                'total_estimated_price' => $totalEstimatedPrice
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

    public function bulkUpdateItemStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:purchase_request_items,id',
            'item_status' => 'required|in:PENDING,VENDOR_SEARCH,PRICE_COMPARISON,PO_CREATED,GOODS_RECEIVED,GOODS_RETURNED,COMPLAIN,TERSEDIA_DI_GA,CLOSED',
            'purchasing_notes' => 'nullable|string|max:1000',
            'payment_method_id' => 'nullable|exists:payment_methods,id'
        ]);

        $user = Auth::user();
        
        // Only purchasing team can update item status (except for items with status "TERSEDIA_DI_GA")
        if (!($user->level === 'admin' || 
              ($user->divisi === 'PURCHASING' && in_array($user->level, ['manager', 'spv', 'staff'])))) {
            return back()->with('error', 'Hanya tim Purchasing yang dapat mengupdate status item.');
        }

        // Check if PR is fully approved
        if (!$purchaseRequest->isFullyApproved() || $purchaseRequest->status !== 'APPROVED') {
            return back()->with('error', 'PR harus fully approved sebelum dapat mengupdate status item.');
        }

        DB::beginTransaction();
        try {
            // Get items that can be updated (exclude completed/final status items)
            $items = PurchaseRequestItem::where('purchase_request_id', $purchaseRequest->id)
                ->whereIn('id', $request->item_ids)
                ->whereNotIn('item_status', ['TERSEDIA_DI_GA', 'CLOSED', 'GOODS_RECEIVED', 'REJECTED'])
                ->get();

            if ($items->isEmpty()) {
                return back()->with('error', 'Tidak ada item yang bisa diupdate. Item yang dipilih mungkin sudah selesai, tersedia di GA, atau sudah final.');
            }

            // If marking as GOODS_RECEIVED, require a valid active payment method
            $selectedPaymentMethodId = null;
            if ($request->item_status === 'GOODS_RECEIVED') {
                $request->validate([
                    'payment_method_id' => 'required|exists:payment_methods,id'
                ]);
                $selectedPaymentMethodId = (int) $request->payment_method_id;
            }

            // Update selected items
            foreach ($items as $item) {
                $update = [
                    'item_status' => $request->item_status,
                    'purchasing_notes' => $request->purchasing_notes
                ];
                if ($selectedPaymentMethodId) {
                    $update['payment_method_id'] = $selectedPaymentMethodId;
                }
                $item->update($update);
            }

            // Create detailed description for bulk update
            $itemDescriptions = $items->map(function($item) {
                return "• {$item->description} (Qty: {$item->quantity})";
            })->join("\n");
            
            $statusLabel = PurchaseRequestItem::getItemStatusLabels()[$request->item_status] ?? $request->item_status;
            
            $description = "Bulk update status menjadi \"{$statusLabel}\" untuk " . count($items) . " item:\n" . $itemDescriptions;
            if ($selectedPaymentMethodId) {
                $pmName = optional(\App\Models\Access_PR\PaymentMethod::find($selectedPaymentMethodId))->name;
                if ($pmName) {
                    $description .= "\n\nPayment Method: {$pmName}";
                }
            }
            if ($request->purchasing_notes) {
                $description .= "\n\nCatatan Purchasing: " . $request->purchasing_notes;
            }

            // Add status update log
            PurchaseRequestStatusUpdate::create([
                'purchase_request_id' => $purchaseRequest->id,
                'update_type' => $request->item_status,
                'description' => $description,
                'data' => [
                    'item_ids' => $items->pluck('id')->toArray(),
                    'item_descriptions' => $items->pluck('description')->toArray(),
                    'purchasing_notes' => $request->purchasing_notes,
                    'bulk_update' => true,
                    'updated_items_count' => count($items),
                    'payment_method_id' => $selectedPaymentMethodId
                ],
                'updated_by' => $user->id
            ]);

            // Check if PR should be auto-completed
            if ($purchaseRequest->shouldAutoComplete()) {
                $purchaseRequest->update(['status' => 'COMPLETED']);
                
                // Add status update record for PR completion
                PurchaseRequestStatusUpdate::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'update_type' => 'CLOSED',
                    'description' => 'Purchase Request otomatis diselesaikan karena semua item telah selesai diproses atau tersedia di GA',
                    'updated_by' => $user->id
                ]);
            }

            DB::commit();
            
            // Refresh the purchase request to get updated items
            $purchaseRequest->refresh();
            $purchaseRequest->load(['items', 'statusUpdates']);
            
            return back()->with('success', 'Status item berhasil diupdate untuk ' . count($items) . ' item.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengupdate status item: ' . $e->getMessage());
        }
    }

    public function gaApproveWithItemSelection(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
            'available_items' => 'nullable|array',
            'available_items.*' => 'exists:purchase_request_items,id',
            'available_quantities' => 'nullable|array',
            'available_quantities.*' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();
        
        // Check if user is from GA and can approve
        if (!($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff']))) {
            return back()->with('error', 'Hanya tim GA yang dapat melakukan approval ini.');
        }

        // Check if this is GA approval level
        $currentApprovalLevel = $purchaseRequest->getCurrentApprovalLevel();
        if ($currentApprovalLevel !== 'ga') {
            return back()->with('error', 'Approval level saat ini bukan GA.');
        }

        DB::beginTransaction();
        try {
            $approvals = $purchaseRequest->approvals ?? [];
            
            // Handle item selection if any items are available at GA
            if (!empty($request->available_items)) {
                $availableItems = PurchaseRequestItem::whereIn('id', $request->available_items)
                    ->where('purchase_request_id', $purchaseRequest->id)
                    ->get();

                $availableQuantities = $request->available_quantities ?? [];
                $splitItemsInfo = [];

                foreach ($availableItems as $item) {
                    $availableQty = $availableQuantities[$item->id] ?? $item->quantity;
                    $availableQty = min($availableQty, $item->quantity); // Ensure not exceeding original quantity
                    
                    if ($availableQty == $item->quantity) {
                        // Full quantity available - just update status
                        $item->update([
                            'item_status' => 'TERSEDIA_DI_GA',
                            'purchasing_notes' => 'Barang tersedia di GA pada approval ' . now()->format('d/m/Y H:i')
                        ]);
                        
                        $splitItemsInfo[] = [
                            'description' => $item->description,  
                            'available_qty' => $availableQty,
                            'total_qty' => $item->quantity,
                            'type' => 'full'
                        ];
                    } else {
                        // Partial quantity - split the item
                        $remainingQty = $item->quantity - $availableQty;
                        
                        // Update original item to available quantity and mark as available
                        $item->update([
                            'quantity' => $availableQty,
                            'item_status' => 'TERSEDIA_DI_GA',
                            'purchasing_notes' => 'Sebagian barang tersedia di GA (' . $availableQty . ' dari ' . ($availableQty + $remainingQty) . ') pada ' . now()->format('d/m/Y H:i')
                        ]);
                        
                        // Create new item for remaining quantity (propagate is_asset)
                        PurchaseRequestItem::create([
                            'purchase_request_id' => $purchaseRequest->id,
                            'description' => $item->description,
                            'quantity' => $remainingQty,
                            'unit' => $item->unit,
                            'estimated_price' => $item->estimated_price,
                            'notes' => $item->notes . ' - Split dari item original',
                            'item_status' => 'PENDING',
                            'purchasing_notes' => 'Sisa dari split item untuk melanjutkan proses PR',
                            'is_asset' => $item->is_asset,
                        ]);
                        
                        $splitItemsInfo[] = [
                            'description' => $item->description,
                            'available_qty' => $availableQty,
                            'remaining_qty' => $remainingQty,
                            'total_qty' => $availableQty + $remainingQty,
                            'type' => 'partial'
                        ];
                    }
                }

                // Check if all items are now available at GA
                $allItemsAvailable = $purchaseRequest->items()
                    ->where('item_status', '!=', 'TERSEDIA_DI_GA')
                    ->count() === 0;

                if ($allItemsAvailable) {
                    // All items available - complete the approval with "tersedia_di_ga" level
                    $approvals['ga'] = [
                        'approved' => true,
                        'approved_at' => Carbon::now('Asia/Jakarta')->toISOString(),
                        'approved_by' => $user->id,
                        'approved_by_name' => $user->name,
                        'approved_by_divisi' => $user->divisi,
                        'approved_by_level' => $user->level,
                        'notes' => $request->notes . ' - Semua barang tersedia di GA'
                    ];

                    // Add "tersedia_di_ga" as the final approval level
                    $approvals['tersedia_di_ga'] = [
                        'approved' => true,
                        'approved_at' => Carbon::now('Asia/Jakarta')->toISOString(),
                        'approved_by' => $user->id,
                        'approved_by_name' => $user->name,
                        'approved_by_divisi' => $user->divisi,
                        'approved_by_level' => $user->level,
                        'notes' => 'Semua barang tersedia di GA'
                    ];

                    // Set the approval flow to only include approved levels plus "tersedia_di_ga"
                    $newApprovalFlow = ['tersedia_di_ga'];
                    
                    $purchaseRequest->update([
                        'approvals' => $approvals,
                        'status' => 'COMPLETED',
                        'approval_flow' => $newApprovalFlow
                    ]);

                    // Prepare success message with item details
                    $message = 'Semua barang tersedia di GA. PR status menjadi COMPLETED.';
                    if (!empty($splitItemsInfo)) {
                        $message .= ' Detail pemilihan: ';
                        foreach ($splitItemsInfo as $info) {
                            if ($info['type'] == 'full') {
                                $message .= $info['description'] . ' (Qty: ' . $info['available_qty'] . ' - Full), ';
                            } else {
                                $message .= $info['description'] . ' (Qty: ' . $info['available_qty'] . ' dari ' . $info['total_qty'] . ' - Partial), ';
                            }
                        }
                        $message = rtrim($message, ', ');
                    }

                    DB::commit();
                    return back()->with('success', $message);
                }
            }

            // Normal GA approval process
            $approvals['ga'] = [
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

            $newStatus = $allApproved ? 'APPROVED' : 'SUBMITTED';

            $purchaseRequest->update([
                'approvals' => $approvals,
                'status' => $newStatus
            ]);

            // Prepare success message with item details if items were selected
            $message = '';
            if (!empty($splitItemsInfo)) {
                $itemsMessage = ' Detail pemilihan: ';
                foreach ($splitItemsInfo as $info) {
                    if ($info['type'] == 'full') {
                        $itemsMessage .= $info['description'] . ' (Qty: ' . $info['available_qty'] . ' - Full), ';
                    } else {
                        $itemsMessage .= $info['description'] . ' (Qty: ' . $info['available_qty'] . ' dari ' . $info['total_qty'] . ' - Partial), ';
                    }
                }
                $itemsMessage = rtrim($itemsMessage, ', ');
            } else {
                $itemsMessage = '';
            }

            // Add status update record if items were processed
            if (!empty($splitItemsInfo)) {
                $itemsDescription = '';
                foreach ($splitItemsInfo as $info) {
                    if ($info['type'] == 'full') {
                        $itemsDescription .= 'Item: ' . $info['description'] . ' qty: ' . $info['available_qty'] . ' tersedia di GA (Full), ';
                    } else {
                        $itemsDescription .= 'Item: ' . $info['description'] . ' qty: ' . $info['available_qty'] . ' tersedia di GA, sisa qty: ' . $info['remaining_qty'] . ' dibuat list baru (Partial), ';
                    }
                }
                $itemsDescription = rtrim($itemsDescription, ', ');
                
                PurchaseRequestStatusUpdate::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'update_type' => 'TERSEDIA_DI_GA',
                    'updated_by' => $user->id,
                    'description' => $itemsDescription
                ]);
            }

            DB::commit();
            
            if ($allApproved) {
                $message = 'Purchase Request berhasil disetujui dan telah mencapai persetujuan final!' . $itemsMessage;
                return back()->with('success', $message);
            } else {
                $message = 'Purchase Request berhasil disetujui. Menunggu persetujuan dari level berikutnya.' . $itemsMessage;
                return back()->with('success', $message);
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal melakukan approval: ' . $e->getMessage());
        }
    }

    public function purchasingPartialApproval(Request $request, PurchaseRequest $purchaseRequest)
    {
    $user = Auth::user();
        
        // Validasi user adalah purchasing dengan logic yang sama seperti show method
        $canUpdateStatus = ($user->level === 'admin' || $user->level === 'ceo' || $user->level === 'cfo') || 
                          ($user->divisi === 'PURCHASING' && 
                           in_array($user->level, ['manager', 'spv', 'staff']) && 
                           $purchaseRequest->isFullyApproved() && 
                           $purchaseRequest->status === 'APPROVED');
                           
        if (!$canUpdateStatus) {
            abort(403, 'Unauthorized. Hanya divisi Purchasing yang dapat memproses PR ini.');
        }

        // Validasi PR status harus APPROVED
        if ($purchaseRequest->status !== 'APPROVED') {
            return back()->with('error', 'Purchase Request harus dalam status APPROVED untuk diproses.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $actions = $request->actions ?? [];
            $quantities = $request->quantities ?? [];
            $reasons = $request->reasons ?? [];
            $processedItemsInfo = [];
            $hasProcessedItems = false;

            foreach ($actions as $itemId => $action) {
                if (empty($action)) continue;

                $item = PurchaseRequestItem::where('id', $itemId)
                    ->where('purchase_request_id', $purchaseRequest->id)
                    ->whereNotIn('item_status', ['TERSEDIA_DI_GA', 'CLOSED', 'GOODS_RECEIVED', 'REJECTED'])
                    ->first();

                if (!$item) continue; // Skip if item not found or already in final status

                $hasProcessedItems = true;

                switch ($action) {
                    case 'approve':
                        // Setujui full quantity - mulai dengan status VENDOR_SEARCH
                        $item->update([
                            'item_status' => 'VENDOR_SEARCH',
                            'purchasing_notes' => 'Item disetujui purchasing dan mulai pencarian vendor pada ' . now()->format('d/m/Y H:i')
                        ]);
                        
                        $processedItemsInfo[] = [
                            'description' => $item->description,
                            'action' => 'approve',
                            'quantity' => $item->quantity,
                            'type' => 'full'
                        ];
                        break;

                    case 'partial':
                        // Proses sebagian quantity
                        $approvedQty = $quantities[$itemId] ?? 0;
                        $approvedQty = min($approvedQty, $item->quantity);
                        
                        if ($approvedQty <= 0) {
                            continue 2; // Skip item ini
                        }

                        if ($approvedQty == $item->quantity) {
                            // Ternyata full quantity
                            $item->update([
                                'item_status' => 'VENDOR_SEARCH',
                                'purchasing_notes' => 'Item disetujui purchasing dan mulai pencarian vendor pada ' . now()->format('d/m/Y H:i')
                            ]);
                            
                            $processedItemsInfo[] = [
                                'description' => $item->description,
                                'action' => 'approve',
                                'quantity' => $approvedQty,
                                'type' => 'full'
                            ];
                        } else {
                            // Partial quantity - split item
                            $remainingQty = $item->quantity - $approvedQty;
                            
                            // Update original item untuk approved quantity
                            $item->update([
                                'quantity' => $approvedQty,
                                'item_status' => 'VENDOR_SEARCH',
                                'purchasing_notes' => 'Sebagian item disetujui (' . $approvedQty . ' dari ' . ($approvedQty + $remainingQty) . ') dan mulai pencarian vendor pada ' . now()->format('d/m/Y H:i')
                            ]);
                            
                            // Create new item untuk remaining quantity (propagate is_asset)
                            PurchaseRequestItem::create([
                                'purchase_request_id' => $purchaseRequest->id,
                                'description' => $item->description,
                                'quantity' => $remainingQty,
                                'unit' => $item->unit,
                                'estimated_price' => $item->estimated_price,
                                'notes' => $item->notes . ' - Split dari item original',
                                'item_status' => 'PENDING',
                                'purchasing_notes' => 'Sisa quantity dari partial approval purchasing',
                                'is_asset' => $item->is_asset,
                            ]);
                            
                            $processedItemsInfo[] = [
                                'description' => $item->description,
                                'action' => 'partial',
                                'approved_qty' => $approvedQty,
                                'remaining_qty' => $remainingQty,
                                'total_qty' => $approvedQty + $remainingQty,
                                'type' => 'partial'
                            ];
                        }
                        break;

                    case 'reject':
                        // Tolak item
                        $reason = $reasons[$itemId] ?? 'Tidak ada alasan';
                        $item->update([
                            'item_status' => 'REJECTED',
                            'purchasing_notes' => 'Item ditolak: ' . $reason . ' (pada ' . now()->format('d/m/Y H:i') . ')'
                        ]);
                        
                        $processedItemsInfo[] = [
                            'description' => $item->description,
                            'action' => 'reject',
                            'quantity' => $item->quantity,
                            'reason' => $reason,
                            'type' => 'reject'
                        ];
                        break;
                }
            }

            if (!$hasProcessedItems) {
                DB::rollback();
                return back()->with('error', 'Tidak ada item yang dipilih untuk diproses.');
            }

            // Add status update record
            $itemsDescription = 'Purchasing memproses item dengan detail sebagai berikut: ';
            $itemDetails = [];
            
            foreach ($processedItemsInfo as $info) {
                if ($info['type'] == 'full') {
                    $itemDetails[] = '• ' . $info['description'] . ' - Qty: ' . $info['quantity'] . ' (' . ($info['action'] == 'approve' ? 'Diproses (Full)' : ucfirst($info['action'])) . ')';
                } elseif ($info['type'] == 'partial') {
                    $itemDetails[] = '• ' . $info['description'] . ' - Qty: ' . $info['approved_qty'] . ' dari ' . $info['total_qty'] . ' (Diproses Sebagian, sisa ' . $info['remaining_qty'] . ' tetap pending)';
                } else {
                    $itemDetails[] = '• ' . $info['description'] . ' - Qty: ' . $info['quantity'] . ' (Ditolak' . (isset($info['reason']) ? ': ' . $info['reason'] : '') . ')';
                }
            }
            
            $itemsDescription .= implode('; ', $itemDetails);
            
            // Add general purchasing notes if provided
            if ($request->purchasing_notes) {
                $itemsDescription .= '. Catatan Purchasing: ' . $request->purchasing_notes;
            }
            
            PurchaseRequestStatusUpdate::create([
                'purchase_request_id' => $purchaseRequest->id,
                'update_type' => 'ITEMS_PROCESSED',
                'updated_by' => $user->id,
                'description' => $itemsDescription
            ]);

            // Prepare success message
            $message = 'Purchasing telah memproses item terpilih. Detail: ';
            foreach ($processedItemsInfo as $info) {
                if ($info['type'] == 'full') {
                    $message .= $info['description'] . ' (' . ucfirst($info['action']) . ' - Qty: ' . $info['quantity'] . '), ';
                } elseif ($info['type'] == 'partial') {
                    $message .= $info['description'] . ' (Partial - Qty: ' . $info['approved_qty'] . ' dari ' . $info['total_qty'] . '), ';
                } else {
                    $message .= $info['description'] . ' (Ditolak), ';
                }
            }
            $message = rtrim($message, ', ');

            // Check if PR should be auto-completed after processing
            if ($purchaseRequest->shouldAutoComplete()) {
                $purchaseRequest->update(['status' => 'COMPLETED']);
                
                // Add status update record for PR completion
                PurchaseRequestStatusUpdate::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'update_type' => 'CLOSED',
                    'description' => 'Purchase Request otomatis diselesaikan karena semua item telah selesai diproses atau tersedia di GA',
                    'updated_by' => $user->id
                ]);
                
                $message .= ' PR telah otomatis diselesaikan karena semua item telah diproses.';
            }

            DB::commit();
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses purchasing: ' . $e->getMessage());
        }
    }

    // GA assigns asset numbers per item with auto-incrementing sequences
    public function assignAssetNumbers(Request $request, PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if (!($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff']))) {
            return back()->with('error', 'Hanya tim GA yang dapat menginput nomor asset.');
        }

        if (!$purchaseRequest->areAllItemsCompleted()) {
            return back()->with('error', 'Nomor asset hanya dapat diinput setelah purchasing selesai.');
        }

        // Validate payload: base_code per item id (optional; GA can choose any items)
        $request->validate([
            'asset_bases' => 'nullable|array',
            'asset_bases.*' => 'nullable|string|max:20|regex:/^[A-Za-z0-9\-_]+$/',
        ], [
            'asset_bases.*.regex' => 'Kode dasar asset hanya boleh huruf, angka, dash (-), underscore (_).',
        ]);

        $baseInputs = $request->input('asset_bases', []);
        if (empty($baseInputs)) {
            return back()->with('info', 'Tidak ada nomor asset yang diinput.');
        }

        // Fetch only items that GA provided base codes for (no longer limited to FAT asset selection)
        $itemIds = array_keys($baseInputs);
        $items = $purchaseRequest->items()->whereIn('id', $itemIds)->get();

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $qty = (int) $item->quantity;
                if ($qty <= 0) continue;

                $base = $request->input("asset_bases.".$item->id);
                if (!$base) continue; // skip if GA didn't provide base

                $base = strtoupper($base);

                // Determine how many assets already generated for this item (any base)
                $existingForItem = \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::where('purchase_request_item_id', $item->id)->count();

                // Determine starting sequence globally across all PRs for this base code
                $last = \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::where('base_code', $base)
                    ->orderByDesc('sequence_no')
                    ->first();
                $startSeq = $last ? ($last->sequence_no + 1) : 1;

                // Optionally convert GA decision from Non-Asset GA to Asset GA upon generate (confirmed from UI)
                if ($request->boolean("convert_non_asset_ga.".$item->id)) {
                    $item->update(['is_asset_hcga' => true]);
                }

                // Generate only the remaining quantities for this item
                $toGenerate = max(0, $qty - $existingForItem);
                if ($toGenerate === 0) { continue; }

                // Generate asset codes for remaining units: BASE-001, BASE-002, ...
                for ($i = 0; $i < $toGenerate; $i++) {
                    $seq = $startSeq + $i;
                    $assetCode = sprintf('%s-%03d', $base, $seq);

                    \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::create([
                        'purchase_request_id' => $purchaseRequest->id,
                        'purchase_request_item_id' => $item->id,
                        'item_description' => $item->description,
                        'base_code' => $base,
                        'asset_code' => $assetCode,
                        'asset_pajak' => (bool) ($item->is_asset ?? false),
                        'sequence_no' => $seq,
                        'created_by' => $user->id,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Nomor asset berhasil direkam.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate nomor asset: ' . $e->getMessage());
        }
    }

    public function updateAssetNumber(Request $request, $id)
    {
        try {
            $purchaseRequest = PurchaseRequest::findOrFail($id);
            
            // Authorization: only GA users can update asset numbers
            if (Auth::user()->divisi !== 'HCGA') {
                return back()->with('error', 'Hanya GA yang dapat mengelola Asset Number.');
            }
            
            // Validation: PR must be asset type and purchasing complete
            if ($purchaseRequest->fat_approval_type !== 'asset') {
                return back()->with('error', 'Asset Number hanya dapat ditetapkan untuk item dengan tipe Asset.');
            }
            
            if (!$purchaseRequest->areAllItemsCompleted()) {
                return back()->with('error', 'Asset Number hanya dapat ditetapkan setelah purchasing selesai.');
            }
            
            // Validate asset number format and uniqueness
            $request->validate([
                'asset_number' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:purchase_requests,asset_number,' . $id,
                    'regex:/^[A-Z0-9\-_]+$/' // Only uppercase letters, numbers, hyphens, underscores
                ]
            ], [
                'asset_number.required' => 'Asset Number wajib diisi.',
                'asset_number.unique' => 'Asset Number sudah digunakan untuk PR lain.',
                'asset_number.regex' => 'Asset Number hanya boleh menggunakan huruf besar, angka, tanda hubung (-) dan underscore (_).',
                'asset_number.max' => 'Asset Number tidak boleh lebih dari 50 karakter.'
            ]);
            
            // Update asset number
            $purchaseRequest->update([
                'asset_number' => strtoupper($request->asset_number)
            ]);
            
            // Log the asset number assignment
            PurchaseRequestStatusUpdate::create([
                'purchase_request_id' => $purchaseRequest->id,
                'update_type' => 'ASSET_NUMBER_ASSIGNED',
                'description' => 'Asset Number ditetapkan: ' . $purchaseRequest->asset_number,
                'updated_by' => Auth::id()
            ]);
            
            return back()->with('success', 'Asset Number berhasil ditetapkan: ' . $purchaseRequest->asset_number);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menetapkan Asset Number: ' . $e->getMessage());
        }
    }
}
