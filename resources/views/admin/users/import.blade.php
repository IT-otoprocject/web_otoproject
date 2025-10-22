<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">Import Users</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Test first, then run the import.</p>
            </div>
            <a href="{{ route('admin.users.template') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg">Download Template</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                    @if(session('excel_errors'))
                        <ul class="list-disc ml-5 mt-2">
                            @foreach(session('excel_errors') as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Upload File Excel</h3>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">📋 Panduan Import:</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• <strong>Download template Excel</strong> terlebih dahulu untuk format yang benar</li>
                            <li>• <strong>Kolom WAJIB:</strong> name, email, system_access</li>
                            <li>• <strong>Kolom OPSIONAL:</strong> username, password, level, divisi, garage</li>
                            <li>• <strong>Username:</strong> kosong akan digenerate otomatis dari email</li>
                            <li>• <strong>Password:</strong> kosong akan digenerate otomatis</li>
                            <li>• <strong>Level:</strong> kosong = default 'staff'. Pilihan: admin, ceo, cfo, manager, spv, staff, headstore, kasir, sales, mekanik, pr_user</li>
                            <li>• <strong>System access:</strong> pisahkan dengan koma: dashboard,user_management,pr,spk_management</li>
                            <li>• <strong>File format:</strong> .xlsx, .xls, atau .csv</li>
                            <li>• <strong>Workflow:</strong> Test Import dulu untuk validasi, lalu Run Import untuk eksekusi</li>
                        </ul>
                        <div class="mt-2 pt-2 border-t border-blue-200">
                            <p class="text-xs text-blue-600">
                                💡 <strong>Tips:</strong> Jika ada error "file not found", pastikan file Excel tidak sedang dibuka dan coba upload ulang.
                            </p>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.users.import.test') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih File Excel</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-700 dark:text-gray-200 border border-gray-300 rounded-lg p-2" required>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="update_existing" value="1" checked>
                            Update user yang sudah ada berdasarkan email
                        </label>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">
                                🧪 Test Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('test_result'))
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    @php($r = session('test_result'))
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Test Result</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Created: <b>{{ $r['created'] }}</b> — Updated: <b>{{ $r['updated'] }}</b> — Skipped: <b>{{ $r['skipped'] }}</b>
                    </p>
                    @if(!empty($r['errors']))
                        <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded p-3 mb-3">
                            <b>Errors:</b>
                            <ul class="list-disc ml-5 mt-1">
                                @foreach($r['errors'] as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            Perbaiki error di atas, lalu jalankan Test Import lagi. Run Import akan memproses data yang valid dan melewati baris yang error.
                        </div>
                    @endif
                    @if(empty($r['errors']))
                    <form action="{{ route('admin.users.import.run') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <input type="hidden" name="update_existing" value="1">
                        @if(session('import_file_path'))
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <i class="fas fa-info-circle"></i> File dari test akan digunakan, atau upload file baru jika diperlukan.
                            </p>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-700 dark:text-gray-200 mb-3">
                        @else
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-700 dark:text-gray-200 mb-3" required>
                        @endif
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Run Import</button>
                        <a href="{{ route('admin.users.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg">Back to Users</a>
                    </form>
                    @else
                    <div class="mt-3">
                        <p class="text-sm text-red-600 dark:text-red-400 mb-3">
                            <i class="fas fa-exclamation-triangle"></i> Perbaiki error di atas dulu sebelum menjalankan import.
                        </p>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg">Back to Users</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
