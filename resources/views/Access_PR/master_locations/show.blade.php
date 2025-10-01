<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Master Lokasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Navigation and Header -->
                    <div class="mb-6">
                        <div class="mb-4 flex justify-between items-center">
                            <a href="{{ route('master-locations.index') }}" 
                               class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                            <a href="{{ route('master-locations.edit', $masterLocation) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Edit
                            </a>
                        </div>
                        
                        <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $masterLocation->name }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $masterLocation->code }} - {{ $masterLocation->company }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $masterLocation->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                <i class="fas fa-{{ $masterLocation->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                {{ $masterLocation->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Kode Lokasi</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->code }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Perusahaan</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->company }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                    <span class="px-2 py-1 rounded-full text-sm {{ $masterLocation->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $masterLocation->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->address ?? 'Tidak ada data' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Telepon</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->phone ?? 'Tidak ada data' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->email ?? 'Tidak ada data' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <h4 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-blue-600 dark:text-blue-400"></i>
                            Statistik Penggunaan
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center bg-white dark:bg-gray-800 rounded p-4 shadow-sm">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $masterLocation->purchaseRequests->count() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Purchase Request</div>
                            </div>
                            
                            <div class="text-center bg-white dark:bg-gray-800 rounded p-4 shadow-sm">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $masterLocation->purchaseRequests->whereIn('status', ['approved', 'fully_approved'])->count() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">PR yang Disetujui</div>
                            </div>
                            
                            <div class="text-center bg-white dark:bg-gray-800 rounded p-4 shadow-sm">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ $masterLocation->purchaseRequests->whereIn('status', ['pending', 'partial_approved'])->count() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">PR Menunggu Approval</div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="mb-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-history mr-2 text-gray-600 dark:text-gray-400"></i>
                            Informasi Audit
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat pada</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->created_at->format('d M Y H:i') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Oleh: {{ $masterLocation->createdBy->name ?? 'System' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir diupdate</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $masterLocation->updated_at->format('d M Y H:i') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Oleh: {{ $masterLocation->updatedBy->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
