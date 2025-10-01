<?php

namespace App\Http\Controllers\Access_PR;

use App\Http\Controllers\Controller;
use App\Models\MasterLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterLocationController extends Controller
{
    public function __construct()
    {
        // Apply system access middleware for master location
        $this->middleware('system_access:master_location');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterLocation::with(['createdBy', 'updatedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('company', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $masterLocations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('Access_PR.master_locations.index', compact('masterLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Access_PR.master_locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:master_locations,code',
            'company' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            MasterLocation::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'company' => $request->company,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('master-locations.index')
                ->with('success', 'Master Lokasi berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal membuat master lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterLocation $masterLocation)
    {
        $masterLocation->load(['createdBy', 'updatedBy', 'purchaseRequests']);
        return view('Access_PR.master_locations.show', compact('masterLocation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterLocation $masterLocation)
    {
        return view('Access_PR.master_locations.edit', compact('masterLocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterLocation $masterLocation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('master_locations', 'code')->ignore($masterLocation->id)
            ],
            'company' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $masterLocation->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'company' => $request->company,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('master-locations.index')
                ->with('success', 'Master Lokasi berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal mengupdate master lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterLocation $masterLocation)
    {
        // Check if location is being used by any purchase requests
        if ($masterLocation->purchaseRequests()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak dapat dihapus karena masih digunakan oleh Purchase Request.'
            ]);
        }

        try {
            $masterLocation->delete();
            return response()->json([
                'success' => true,
                'message' => 'Master Lokasi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus master lokasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(MasterLocation $masterLocation)
    {
        try {
            $masterLocation->update([
                'is_active' => !$masterLocation->is_active,
                'updated_by' => Auth::id(),
            ]);

            $status = $masterLocation->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return response()->json([
                'success' => true,
                'message' => "Master Lokasi berhasil {$status}.",
                'is_active' => $masterLocation->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get locations for API (used in forms)
     */
    public function getLocations()
    {
        return response()->json([
            'success' => true,
            'data' => MasterLocation::getActiveLocations()
        ]);
    }
}
