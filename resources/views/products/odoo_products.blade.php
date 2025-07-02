<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-x2 text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Produk Odoo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Search Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <form method="GET" action="" id="searchForm" class="search-bar">
                            <input type="text" name="search" id="searchInput" class="form-control" 
                                placeholder="Cari ID, Nama, atau Kode..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>
                    </div>

                    <div class="row">
                        @foreach($products as $db => $items)
                            <div class="col-md-12 mb-4">
                                <h4 class="font-semibold mb-3">Database: {{ $db }}</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">ID</th>
                                            <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Nama</th>
                                            <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Kode</th>
                                            <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Harga</th>
                                            <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr>
                                            <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                                <span class="text-gray-900 dark:text-white">{{ $item['id'] }}</span>
                                            </td>
                                            <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                                <span class="text-gray-900 dark:text-white">{{ $item['name'] }}</span>
                                            </td>
                                            <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                                <span class="text-gray-900 dark:text-white">{{ $item['default_code'] ?? '-' }}</span>
                                            </td>
                                            <td class="border-collapse border border-gray-300 dark:border-gray-600">
                                                <span class="text-gray-900 dark:text-white">{{ $item['list_price'] }}</span>
                                            </td>
                                            <td class="border-collapse border border-gray-300 dark:border-gray-600 text-center">
                                                <a href="{{ route('odoo.products.show', [$db, $item['id']]) }}" 
                                                   class="inline-block px-4 py-2 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                                    Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    @if($page > 1)
                                        <a href="?page={{ $page - 1 }}" class="btn btn-secondary">&laquo; Prev</a>
                                    @else
                                        <span></span>
                                    @endif
                                    @if($hasNext[$db] ?? false)
                                        <a href="?page={{ $page + 1 }}" class="btn btn-secondary">Next &raquo;</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            document.getElementById('searchForm').submit();
        }, 500); // submit otomatis setelah 0.5 detik berhenti ngetik
    });
});
    </script>
</x-app-layout>