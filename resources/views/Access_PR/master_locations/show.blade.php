<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Master Lokasi') }}
        </h2>
    </x-slot>

    <style>
        .master-location-show {
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f2ff 50%, #f0f8ff 100%) !important;
            min-height: 100vh;
        }
        .content-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%) !important;
        }
        .dark .master-location-show {
            background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #1f2937 100%) !important;
        }
        .dark .content-card {
            background: linear-gradient(135deg, #374151 0%, #4b5563 50%, #6b7280 100%) !important;
        }
    </style>

    <div class="py-12 master-location-show">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="content-card overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100 min-h-[600px]">

                    <!-- Header with Status -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $masterLocation->name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                {{ $masterLocation->code }} - {{ $masterLocation->company }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $masterLocation->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                <span class="w-2 h-2 mr-1.5 rounded-full {{ $masterLocation->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $masterLocation->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-600 dark:text-blue-400"></i>
                                Informasi Dasar
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Lokasi:</span>
                                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $masterLocation->code }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lokasi:</span>
                                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $masterLocation->name }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Perusahaan:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->company }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-address-card mr-2 text-green-600 dark:text-green-400"></i>
                                Informasi Kontak
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->address ?? 'Tidak ada data' }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Telepon:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->phone ?? 'Tidak ada data' }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->email ?? 'Tidak ada data' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Statistics -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800 mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-blue-600 dark:text-blue-400"></i>
                            Statistik Penggunaan
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $masterLocation->purchaseRequests->count() }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Total Purchase Request
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                        {{ $masterLocation->purchaseRequests->whereIn('status', ['approved', 'fully_approved'])->count() }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        PR yang Disetujui
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ $masterLocation->purchaseRequests->whereIn('status', ['pending', 'partial_approved'])->count() }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        PR Menunggu Approval
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-history mr-2 text-gray-600 dark:text-gray-400"></i>
                            Informasi Audit
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat pada:</span>
                                <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->created_at->format('d M Y H:i') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Oleh: {{ $masterLocation->createdBy->name ?? 'System' }}
                                </p>
                            </div>
                            
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir diupdate:</span>
                                <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->updated_at->format('d M Y H:i') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Oleh: {{ $masterLocation->updatedBy->name ?? 'System' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('master-locations.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Daftar
                        </a>
                        
                        <div class="flex space-x-2">
                            <button onclick="toggleStatus({{ $masterLocation->id }})" 
                                    class="inline-flex items-center px-4 py-2 {{ $masterLocation->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-{{ $masterLocation->is_active ? 'pause' : 'play' }} mr-2"></i>
                                {{ $masterLocation->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                            
                            <a href="{{ route('master-locations.edit', $masterLocation) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Lokasi
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for toggle status -->
    <script>
        function toggleStatus(locationId) {
            if (confirm('Apakah Anda yakin ingin mengubah status lokasi ini?')) {
                fetch(`/master-locations/${locationId}/toggle-status`, {
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
    </script>
</x-app-layout>
