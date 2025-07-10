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

    <!-- Script tambah hapus item dan autocomplete -->
    <script>
        let debounceTimer;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Add item functionality
            document.getElementById('addItem').addEventListener('click', function() {
                const container = document.getElementById('itemContainer');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
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
                `;
                container.appendChild(newRow);
                attachProductAutocomplete(newRow.querySelector('.product-input'));
            });

            // Remove item functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.removeItem')) {
                    const row = e.target.closest('tr');
                    const container = document.getElementById('itemContainer');
                    if (container.children.length > 1) {
                        row.remove();
                    }
                }
            });

            // Attach autocomplete to existing inputs
            document.querySelectorAll('.product-input').forEach(attachProductAutocomplete);

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const productInputs = form.querySelectorAll('input[name="nama_barang[]"]');
                const names = new Set();
                let hasDuplicate = false;
                productInputs.forEach(input => {
                    const val = input.value.trim().toLowerCase();
                    if (val !== '' && names.has(val)) {
                        hasDuplicate = true;
                        input.classList.add('border-red-500');
                    } else {
                        input.classList.remove('border-red-500');
                    }
                    names.add(val);
                });
                if (hasDuplicate) {
                    e.preventDefault();
                    alert('Product ada yang sama lebih dari 1, Silahkan hapus product yang sama dan Cukup ubah Qty');
                    return false;
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.product-input-container')) {
                    document.querySelectorAll('.product-dropdown').forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });
                }
            });
        });

        function attachProductAutocomplete(input) {
            const container = input.closest('.product-input-container');
            const dropdown = container.querySelector('.product-dropdown');
            const skuInput = input.closest('tr').querySelector('.sku-input');

            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (query.length >= 2) {
                        searchProducts(query, dropdown, input, skuInput);
                    } else {
                        dropdown.innerHTML = '';
                        dropdown.classList.add('hidden');
                    }
                }, 300);
            });

            input.addEventListener('focus', function() {
                positionDropdown(input, dropdown);
            });
        }

        function positionDropdown(input, dropdown) {
            const rect = input.getBoundingClientRect();
            const dropdownHeight = 200; // max height
            const viewportHeight = window.innerHeight;
            const spaceBelow = viewportHeight - rect.bottom;
            const spaceAbove = rect.top;

            // Position dropdown
            dropdown.style.left = rect.left + 'px';
            dropdown.style.width = rect.width + 'px';
            
            // Check if there's enough space below
            if (spaceBelow >= dropdownHeight || spaceBelow >= spaceAbove) {
                dropdown.style.top = (rect.bottom + 2) + 'px';
                dropdown.style.bottom = 'auto';
            } else {
                dropdown.style.bottom = (viewportHeight - rect.top + 2) + 'px';
                dropdown.style.top = 'auto';
            }
        }

        function searchProducts(query, dropdown, input, skuInput) {
            // Show loading state
            dropdown.innerHTML = '<div class="product-dropdown-item text-gray-500">Loading...</div>';
            dropdown.classList.remove('hidden');
            positionDropdown(input, dropdown);
            
            fetch(`/api/search-products?search=${encodeURIComponent(query)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Products found:', data);
                    dropdown.innerHTML = '';
                    
                    if (data.length === 0) {
                        dropdown.innerHTML = '<div class="product-dropdown-item text-gray-500">No products found</div>';
                    } else {
                        data.forEach(product => {
                            const item = document.createElement('div');
                            item.className = 'product-dropdown-item text-gray-900 dark:text-white';
                            item.innerHTML = `
                                <div class="font-medium">${product.name}</div>
                                <div class="text-sm text-gray-500">SKU: ${product.default_code || 'N/A'} | DB: ${product.database}</div>
                            `;
                            item.addEventListener('click', function() {
                                input.value = product.name;
                                skuInput.value = product.default_code || '';
                                dropdown.classList.add('hidden');
                            });
                            dropdown.appendChild(item);
                        });
                    }
                    
                    dropdown.classList.remove('hidden');
                    positionDropdown(input, dropdown);
                })
                .catch(error => {
                    console.error('Error searching products:', error);
                    dropdown.innerHTML = '<div class="product-dropdown-item text-red-500">Error loading products: ' + error.message + '</div>';
                    dropdown.classList.remove('hidden');
                    positionDropdown(input, dropdown);
                });
        }

        // Reposition dropdown on window resize
        window.addEventListener('resize', function() {
            document.querySelectorAll('.product-dropdown:not(.hidden)').forEach(dropdown => {
                const container = dropdown.closest('.product-input-container');
                const input = container.querySelector('.product-input');
                positionDropdown(input, dropdown);
            });
        });
    </script>
</x-app-layout>