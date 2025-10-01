<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Configuration') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Pilihan Konfigurasi
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Kelola konfigurasi sistem untuk Purchase Request
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- PR Categories Configuration -->
                        @hasAccess('pr_categories')
                        @if (Auth::user()->divisi === 'FAT' && in_array(Auth::user()->level, ['manager', 'spv']))
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/30 rounded-lg border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="h-12 w-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tags text-white text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Rules Kategori PR
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Kelola kategori dan aturan persetujuan
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Konfigurasi persetujuan berdasarkan kategori
                                    </span>
                                    <a href="{{ route('pr-categories.index') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <i class="fas fa-cog mr-2"></i>
                                        Kelola
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endhasAccess

                        <!-- Master Location Configuration -->
                        @if(Auth::user()->hasAccess('master_location'))
                        <div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/30 rounded-lg border border-purple-200 dark:border-purple-800 hover:shadow-lg transition-shadow duration-200">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="h-12 w-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Master Location
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Kelola data lokasi perusahaan
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Konfigurasi lokasi untuk PR
                                    </span>
                                    <a href="{{ route('master-locations.index') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <i class="fas fa-cog mr-2"></i>
                                        Kelola
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Placeholder for future configurations -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700 opacity-60">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="h-12 w-12 bg-gray-400 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-plus text-white text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-500 dark:text-gray-400">
                                            Coming Soon
                                        </h4>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">
                                            Konfigurasi lainnya
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400 dark:text-gray-500">
                                        Akan tersedia segera
                                    </span>
                                    <button disabled 
                                            class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed">
                                        <i class="fas fa-lock mr-2"></i>
                                        Kelola
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    @php
                        $canAccessPrCategories = Auth::user()->divisi === 'FAT' && in_array(Auth::user()->level, ['manager', 'spv']);
                        $canAccessMasterLocation = Auth::user()->hasAccess('master_location');
                        $hasAnyConfigAccess = $canAccessPrCategories || $canAccessMasterLocation;
                    @endphp
                    
                    @if (!$hasAnyConfigAccess)
                        <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        Akses Terbatas
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>Anda tidak memiliki akses ke beberapa konfigurasi. Hubungi administrator untuk mendapatkan akses yang diperlukan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
