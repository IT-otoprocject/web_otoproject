<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('SPK') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->has('nama_barang'))
                    <div class="mb-4 text-red-600 font-semibold">
                        {{ $errors->first('nama_barang') }}
                    </div>
                    @endif
                    <form action="{{ route('spk.store') }}" method="POST">
                        @csrf

                        <!-- Informasi SPK -->
                        <div class="table-container mb-6">
                            <table class="detail-table w-full text-left border-collapse">
                                <tbody>
                                    <h2><span class="text-gray-900 dark:text-white mb-4">SPK Baru</span></h2>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 w-1/4">
                                            <span class="text-gray-900 dark:text-white">Garage</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <select name="garage" id="garage" class="form-control w-full dark:text-white dark:bg-gray-700" required>
                                                <option value="">Pilih Garage</option>
                                                <option value="Bandung" {{ old('garage') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                                <option value="Bekasi" {{ old('garage') == 'Bekasi' ? 'selected' : '' }}>Bekasi</option>
                                                <option value="Bintaro" {{ old('garage') == 'Bintaro' ? 'selected' : '' }}>Bintaro</option>
                                                <option value="Cengkareng" {{ old('garage') == 'Cengkareng' ? 'selected' : '' }}>Cengkareng</option>
                                                <option value="Cibubur" {{ old('garage') == 'Cibubur' ? 'selected' : '' }}>Cibubur</option>
                                                <option value="Surabaya" {{ old('garage') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">
                                            <span class="text-gray-900 dark:text-white">Tanggal</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <input type="date" class="form-control w-full dark:text-white dark:bg-gray-700" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">
                                            <span class="text-gray-900 dark:text-white">Customer</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="customer" name="customer" value="{{ old('customer') }}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">
                                            <span class="text-gray-900 dark:text-white">Alamat</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <textarea class="form-control w-full dark:text-white dark:bg-gray-700" id="alamat" name="alamat" required>{{ old('alamat') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">
                                            <span class="text-gray-900 dark:text-white">No. HP</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">
                                            <span class="text-gray-900 dark:text-white">Jenis Mobil</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="jenis_mobil" name="jenis_mobil" value="{{ old('jenis_mobil') }}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">
                                            <span class="text-gray-900 dark:text-white">No. Plat</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="no_plat" name="no_plat" value="{{ old('no_plat') }}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4" style="border-bottom: 1px solid white;">
                                            <span class="text-gray-900 dark:text-white">Catatan</span>
                                        </td>
                                        <td class="py-2 px-4 text-gray-900 dark:text-white" style="border-bottom: 1px solid white;">
                                            <textarea class="form-control w-full dark:text-white dark:bg-gray-700" id="catatan" name="catatan">{{ old('catatan') }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Barang Section -->
                        <h2><span class="section-title font-semibold text-lg mb-4 text-gray-900 dark:text-white">Product</span></h2>

                        <div class="table-container mb-6">
                            <table class="table-barang w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-barang px-4 py-2">
                                            <span class="text-gray-900 dark:text-white">Nama Product</span>
                                        </th>
                                        <th class="border-barang px-4 py-2" style="width: 120px;">
                                            <span class="text-gray-900 dark:text-white">SKU</span>
                                        </th>
                                        <th class="border-barang px-4 py-2" style="width: 80px;">
                                            <span class="text-gray-900 dark:text-white">Quantity</span>
                                        </th>
                                        <th class="border-barang px-4 py-2" style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemContainer">
                                    @if(old('nama_barang'))
                                    @foreach (old('nama_barang') as $i => $nama_barang)
                                    <tr>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <div class="product-input-container">
                                                <input type="text" name="nama_barang[]" class="form-control w-full dark:text-white dark:bg-gray-700 product-input" placeholder="Nama Product" value="{{ $nama_barang }}" required autocomplete="off">
                                                <div class="product-dropdown hidden max-h-60 overflow-y-auto"></div>
                                            </div>
                                        </td>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <input type="text" name="sku[]" class="form-control w-full dark:text-white dark:bg-gray-700 sku-input" placeholder="SKU" value="{{ old('sku')[$i] ?? '' }}" readonly>
                                        </td>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <input type="number" name="qty[]" class="form-control dark:text-white dark:bg-gray-700" placeholder="Qty" style="width: 80px;" value="{{ old('qty')[$i] ?? '' }}" required>
                                        </td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-outline-danger removeItem">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <div class="product-input-container">
                                                <input type="text" name="nama_barang[]" class="form-control w-full dark:text-white dark:bg-gray-700 product-input" placeholder="Nama Product" required autocomplete="off">
                                                <div class="product-dropdown hidden max-h-60 overflow-y-auto"></div>
                                            </div>
                                        </td>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <input type="text" name="sku[]" class="form-control w-full dark:text-white dark:bg-gray-700 sku-input" placeholder="SKU" readonly>
                                        </td>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <input type="number" name="qty[]" class="form-control dark:text-white dark:bg-gray-700" placeholder="Qty" style="width: 80px;" required>
                                        </td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-outline-danger removeItem">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" id="addItem" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Tambah Product
                            </button>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="flex justify-center space-x-4">
                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                Submit
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- FontAwesome CDN (jika belum ada di layout) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS untuk dropdown -->
    <style>
        .product-dropdown {
            max-height: 200px;
            overflow-y: auto;
            z-index: 9999 !important;
            position: fixed !important;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .dark .product-dropdown {
            background: #374151;
            border-color: #4b5563;
        }
        .product-dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #e5e5e5;
        }
        .product-dropdown-item:hover {
            background-color: #f5f5f5;
        }
        .dark .product-dropdown-item {
            border-bottom-color: #4b5563;
        }
        .dark .product-dropdown-item:hover {
            background-color: #4b5563;
        }
        .product-input-container {
            position: relative;
        }
        .table-container {
            overflow: visible !important;
        }
        .table-barang {
            overflow: visible !important;
        }
    </style>

</x-app-layout>