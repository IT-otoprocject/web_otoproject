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

                    <form action="{{ route('spk.store') }}" method="POST">
                        @csrf

                        <!-- Informasi SPK -->
                        <div class="table-container mb-6">
                            <table class="detail-table w-full text-left border-collapse">
                                <tbody>
                                    <h2 class="text-gray-900 dark:text-white mb-4">SPK Baru</h2>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 w-1/4 text-gray-900 dark:text-white">Garage</td>
                                        <td class="py-2 px-4">
                                            <select name="garage" id="garage" class="form-control w-full dark:text-white dark:bg-gray-700" required>
                                                <option value="Bandung">Bandung</option>
                                                <option value="Bekasi">Bekasi</option>
                                                <option value="Bintaro">Bintaro</option>
                                                <option value="Cengkareng">Cengkareng</option>
                                                <option value="Cibubur">Cibubur</option>
                                                <option value="Surabaya">Surabaya</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">Tanggal</td>
                                        <td class="py-2 px-4">
                                            <input type="date" class="form-control w-full dark:text-white dark:bg-gray-700" id="tanggal" name="tanggal" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">Teknisi 1</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="teknisi_1" name="teknisi_1" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">Teknisi 2</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="teknisi_2" name="teknisi_2">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">Customer</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="customer" name="customer" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">Alamat</td>
                                        <td class="py-2 px-4">
                                            <textarea class="form-control w-full dark:text-white dark:bg-gray-700" id="alamat" name="alamat" required></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">No. HP</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="no_hp" name="no_hp" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">Jenis Mobil</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="jenis_mobil" name="jenis_mobil" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white">No. Plat</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full dark:text-white dark:bg-gray-700" id="no_plat" name="no_plat" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 text-gray-900 dark:text-white" style="border-bottom: 1px solid white;">Catatan</td>
                                        <td class="py-2 px-4" style="border-bottom: 1px solid white;">
                                            <textarea class="form-control w-full dark:text-white dark:bg-gray-700" id="catatan" name="catatan"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        

                        <!-- Barang Section -->
                        <h2 class="section-title font-semibold text-lg mb-4 text-gray-900 dark:text-white">Product</h2>

                        <div class="table-container mb-6">
                            <table class="table-barang w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white">Nama Product</th>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white" style="width: 80px;">Quantity</th>
                                        <th class="border-barang px-4 py-2" style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemContainer">
                                    <tr>
                                        <td class="custom-td">
                                            <input type="text" name="nama_barang[]" class="form-control w-full dark:text-white dark:bg-gray-700" placeholder="Nama Product" required>
                                        </td>
                                        <td class="custom-td">
                                            <input type="number" name="qty[]" class="form-control dark:text-white dark:bg-gray-700" placeholder="Qty" style="width: 80px;" required>
                                        </td>

                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-outline-danger removeItem">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
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

    <!-- Script tambah hapus item -->
    <script>

    </script>
</x-app-layout>