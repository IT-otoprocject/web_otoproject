<?php

namespace App\Http\Controllers\Access_PR;

use App\Http\Controllers\Controller;
use App\Models\Access_PR\PrCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrCategoryController extends Controller
{
    public function __construct()
    {
        // Apply system access middleware for PR categories
        $this->middleware('system_access:pr_categories');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = PrCategory::with(['createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('Access_PR.PrCategory.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $approvalLevels = PrCategory::getAvailableApprovalLevels();
        return view('Access_PR.PrCategory.create', compact('approvalLevels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:pr_categories,name',
            'description' => 'nullable|string|max:1000',
            'approval_rules' => 'required|array|min:1',
            'approval_rules.*' => 'required|string|in:dept_head,ga,finance_dept,ceo,cfo',
        ]);

        DB::beginTransaction();
        try {
            PrCategory::create([
                'name' => $request->name,
                'description' => $request->description,
                'approval_rules' => array_values($request->approval_rules), // Reindex array
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('pr-categories.index')
                ->with('success', 'Kategori PR berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal membuat kategori PR: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PrCategory $prCategory)
    {
        $prCategory->load(['createdBy', 'updatedBy']);
        $approvalLevels = PrCategory::getAvailableApprovalLevels();
        
        return view('Access_PR.PrCategory.show', compact('prCategory', 'approvalLevels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrCategory $prCategory)
    {
        $approvalLevels = PrCategory::getAvailableApprovalLevels();
        return view('Access_PR.PrCategory.edit', compact('prCategory', 'approvalLevels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrCategory $prCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:pr_categories,name,' . $prCategory->id,
            'description' => 'nullable|string|max:1000',
            'approval_rules' => 'required|array|min:1',
            'approval_rules.*' => 'required|string|in:dept_head,ga,finance_dept,ceo,cfo',
        ]);

        DB::beginTransaction();
        try {
            $prCategory->update([
                'name' => $request->name,
                'description' => $request->description,
                'approval_rules' => array_values($request->approval_rules), // Reindex array
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('pr-categories.index')
                ->with('success', 'Kategori PR berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal mengupdate kategori PR: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrCategory $prCategory)
    {
        // Check if category is being used by any purchase requests
        if ($prCategory->purchaseRequests()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh Purchase Request.');
        }

        $prCategory->delete();
        return redirect()->route('pr-categories.index')
            ->with('success', 'Kategori PR berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(PrCategory $prCategory)
    {
        $prCategory->update([
            'is_active' => !$prCategory->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $prCategory->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori PR berhasil {$status}.");
    }
}
