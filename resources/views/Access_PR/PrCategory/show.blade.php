<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Rules Kategori PR') }}
        </h2>
    </x-slot>

    @include('Access_PR.master_locations.partials.notifications')

    <style>
        .pr-category-show {
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f2ff 50%, #f0f8ff 100%) !important;
            min-height: 100vh;
        }
        .content-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%) !important;
        }
        .dark .pr-category-show {
            background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #1f2937 100%) !important;
        }
        .dark .content-card {
            background: linear-gradient(135deg, #374151 0%, #4b5563 50%, #6b7280 100%) !important;
        }
    </style>

    <div class="py-12 pr-category-show">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="content-card overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100 min-h-[600px]">

                    <!-- Navigation and Header -->
                    <div class="mb-6">
                        <div class="mb-4 flex justify-between items-center">
                            <a href="{{ route('pr-categories.index') }}" 
                               class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                            <a href="{{ route('pr-categories.edit', $prCategory) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Edit
                            </a>
                        </div>
                        
                        <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $prCategory->name }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $prCategory->code }} - Kategori Purchase Request
                                </p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $prCategory->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    <span class="w-2 h-2 mr-1.5 rounded-full {{ $prCategory->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    {{ $prCategory->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Informasi Dasar
                            </h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode:</span>
                                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $prCategory->code }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Kategori:</span>
                                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $prCategory->name }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $prCategory->description ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $prCategory->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $prCategory->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                        {{ $prCategory->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Informasi Timestamp
                            </h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $prCategory->created_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Oleh: {{ $prCategory->createdBy->name ?? 'N/A' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diupdate:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $prCategory->updated_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Oleh: {{ $prCategory->updatedBy->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Rules -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-check-circle mr-2 text-blue-600 dark:text-blue-400"></i>
                            Aturan Persetujuan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $prCategory->require_manager_approval ? 'Ya' : 'Tidak' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Persetujuan Manager
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ $prCategory->require_ceo_approval ? 'Ya' : 'Tidak' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Persetujuan CEO
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ $prCategory->require_finance_approval ? 'Ya' : 'Tidak' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Persetujuan Finance
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Statistics -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-gray-600 dark:text-gray-400"></i>
                            Statistik Penggunaan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $prCategory->purchaseRequests()->count() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Total Purchase Request
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $prCategory->purchaseRequests()->where('status', 'approved')->count() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    PR yang Disetujui
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for toggle status -->
    <script>
        function toggleStatus(categoryId) {
            if (confirm('Apakah Anda yakin ingin mengubah status kategori ini?')) {
                fetch(`/pr-categories/${categoryId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengubah status');
                });
            }
        }

        // Auto-hide notification popup
        setTimeout(function() {
            const popup = document.getElementById('notifPopup');
            if (popup) {
                popup.style.opacity = '0';
                setTimeout(() => popup.remove(), 300);
            }
        }, 5000);
    </script>


</x-app-layout>
