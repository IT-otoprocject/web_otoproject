<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Configuration\MasterDivisi;
use App\Models\Configuration\MasterUserLevel;
use App\Models\Configuration\MasterGarage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Exports\UsersTemplate;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('level', 'like', "%{$search}%")
                    ->orWhere('divisi', 'like', "%{$search}%")
                    ->orWhere('garage', 'like', "%{$search}%");
            });
        }

        // Email domain filter
        if ($request->filled('email_filter')) {
            if ($request->email_filter === 'otoproject') {
                $query->where('email', 'like', '%@otoproject.id%');
            } elseif ($request->email_filter === 'external') {
                $query->where('email', 'not like', '%@otoproject.id%');
            }
        }

        $users = $query->paginate(10)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $availableModules = $this->getAvailableModules();
        $masterDivisi = MasterDivisi::active()->orderBy('nama')->get();
        $masterLevels = MasterUserLevel::active()->orderBy('nama')->get();
        $masterGarages = MasterGarage::active()->orderBy('nama')->get();
        
        return view('admin.users.create', compact('availableModules', 'masterDivisi', 'masterLevels', 'masterGarages'));
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
        $masterDivisi = MasterDivisi::active()->orderBy('nama')->get();
        $masterLevels = MasterUserLevel::active()->orderBy('nama')->get();
        $masterGarages = MasterGarage::active()->orderBy('nama')->get();
        
        return view('admin.users.edit', compact('user', 'availableModules', 'masterDivisi', 'masterLevels', 'masterGarages'));
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
     * Reset user's password to a new value (random if not provided via request)
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'nullable|string|min:8',
        ]);

        $newPassword = $request->input('password') ?: str()->random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', 'Password user berhasil direset.');
    }

    /**
     * Download Excel template for importing users
     */
    public function downloadTemplate()
    {
        Log::info('downloadTemplate called');
        return Excel::download(new UsersTemplate(), 'users_import_template.xlsx');
    }

    /** Show import page */
    public function importPage()
    {
        Log::info('importPage called');
        return view('admin.users.import');
    }

    /** Import test (dry run) */
    public function importTest(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'update_existing' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->with('error', 'Validasi file gagal.');
        }

        $allowedModules = array_keys($this->getAvailableModules());
        
        // Clean up old files from previous session
        if (session('import_file_path')) {
            $oldPath = storage_path('app/'.session('import_file_path'));
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        
        // Clean up old temporary files (older than 1 hour)
        $this->cleanupOldTempFiles();
        
        try {
            // Get uploaded file
            $uploaded = $request->file('file');
            $originalName = $uploaded->getClientOriginalName();
            $timestamp = now()->format('YmdHis');
            $uniqueName = $timestamp . '_' . uniqid() . '_' . $originalName;
            
            // Ensure tmp directory exists and is writable
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) {
                if (!mkdir($tmpDir, 0755, true)) {
                    Log::error('Cannot create tmp directory', ['path' => $tmpDir]);
                    return back()->with('error', 'Tidak dapat membuat direktori temporary. Silakan hubungi administrator.');
                }
            }
            
            if (!is_writable($tmpDir)) {
                Log::error('Tmp directory is not writable', ['path' => $tmpDir, 'permissions' => substr(sprintf('%o', fileperms($tmpDir)), -4)]);
                return back()->with('error', 'Direktori temporary tidak dapat ditulis. Silakan hubungi administrator.');
            }
            
            // Try multiple storage methods
            $path = null;
            $fullPath = null;
            
            // Method 1: Try storeAs
            try {
                $path = $uploaded->storeAs('tmp', $uniqueName);
                if ($path) {
                    $fullPath = storage_path('app/'.$path);
                }
            } catch (\Exception $e) {
                Log::warning('storeAs method failed', ['error' => $e->getMessage()]);
            }
            
            // Method 2: Manual file move if storeAs failed
            if (!$path || !file_exists($fullPath)) {
                $fullPath = $tmpDir . DIRECTORY_SEPARATOR . $uniqueName;
                try {
                    if ($uploaded->move($tmpDir, $uniqueName)) {
                        $path = 'tmp/' . $uniqueName;
                        Log::info('File stored using move method', ['path' => $fullPath]);
                    }
                } catch (\Exception $e) {
                    Log::warning('move method failed', ['error' => $e->getMessage()]);
                }
            }
            
            // Final check
            if (!$path || !$fullPath || !file_exists($fullPath)) {
                Log::error('All file storage methods failed', [
                    'path' => $path,
                    'fullPath' => $fullPath,
                    'file_exists' => $fullPath ? file_exists($fullPath) : false,
                    'tmp_dir' => $tmpDir,
                    'tmp_dir_exists' => is_dir($tmpDir),
                    'tmp_dir_writable' => is_writable($tmpDir)
                ]);
                return back()->with('error', 'Gagal menyimpan file. Silakan coba lagi atau hubungi administrator.');
            }
            
            $fileSize = filesize($fullPath);
            if ($fileSize === 0) {
                Log::error('Stored file is empty', ['path' => $fullPath]);
                unlink($fullPath); // Clean up empty file
                return back()->with('error', 'File kosong atau rusak. Silakan upload file yang berisi data.');
            }
            
            // Test file readability
            if (!is_readable($fullPath)) {
                Log::error('File is not readable', ['path' => $fullPath]);
                unlink($fullPath);
                return back()->with('error', 'File tidak dapat dibaca. Silakan coba lagi.');
            }
            
            // Run dry-run test
            Log::info('Starting test import', [
                'file_path' => $fullPath, 
                'file_exists' => file_exists($fullPath),
                'file_size' => $fileSize
            ]);
            
            $import = new UsersImport((bool)$request->boolean('update_existing', true), true, $allowedModules);
            Excel::import($import, $fullPath);
            $result = $import->getResult();
            Log::info('Test import completed', ['result' => $result]);

            // Store file path in session for later use in importRun
            // Store the relative path from storage/app/
            $relativePath = str_replace(storage_path('app/'), '', $fullPath);
            session(['import_file_path' => $relativePath]);
            
            return back()->with('test_result', $result)->with('success', 'Test import berhasil. File siap untuk diimport.');
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::error('Excel validation error in test', ['error' => $e->getMessage()]);
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return back()->withErrors(['file' => 'Error validasi Excel'])
                ->with('error', 'Ditemukan error dalam file Excel:')
                ->with('excel_errors', $errors);
                
        } catch (\Exception $e) {
            Log::error('Test import failed with exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Clean up file on error
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            $errorMessage = 'Test import gagal: ' . $e->getMessage();
            
            // Provide more specific error messages
            if (strpos($e->getMessage(), 'Invalid file format') !== false) {
                $errorMessage = 'Format file tidak valid. Pastikan file adalah Excel (.xlsx, .xls) atau CSV yang benar.';
            } elseif (strpos($e->getMessage(), 'corrupted') !== false) {
                $errorMessage = 'File Excel rusak atau tidak dapat dibaca. Silakan buat file baru.';
            }
            
            return back()->withErrors(['file' => $errorMessage])
                ->with('error', 'Terjadi kesalahan saat test import. Silakan periksa file Excel Anda.');
        }
    }

    /** Import run (commit changes) */
    public function importRun(Request $request)
    {
        // Debug: Log current session state
        Log::info('Import run started', [
            'has_file_upload' => $request->hasFile('file'),
            'session_file_path' => session('import_file_path'),
            'session_exists' => session()->has('import_file_path')
        ]);
        
        // Make file upload optional if we have file from test
        $rules = [
            'update_existing' => 'nullable|boolean',
        ];
        
        // Only require file if no session file exists
        if (!session('import_file_path') || !file_exists(storage_path('app/'.session('import_file_path')))) {
            $rules['file'] = 'required|file|mimes:xlsx,xls,csv';
        } else {
            $rules['file'] = 'nullable|file|mimes:xlsx,xls,csv';
        }
        
        $request->validate($rules);

        $allowedModules = array_keys($this->getAvailableModules());
        
        // Handle file - either from new upload or from session (if test was run first)
        $fullPath = null;
        $cleanupFile = false;
        $fileSource = 'unknown';
        
        if ($request->hasFile('file')) {
            // New file uploaded - use this instead of session file
            $uploaded = $request->file('file');
            
            // Create unique filename
            $fileName = date('YmdHis') . '_' . uniqid() . '_' . $uploaded->getClientOriginalName();
            $fullPath = storage_path('app/tmp/' . $fileName);
            
            // Ensure tmp directory exists
            if (!file_exists(storage_path('app/tmp'))) {
                mkdir(storage_path('app/tmp'), 0755, true);
            }
            
            // Move file using move method for better control
            $uploaded->move(storage_path('app/tmp'), $fileName);
            
            $cleanupFile = true;
            $fileSource = 'new_upload';
            Log::info('Using newly uploaded file for import run', [
                'path' => $fullPath,
                'file_exists' => file_exists($fullPath),
                'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0
            ]);
        } else if (session('import_file_path')) {
            // Use file from previous test
            $sessionPath = session('import_file_path');
            
            // Handle both absolute and relative paths
            if (strpos($sessionPath, storage_path('app')) === 0) {
                // Already absolute path
                $fullPath = $sessionPath;
            } else {
                // Relative path, make it absolute
                $fullPath = storage_path('app/' . $sessionPath);
            }
            
            $fileSource = 'session';
            Log::info('Attempting to use session file for import run', [
                'session_path' => $sessionPath, 
                'full_path' => $fullPath, 
                'file_exists' => file_exists($fullPath),
                'is_readable' => file_exists($fullPath) ? is_readable($fullPath) : false
            ]);
            
            if (!file_exists($fullPath)) {
                Log::error('Session file not found', ['path' => $fullPath, 'session_path' => $sessionPath]);
                // Clear invalid session
                session()->forget('import_file_path');
                return back()->with('error', 'File tidak ditemukan. Silakan upload file lagi.')
                    ->withErrors(['file' => 'File dari test import sudah tidak ada. Silakan upload dan test ulang.']);
            }
            
            // Additional checks for session file
            if (!is_readable($fullPath)) {
                Log::error('Session file not readable', ['path' => $fullPath]);
                session()->forget('import_file_path');
                return back()->with('error', 'File tidak dapat dibaca. Silakan upload file lagi.');
            }
            
            $sessionFileSize = filesize($fullPath);
            if ($sessionFileSize === 0) {
                Log::error('Session file is empty', ['path' => $fullPath]);
                session()->forget('import_file_path');
                unlink($fullPath); // Clean up empty file
                return back()->with('error', 'File kosong. Silakan upload file yang berisi data.');
            }
        } else {
            Log::error('No file provided and no session file');
            return back()->with('error', 'Silakan upload file Excel terlebih dahulu.');
        }

        try {
            // Final verification that file exists and is ready
            if (!$fullPath || !file_exists($fullPath)) {
                Log::error('File does not exist at import run', ['path' => $fullPath, 'source' => $fileSource]);
                if ($fileSource === 'session') {
                    session()->forget('import_file_path');
                }
                return back()->with('error', 'File Excel tidak ditemukan. Pastikan file telah diupload dengan benar.');
            }
            
            if (!is_readable($fullPath)) {
                Log::error('File is not readable at import run', ['path' => $fullPath, 'source' => $fileSource]);
                if ($fileSource === 'session') {
                    session()->forget('import_file_path');
                }
                return back()->with('error', 'File tidak dapat dibaca. Silakan upload file lagi.');
            }

            // Check file size
            $fileSize = filesize($fullPath);
            if ($fileSize === 0) {
                Log::error('File is empty at import run', ['path' => $fullPath, 'size' => $fileSize]);
                if ($fileSource === 'session') {
                    session()->forget('import_file_path');
                }
                if (file_exists($fullPath)) {
                    unlink($fullPath); // Clean up empty file
                }
                return back()->with('error', 'File kosong. Silakan upload file yang berisi data.');
            }

            // 1) Dry-run validation first
            Log::info('Starting import run validation', [
                'file_path' => $fullPath, 
                'file_exists' => file_exists($fullPath),
                'file_size' => $fileSize,
                'source' => $fileSource
            ]);
            
            $testImport = new UsersImport((bool)$request->boolean('update_existing', true), true, $allowedModules);
            Excel::import($testImport, $fullPath);
            $testResult = $testImport->getResult();
            Log::info('Import run validation completed', ['result' => $testResult]);

            if (!empty($testResult['errors'])) {
                Log::warning('Import validation failed', ['errors' => $testResult['errors']]);
                return back()->with('test_result', $testResult)
                    ->with('error', 'Ditemukan error dalam file Excel. Silakan perbaiki dan coba lagi.');
            }

            // 2) Actual import
            Log::info('Starting actual import');
            $import = new UsersImport((bool)$request->boolean('update_existing', true), false, $allowedModules);
            Excel::import($import, $fullPath);
            $result = $import->getResult();
            Log::info('Import completed successfully', ['result' => $result]);

            // Clean up session and temporary file
            session()->forget('import_file_path');
            if ($cleanupFile && file_exists($fullPath)) {
                unlink($fullPath);
                Log::info('Cleaned up temporary file', ['path' => $fullPath]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', "Import berhasil: {$result['created']} user dibuat, {$result['updated']} user diupdate, {$result['skipped']} user dilewati.")
                ->with('import_result', $result);
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::error('Excel validation error', ['error' => $e->getMessage()]);
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            // Clean up on error
            session()->forget('import_file_path'); // Clear session on error
            if ($cleanupFile && file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            return back()->withErrors(['file' => 'Error validasi Excel'])
                ->with('error', 'Ditemukan error dalam file Excel:')
                ->with('excel_errors', $errors);
                
        } catch (\Exception $e) {
            Log::error('Import failed with exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up on error
            session()->forget('import_file_path'); // Clear session on error
            if ($cleanupFile && file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            $errorMessage = 'Import gagal: ' . $e->getMessage();
            
            // Provide more specific error messages for common issues
            if (strpos($e->getMessage(), 'does not exist') !== false) {
                $errorMessage = 'File Excel tidak ditemukan. Silakan upload file lagi.';
            } elseif (strpos($e->getMessage(), 'Permission denied') !== false) {
                $errorMessage = 'Tidak dapat mengakses file. Pastikan file tidak sedang dibuka di Excel.';
            } elseif (strpos($e->getMessage(), 'Invalid file format') !== false) {
                $errorMessage = 'Format file tidak valid. Pastikan file adalah Excel (.xlsx, .xls) atau CSV.';
            }
            
            return back()->withErrors(['file' => $errorMessage])
                ->with('error', 'Terjadi kesalahan saat import. Silakan coba lagi.');
        }
    }

    /**
     * Show bulk edit form
     */
    public function bulkEdit(Request $request)
    {
        Log::info('bulkEdit called', ['request' => $request->all()]);
        $ids = $request->input('ids', $request->input('id', []));
        if (!is_array($ids)) { $ids = [$ids]; }
        $users = User::whereIn('id', $ids)->get();
        $availableModules = $this->getAvailableModules();
        return view('admin.users.bulk-edit', compact('users', 'availableModules'));
    }

    /**
     * Apply bulk update to selected users
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'apply' => 'required|array',
        ]);

        $apply = $request->input('apply', []);
        $count = 0;

        $users = User::whereIn('id', $request->input('ids', []))->get();
    foreach ($users as $user) {
            $data = [];
            if (isset($apply['level']) && $request->filled('level')) {
        $data['level'] = (string) $request->input('level');
            }
            if (isset($apply['divisi'])) {
                $data['divisi'] = $request->input('divisi');
            }
            if (isset($apply['garage'])) {
                $data['garage'] = $request->input('garage');
            }
            if (isset($apply['system_access'])) {
                $data['system_access'] = $request->input('system_access', []);
            }
            if (isset($apply['password']) && $request->filled('password')) {
        $data['password'] = Hash::make((string) $request->input('password'));
            }

            if (!empty($data)) {
                /** @var \App\Models\User $user */
                $user->update($data);
                $count++;
            }
        }

        return redirect()->route('admin.users.index')->with('success', "Bulk update berhasil untuk {$count} user.");
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
            'dokumen_manajemen' => 'Dokumen Manajemen (View)',
            'dokumen_manajemen_admin' => 'Dokumen Manajemen Admin (CRUD)',
            'reports' => 'Reports',
            'settings' => 'Settings'
        ];
    }

    /**
     * Clean up old temporary files
     */
    private function cleanupOldTempFiles(): void
    {
        try {
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) {
                return;
            }

            $files = glob($tmpDir . '/*');
            $oneHourAgo = time() - 3600; // 1 hour ago

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $oneHourAgo) {
                    unlink($file);
                    Log::info('Cleaned up old temp file', ['file' => $file]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup temp files', ['error' => $e->getMessage()]);
        }
    }
}