<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dokumen Manajemen') }}
            </h2>
            @if(auth()->user()->hasAccess('dokumen_manajemen_admin'))
                <a href="{{ route('document-management.manage-folders') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Kelola Folder
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Folders Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($folders as $folder)
                    <a href="{{ route('document-management.folder', $folder->slug) }}" 
                       class="block bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <!-- Folder Icon -->
                                <svg class="w-12 h-12 text-yellow-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                </svg>
                                
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $folder->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $folder->documents_count }} dokumen
                                    </p>
                                </div>
                            </div>
                            
                            @if($folder->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $folder->description }}
                                </p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada folder</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada folder dokumen yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
