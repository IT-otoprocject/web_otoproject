<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\MasterGarage;
use Illuminate\Http\Request;

class MasterGarageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterGarage::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $garages = $query->orderBy('nama')->paginate(10)->withQueryString();
        
        return view('admin.configuration.garage.index', compact('garages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.configuration.garage.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_garage,kode',
            'nama' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        MasterGarage::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.configuration.garage.index')
            ->with('success', 'Master Garage berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterGarage $garage)
    {
        $usersCount = $garage->getUsersCount();
        return view('admin.configuration.garage.show', compact('garage', 'usersCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterGarage $garage)
    {
        return view('admin.configuration.garage.edit', compact('garage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterGarage $garage)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_garage,kode,' . $garage->id,
            'nama' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $garage->update([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.configuration.garage.index')
            ->with('success', 'Master Garage berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterGarage $garage)
    {
        // Cek apakah garage sedang digunakan oleh user
        if ($garage->isUsedByUsers()) {
            return redirect()->route('admin.configuration.garage.index')
                ->with('error', 'Master Garage tidak dapat dihapus karena sedang digunakan oleh ' . $garage->getUsersCount() . ' user!');
        }

        $garage->delete();

        return redirect()->route('admin.configuration.garage.index')
            ->with('success', 'Master Garage berhasil dihapus!');
    }
}
