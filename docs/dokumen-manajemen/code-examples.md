# Document Management Module - Code Examples and API Reference

## Model Usage Examples

### DocumentFolder Model

**Namespace:** `App\Models\DocumentManagement\DocumentFolder`

```php
use App\Models\DocumentManagement\DocumentFolder;

// Retrieve all active folders with ordering
$folders = DocumentFolder::active()->ordered()->get();

// Retrieve folder with document count
$folder = DocumentFolder::withCount('documents')->find(1);
echo $folder->documents_count; // Example output: 5

// Query folder by slug
$folder = DocumentFolder::where('slug', 'sop')->first();

// Create new folder
$folder = DocumentFolder::create([
    'name' => 'Quality Assurance',
    'slug' => 'quality-assurance',
    'description' => 'Quality Assurance Documentation',
    'order' => 7,
    'is_active' => true,
]);

// Retrieve all documents within folder
$documents = $folder->documents;

// Retrieve only active documents
$activeDocuments = $folder->activeDocuments;

// Update folder status
$folder->update(['is_active' => false]);
```

### Document Model

**Namespace:** `App\Models\DocumentManagement\Document`

```php
use App\Models\DocumentManagement\Document;

// Retrieve document with relationships
$document = Document::with(['folder', 'uploader'])->find(1);

// Access human-readable file size
echo $document->file_size_human; // Example output: "2.5 MB"

// Access file URL for download/view
echo $document->file_url; // Example output: "/storage/documents/sop/123456_file.pdf"

// Increment download counter
$document->incrementDownloadCount();

// Query documents by folder
$documents = Document::where('folder_id', 1)
    ->orderBy('created_at', 'desc')
    ->paginate(15);

// Search documents by title
$results = Document::where('title', 'like', '%SOP%')
    ->with(['folder', 'uploader'])
    ->get();

// Retrieve recently uploaded documents
$recent = Document::with('uploader')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

// Retrieve most downloaded documents
$popular = Document::with(['folder', 'uploader'])
    ->orderBy('download_count', 'desc')
    ->limit(10)
    ->get();

// Soft delete document
$document->delete();

// Permanently delete (force delete)
$document->forceDelete();
```

## User Access Control

### Permission Checking

```php
use App\Models\User;

/** @var User|null $user */
$user = auth()->user();

// Verify view/download access
if ($user && $user->hasAccess('dokumen_manajemen')) {
    // User authorized to view and download documents
    return view('document-management.index');
}

// Verify administrative access
if ($user && $user->hasAccess('dokumen_manajemen_admin')) {
    // User authorized for full CRUD operations
    return view('document-management.manage-folders');
}

// Retrieve all accessible modules for user
$accessibleModules = $user->getAccessibleModules();

// Check multiple permissions
if ($user && $user->hasAccess(['dokumen_manajemen', 'spk'])) {
    // User has access to both modules
}
```

## Blade Template Directives

```blade
{{-- Single permission check --}}
@if(auth()->user() && auth()->user()->hasAccess('dokumen_manajemen'))
    <a href="{{ route('document-management.index') }}" class="menu-item">
        Document Management
    </a>
@endif

{{-- Administrative permission check --}}
@if(auth()->user() && auth()->user()->hasAccess('dokumen_manajemen_admin'))
    <button class="btn btn-primary" onclick="uploadDocument()">
        Upload Document
    </button>
@endif
@if(auth()->user()->hasAccess('dokumen_manajemen_admin'))
    <button>Upload Document</button>
@endif

{{-- Display document info --}}
{{ $document->title }}
{{ $document->file_size_human }}
{{ $document->uploader->name }}
{{ $document->created_at->format('d M Y') }}
```

## Controller Examples

### Custom Query in Controller

```php
public function searchDocuments(Request $request)
{
    $query = $request->input('q');
    
    $documents = Document::where('title', 'like', "%{$query}%")
        ->orWhere('description', 'like', "%{$query}%")
        ->with(['folder', 'uploader'])
        ->paginate(20);
    
    return view('document-management.search', compact('documents', 'query'));
}

public function statistics()
{
    $stats = [
        'total_folders' => DocumentFolder::count(),
        'total_documents' => Document::count(),
        'total_downloads' => Document::sum('download_count'),
        'total_size' => Document::sum('file_size'),
    ];
    
    return view('document-management.statistics', compact('stats'));
}

public function recentDocuments()
{
    $documents = Document::with(['folder', 'uploader'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    return view('document-management.recent', compact('documents'));
}
```

## File Upload Helper

```php
use Illuminate\Support\Facades\Storage;

// Custom upload method
public function uploadDocument($file, $folderId)
{
    $folder = DocumentFolder::findOrFail($folderId);
    
    // Validate
    $validatedData = request()->validate([
        'file' => 'required|file|mimes:pdf|max:3072',
    ]);
    
    // Generate filename
    $originalName = $file->getClientOriginalName();
    $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.pdf';
    
    // Store file
    $path = $file->storeAs('documents/' . $folder->slug, $filename, 'public');
    
    // Create record
    $document = Document::create([
        'folder_id' => $folderId,
        'title' => pathinfo($originalName, PATHINFO_FILENAME),
        'file_name' => $originalName,
        'file_path' => $path,
        'file_size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
        'uploaded_by' => auth()->id(),
    ]);
    
    return $document;
}

// Delete document with file
public function deleteDocumentWithFile($documentId)
{
    $document = Document::findOrFail($documentId);
    
    // Delete file from storage
    if (Storage::disk('public')->exists($document->file_path)) {
        Storage::disk('public')->delete($document->file_path);
    }
    
    // Delete record
    $document->delete();
    
    return true;
}
```

## Middleware Usage

```php
// In routes/web.php
Route::middleware(['auth', 'system_access:dokumen_manajemen'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index']);
});

// In controller
public function store(Request $request)
{
    // Check access manually
    if (!auth()->user()->hasAccess('dokumen_manajemen_admin')) {
        abort(403, 'Unauthorized');
    }
    
    // Process upload
}
```

## Event Listeners (Optional)

```php
// app/Events/DocumentUploaded.php
namespace App\Events;

use App\Models\Document;
use Illuminate\Foundation\Events\Dispatchable;

class DocumentUploaded
{
    use Dispatchable;
    
    public $document;
    
    public function __construct(Document $document)
    {
        $this->document = $document;
    }
}

// app/Listeners/NotifyDocumentUpload.php
namespace App\Listeners;

use App\Events\DocumentUploaded;
use Illuminate\Support\Facades\Log;

class NotifyDocumentUpload
{
    public function handle(DocumentUploaded $event)
    {
        Log::info('New document uploaded', [
            'document_id' => $event->document->id,
            'title' => $event->document->title,
            'uploaded_by' => $event->document->uploader->name,
        ]);
        
        // Send notification to admins
        // Notification::send($admins, new DocumentUploadedNotification($event->document));
    }
}

// In DocumentManagementController@store
event(new DocumentUploaded($document));
```

## Helper Functions

```php
// app/Helpers/DocumentHelper.php
namespace App\Helpers;

use App\Models\Document;
use App\Models\DocumentFolder;

class DocumentHelper
{
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    public static function getFolderStats($folderId)
    {
        $folder = DocumentFolder::findOrFail($folderId);
        
        return [
            'total_documents' => $folder->documents()->count(),
            'total_size' => $folder->documents()->sum('file_size'),
            'total_downloads' => $folder->documents()->sum('download_count'),
            'recent_upload' => $folder->documents()->latest()->first(),
        ];
    }
    
    public static function cleanOldDocuments($days = 365)
    {
        $date = now()->subDays($days);
        
        $documents = Document::onlyTrashed()
            ->where('deleted_at', '<', $date)
            ->get();
        
        foreach ($documents as $document) {
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
            $document->forceDelete();
        }
        
        return $documents->count();
    }
}
```

## Testing Examples

```php
// tests/Feature/DocumentManagementTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DocumentFolder;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_view_folders()
    {
        $user = User::factory()->create();
        $user->setAccess(['dokumen_manajemen']);
        
        $response = $this->actingAs($user)
            ->get(route('document-management.index'));
        
        $response->assertStatus(200);
    }
    
    public function test_admin_can_upload_document()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $user->setAccess(['dokumen_manajemen_admin']);
        
        $folder = DocumentFolder::factory()->create();
        
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        $response = $this->actingAs($user)
            ->post(route('document-management.store'), [
                'folder_id' => $folder->id,
                'title' => 'Test Document',
                'file' => $file,
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'folder_id' => $folder->id,
        ]);
    }
}
```

## API Endpoints (Jika Diperlukan)

```php
// routes/api.php
Route::middleware('auth:sanctum')->prefix('documents')->group(function () {
    Route::get('/', [DocumentApiController::class, 'index']);
    Route::get('/folders', [DocumentApiController::class, 'folders']);
    Route::get('/folder/{slug}', [DocumentApiController::class, 'folderDocuments']);
    Route::get('/{id}', [DocumentApiController::class, 'show']);
    Route::post('/', [DocumentApiController::class, 'store']);
    Route::put('/{id}', [DocumentApiController::class, 'update']);
    Route::delete('/{id}', [DocumentApiController::class, 'destroy']);
});

// app/Http/Controllers/Api/DocumentApiController.php
public function index()
{
    $documents = Document::with(['folder', 'uploader'])
        ->paginate(20);
    
    return response()->json($documents);
}

public function folders()
{
    $folders = DocumentFolder::active()
        ->ordered()
        ->withCount('documents')
        ->get();
    
    return response()->json($folders);
}
```

## Console Commands

```php
// app/Console/Commands/CleanOldDocuments.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\DocumentHelper;

class CleanOldDocuments extends Command
{
    protected $signature = 'documents:clean {--days=365}';
    protected $description = 'Clean old deleted documents';
    
    public function handle()
    {
        $days = $this->option('days');
        $count = DocumentHelper::cleanOldDocuments($days);
        
        $this->info("Cleaned {$count} old documents");
    }
}

// Register in app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('documents:clean')->monthly();
}
```
