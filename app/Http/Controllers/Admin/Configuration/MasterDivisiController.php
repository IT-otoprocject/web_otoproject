<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\MasterDivisi;
use Illuminate\Http\Request;

class MasterDivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterDivisi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $divisis = $query->orderBy('nama')->paginate(10)->withQueryString();
        
        return view('admin.configuration.divisi.index', compact('divisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.configuration.divisi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_divisi,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        MasterDivisi::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.configuration.divisi.index')
            ->with('success', 'Master Divisi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterDivisi $divisi)
    {
        $usersCount = $divisi->getUsersCount();
        return view('admin.configuration.divisi.show', compact('divisi', 'usersCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterDivisi $divisi)
    {
        return view('admin.configuration.divisi.edit', compact('divisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterDivisi $divisi)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_divisi,kode,' . $divisi->id,
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $divisi->update([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.configuration.divisi.index')
            ->with('success', 'Master Divisi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterDivisi $divisi)
    {
        // Cek apakah divisi sedang digunakan oleh user
        if ($divisi->isUsedByUsers()) {
            return redirect()->route('admin.configuration.divisi.index')
                ->with('error', 'Master Divisi tidak dapat dihapus karena sedang digunakan oleh ' . $divisi->getUsersCount() . ' user!');
        }

        $divisi->delete();

        return redirect()->route('admin.configuration.divisi.index')
            ->with('success', 'Master Divisi berhasil dihapus!');
    }
}
