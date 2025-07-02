<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-x2 text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Produk Odoo') }} ({{ $db }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($detail)
                        <table class="table">
                            <tr>
                                <th class="border-collapse border border-gray-300 dark:border-gray-600" style="width: 200px">
                                    <span class="text-gray-900 dark:text-gray-900">ID</span>
                                </th>
                                <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-white">{{ $detail['id'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-gray-900">Nama</span>
                                </th>
                                <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-white">{{ $detail['name'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-gray-900">Kode</span>
                                </th>
                                <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-white">{{ $detail['default_code'] ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-gray-900">Harga</span>
                                </th>
                                <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                    <span class="text-gray-900 dark:text-white">{{ $detail['list_price'] }}</span>
                                </td>
                            </tr>
                        </table>
                        <div class="mt-4">
                            <a href="{{ route('odoo.products') }}" 
                               class="inline-block px-4 py-2 bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white dark:text-white rounded-lg transition-colors duration-200">
                                Kembali ke Daftar Produk
                            </a>
                        </div>
                    @else
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">Produk tidak ditemukan.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

