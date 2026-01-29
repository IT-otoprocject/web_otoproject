<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\DocumentManagement\Document;
use App\Models\DocumentManagement\DocumentFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentManagementController extends Controller
{
    /**
     * Display a listing of folders
     */
    public function index()
    {
        $folders = DocumentFolder::active()
            ->ordered()
            ->withCount('documents')
            ->get();

        return view('document-management.index', compact('folders'));
    }

    /**
     * Display documents in a specific folder
     */
    public function showFolder(DocumentFolder $folder)
    {
        $documents = Document::where('folder_id', $folder->id)
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        /** @var \App\Models\User|null $currentUser */
        $currentUser = auth()->user();
        $canManage = $currentUser ? $currentUser->hasAccess('dokumen_manajemen_admin') : false;

        return view('document-management.folder', compact('folder', 'documents', 'canManage'));
    }

    /**
     * Show the form for creating a new document
     */
    public function create(DocumentFolder $folder)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('document-management.create', compact('folder'));
    }

    /**
     * Store a newly created document in storage
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $request->validate([
            'folder_id' => 'required|exists:document_folders,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:3072', // 3MB max
        ], [
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 3 MB',
        ]);

        $file = $request->file('file');
        $folder = DocumentFolder::findOrFail($request->folder_id);

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.pdf';

        // Store file in storage/app/public/documents/{folder-slug}
        $path = $file->storeAs('documents/' . $folder->slug, $filename, 'public');

        // Create document record
        Document::create([
            'folder_id' => $request->folder_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $originalName,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('document-management.folder', $folder->slug)
            ->with('success', 'Dokumen berhasil diupload');
    }

    /**
     * Show the form for editing the specified document
     */
    public function edit(Document $document)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $folders = DocumentFolder::active()->ordered()->get();

        return view('document-management.edit', compact('document', 'folders'));
    }

    /**
     * Update the specified document in storage
     */
    public function update(Request $request, Document $document)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $request->validate([
            'folder_id' => 'required|exists:document_folders,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:3072', // 3MB max
        ], [
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 3 MB',
        ]);

        $data = [
            'folder_id' => $request->folder_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        // If new file uploaded, replace the old one
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = DocumentFolder::findOrFail($request->folder_id);

            // Delete old file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Store new file
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.pdf';
            $path = $file->storeAs('documents/' . $folder->slug, $filename, 'public');

            $data['file_name'] = $originalName;
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        $document->update($data);

        $folder = DocumentFolder::find($request->folder_id);

        return redirect()
            ->route('document-management.folder', $folder->slug)
            ->with('success', 'Dokumen berhasil diupdate');
    }

    /**
     * Remove the specified document from storage
     */
    public function destroy(Document $document)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $folder = $document->folder;

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('document-management.folder', $folder->slug)
            ->with('success', 'Dokumen berhasil dihapus');
    }

    /**
     * Download document
     */
    public function download(Document $document)
    {
        // Increment download count
        $document->incrementDownloadCount();

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, $document->file_name);
    }

    /**
     * View document in browser
     */
    public function view(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($filePath);
    }

    /**
     * Manage folders (admin only)
     */
    public function manageFolders()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $folders = DocumentFolder::withCount('documents')
            ->orderBy('order', 'asc')
            ->get();

        return view('document-management.manage-folders', compact('folders'));
    }

    /**
     * Store new folder
     */
    public function storeFolder(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:document_folders,name',
            'description' => 'nullable|string',
        ]);

        DocumentFolder::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => 'folder',
            'order' => DocumentFolder::max('order') + 1,
            'is_active' => true,
        ]);

        return redirect()
            ->route('document-management.manage-folders')
            ->with('success', 'Folder berhasil ditambahkan');
    }

    /**
     * Update folder
     */
    public function updateFolder(Request $request, DocumentFolder $folder)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:document_folders,name,' . $folder->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $folder->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('document-management.manage-folders')
            ->with('success', 'Folder berhasil diupdate');
    }

    /**
     * Delete folder
     */
    public function destroyFolder(DocumentFolder $folder)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        if (!$user || !$user->hasAccess('dokumen_manajemen_admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        // Check if folder has documents
        if ($folder->documents()->count() > 0) {
            return redirect()
                ->route('document-management.manage-folders')
                ->with('error', 'Tidak bisa menghapus folder yang masih memiliki dokumen');
        }

        $folder->delete();

        return redirect()
            ->route('document-management.manage-folders')
            ->with('success', 'Folder berhasil dihapus');
    }
}
