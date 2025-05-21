<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-x2 text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('spk.updateBarang', ['spk_id' => $spk->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Barang Section -->
                        <h2><span class="section-title font-semibold text-lg mb-4 text-gray-900 dark:text-white">Product</span></h2>
                        <div class="table-container mb-6">
                            <table class="table-barang w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-barang px-4 py-2">
                                            <span class="text-gray-900 dark:text-white">Nama Product</span>
                                        </th>
                                        <th class="border-barang px-4 py-2" style="width: 80px;">
                                            <span class="text-gray-900 dark:text-white">Quantity</span>
                                        </th>
                                        <th class="border-barang px-4 py-2" style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemContainer">
                                    @foreach ($spk->items as $index => $item)
                                    <tr>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <input type="text" name="nama_barang[]" class="form-control w-full dark:text-white dark:bg-gray-700" value="{{ $item->nama_barang }}" required>
                                        </td>
                                        <td class="custom-td text-gray-900 dark:text-white">
                                            <input type="number" name="qty[]" class="form-control dark:text-white dark:bg-gray-700" value="{{ $item->qty }}" style="width: 80px;" required>
                                        </td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-outline-danger removeItem">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" id="addItem" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Tambah Product
                            </button>
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="flex justify-center space-x-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addNewItem() {
            const container = document.getElementById('barangBaruContainer');
            const index = container.children.length;
            const newItem = `
                <div class="form-group mb-3">
                    <label for="nama_barang_baru_${index}">Nama Barang:</label>
                    <input type="text" id="nama_barang_baru_${index}" name="nama_barang_baru[]" class="form-control">
                    <label for="qty_baru_${index}">Jumlah (QTY):</label>
                    <input type="number" id="qty_baru_${index}" name="qty_baru[]" class="form-control">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newItem);
        }

        function deleteBarang(itemId) {
            if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                fetch(`/spk/barang/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        document.getElementById(`barangBaru_${itemId}`).remove();
                        alert('Barang berhasil dihapus.');
                    } else {
                        alert('Gagal menghapus barang.');
                    }
                });
            }
        }
    </script>
</x-app-layout>
