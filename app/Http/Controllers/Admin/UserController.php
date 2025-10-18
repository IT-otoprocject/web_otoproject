<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $availableModules = $this->getAvailableModules();
        return view('admin.users.create', compact('availableModules'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'level' => 'required|string',
            'garage' => 'nullable|string',
            'system_access' => 'nullable|array',
        ];

        // Divisi wajib kecuali untuk admin, CEO, dan CFO
        if (!in_array($request->level, ['admin', 'ceo', 'cfo'])) {
            $rules['divisi'] = 'required|string';
        } else {
            $rules['divisi'] = 'nullable|string';
        }

        $request->validate($rules);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'divisi' => $request->divisi,
            'garage' => $request->garage,
            'system_access' => $request->system_access ?? [],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $availableModules = $this->getAvailableModules();
        return view('admin.users.show', compact('user', 'availableModules'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $availableModules = $this->getAvailableModules();
        return view('admin.users.edit', compact('user', 'availableModules'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'level' => 'required|string',
            'garage' => 'nullable|string',
            'system_access' => 'nullable|array',
        ];

        // Divisi wajib kecuali untuk admin, CEO, dan CFO
        if (!in_array($request->level, ['admin', 'ceo', 'cfo'])) {
            $rules['divisi'] = 'required|string';
        } else {
            $rules['divisi'] = 'nullable|string';
        }

        // Add password validation if changing password
        if ($request->has('change_password') && $request->change_password) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'level' => $request->level,
            'divisi' => $request->divisi,
            'garage' => $request->garage,
            'system_access' => $request->system_access ?? [],
        ];

        // Update password if changing
        if ($request->has('change_password') && $request->change_password && $request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    /**
     * Get available system modules
     */
    private function getAvailableModules()
    {
        return [
            'dashboard' => 'Dashboard',
            'user_management' => 'User Management',
            'pr' => 'Purchase Request',
            'pr_categories' => 'Rules Kategori PR',
            'master_location' => 'Master Location',
            'payment_method' => 'Payment Methods (PR)',
            'spk_management' => 'SPK Management', 
            'inventory' => 'Inventory',
            'reports' => 'Reports',
            'settings' => 'Settings'
        ];
    }
}