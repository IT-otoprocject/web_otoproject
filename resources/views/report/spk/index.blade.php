<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report SPK') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-12">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <a href="{{ route('report.spk.report_spk') }}" class="w-full flex items-center gap-3 font-medium px-4 py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition shadow text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 7.5h16.5M4.5 21h15a.75.75 0 00.75-.75V7.5a.75.75 0 00-.75-.75h-15a.75.75 0 00-.75.75v12.75c0 .414.336.75.75.75z" />
                </svg>
                {{ __('Lihat Report SPK') }}
            </a>
            <a href="{{ route('report.spk.barang') }}" class="w-full flex items-center gap-3 font-medium px-4 py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition shadow text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v1.5M3 7.5v9A2.25 2.25 0 005.25 18.75h13.5A2.25 2.25 0 0021 16.5v-9M3 7.5h18" />
                </svg>
                {{ __('Export Rata-rata Waktu Perngerjaan Barang') }}
            </a>
            <a href="{{ route('report.spk.mekanik_product') }}" class="w-full flex items-center gap-3 font-medium px-4 py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition shadow text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75v-1.5A2.25 2.25 0 0015 3h-6a2.25 2.25 0 00-2.25 2.25v1.5m10.5 0h-10.5m10.5 0v10.5A2.25 2.25 0 0115 18.75h-6A2.25 2.25 0 016.75 17.25V6.75m10.5 0h-10.5" />
                </svg>
                {{ __('Export Rata-rata Mekanik per Produk') }}
            </a>
            <a href="{{ route('report.spk.mekanik') }}" class="w-full flex items-center gap-3 font-medium px-4 py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition shadow text-base">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232a2.25 2.25 0 113.182 3.182l-9 9a2.25 2.25 0 01-3.182-3.182l9-9z" />
                </svg>
                {{ __('Export Rata-rata Kerja Mekanik') }}
            </a>
        </div>
    </div>
</x-app-layout>