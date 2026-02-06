<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\MasterUserLevel;
use Illuminate\Http\Request;

class MasterUserLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterUserLevel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $userLevels = $query->orderBy('nama')->paginate(10)->withQueryString();
        
        return view('admin.configuration.user-level.index', compact('userLevels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.configuration.user-level.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_user_level,kode',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        MasterUserLevel::create([
            'kode' => strtolower($request->kode),
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.configuration.user-level.index')
            ->with('success', 'Master User Level berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterUserLevel $userLevel)
    {
        $usersCount = $userLevel->getUsersCount();
        return view('admin.configuration.user-level.show', compact('userLevel', 'usersCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterUserLevel $userLevel)
    {
        return view('admin.configuration.user-level.edit', compact('userLevel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterUserLevel $userLevel)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:master_user_level,kode,' . $userLevel->id,
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $userLevel->update([
            'kode' => strtolower($request->kode),
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.configuration.user-level.index')
            ->with('success', 'Master User Level berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterUserLevel $userLevel)
    {
        // Cek apakah level sedang digunakan oleh user
        if ($userLevel->isUsedByUsers()) {
            return redirect()->route('admin.configuration.user-level.index')
                ->with('error', 'Master User Level tidak dapat dihapus karena sedang digunakan oleh ' . $userLevel->getUsersCount() . ' user!');
        }

        $userLevel->delete();

        return redirect()->route('admin.configuration.user-level.index')
            ->with('success', 'Master User Level berhasil dihapus!');
    }
}
