<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Rules Kategori PR') }}
            </h2>
            <a href="{{ route('pr-categories.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kategori
            </a>
        </div>
    </x-slot>

    <!-- Popup Notification -->
    @if (session('error'))
    <div id="notifPopup" class="notif-popup bg-red-500">
        <p>{{ session('error') }}</p>
    </div>
    @endif
    
    @if (session('success'))
    <div id="notifPopup" class="notif-popup bg-green-500">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($categories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kategori
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Aturan Persetujuan
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Dibuat
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($categories as $category)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $category->name }}
                                                    </div>
                                                    @if($category->description)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ Str::limit($category->description, 50) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($category->approval_rules as $rule)
                                                        <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 text-xs rounded">
                                                            {{ \App\Models\Access_PR\PrCategory::getAvailableApprovalLevels()[$rule] ?? $rule }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('pr-categories.toggle-status', $category) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full transition-colors duration-200
                                                                   {{ $category->is_active 
                                                                      ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-800 dark:text-green-200' 
                                                                      : 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-800 dark:text-red-200' }}">
                                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div>{{ $category->created_at->format('d/m/Y') }}</div>
                                                <div class="text-xs">{{ $category->createdBy->name ?? 'Unknown' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('pr-categories.show', $category) }}" 
                                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pr-categories.edit', $category) }}" 
                                                       class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('pr-categories.destroy', $category) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 dark:text-gray-400 mb-4">
                                <i class="fas fa-folder-open text-6xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                Belum ada kategori PR
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">
                                Mulai dengan membuat kategori PR pertama Anda.
                            </p>
                            <a href="{{ route('pr-categories.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Kategori Pertama
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto hide notification popup
        setTimeout(function() {
            const popup = document.getElementById('notifPopup');
            if (popup) {
                popup.style.display = 'none';
            }
        }, 5000);
    </script>
    
    <style>
        .notif-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            z-index: 9999;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .notif-popup.bg-red-500 {
            background-color: #ef4444;
        }
        
        .notif-popup.bg-green-500 {
            background-color: #10b981;
        }
    </style>
</x-app-layout>
