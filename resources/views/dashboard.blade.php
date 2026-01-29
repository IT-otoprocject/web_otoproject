<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Menu SPK Garage -->
                @hasAccess('spk_garage')
                <a href="{{ route('spk.daily') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center hover:bg-blue-100 dark:hover:bg-blue-700 transition aspect-square min-w-[140px] max-w-[220px] min-h-[140px] max-h-[220px] mx-auto">
                    <div class="flex flex-col items-center justify-center">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSAEzwyh_9UL3CUopvOkYhXiRg_nFlX-bbgg&s" alt="SPK Icon" class="h-16 w-16 mb-4 object-contain border-4 border-red-500" style="border-radius:16px;" />
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">SPK Garage</span>
                    </div>
                </a>
                @endhasAccess
                
                <!-- Menu PR -->
                @hasAccess('pr')
                <a href="{{ route('purchase-request.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center hover:bg-green-100 dark:hover:bg-green-700 transition aspect-square min-w-[140px] max-w-[220px] min-h-[140px] max-h-[220px] mx-auto">
                    <div class="flex flex-col items-center justify-center">
                        <img src="https://i.pinimg.com/1200x/52/c2/13/52c213b67f558464e827e4c4500ae5ea.jpg" alt="PR Icon" class="h-16 w-16 mb-4 object-contain border-4 border-red-500" style="border-radius:16px;" />
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Purchase Request</span>
                    </div>
                </a>
                @endhasAccess
                
                <!-- Menu Dokumen Manajemen -->
                @hasAccess('dokumen_manajemen')
                <a href="{{ route('document-management.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center hover:bg-yellow-100 dark:hover:bg-yellow-700 transition aspect-square min-w-[140px] max-w-[220px] min-h-[140px] max-h-[220px] mx-auto">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="h-16 w-16 mb-4 text-yellow-500 border-4 border-red-500" style="border-radius:16px; padding: 8px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                        </svg>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Dokumen Manajemen</span>
                    </div>
                </a>
                @endhasAccess
                
            </div>
        </div>
    </div>
</x-app-layout>