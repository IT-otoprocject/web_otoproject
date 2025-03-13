<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat SPK') }}
        </h2>
    </x-slot>

    <div class="container py-12 text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('spk.store') }}" method="POST">
                        @csrf

                        <!-- Informasi SPK -->
                        <div class="table-container mb-6">
                            <table class="detail-table w-full text-left border-collapse">
                                <tbody>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4 w-1/4">Garage</td>
                                        <td class="py-2 px-4">
                                            <select name="garage" id="garage" class="form-control w-full" required>
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
                                        <td class="label font-semibold py-2 px-4">Tanggal</td>
                                        <td class="py-2 px-4">
                                            <input type="date" class="form-control w-full" id="tanggal" name="tanggal" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">Teknisi 1</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full" id="teknisi_1" name="teknisi_1" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">Teknisi 2</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full" id="teknisi_2" name="teknisi_2">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">Customer</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full" id="customer" name="customer" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">Alamat</td>
                                        <td class="py-2 px-4">
                                            <textarea class="form-control w-full" id="alamat" name="alamat" required></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">No. HP</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full" id="no_hp" name="no_hp" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">Jenis Mobil</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full" id="jenis_mobil" name="jenis_mobil" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">No. Plat</td>
                                        <td class="py-2 px-4">
                                            <input type="text" class="form-control w-full" id="no_plat" name="no_plat" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label font-semibold py-2 px-4">Catatan</td>
                                        <td class="py-2 px-4">
                                            <textarea class="form-control w-full" id="catatan" name="catatan"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Barang Section -->
                        <h2 class="section-title font-semibold text-lg mb-4">Barang</h2>

                        <div class="table-container mb-6">
                            <table class="table w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border px-4 py-2">Nama Barang</th>
                                        <th class="border px-4 py-2">Quantity</th>
                                        <th class="border px-4 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemContainer">
                                    <tr>
                                        <td class="border px-4 py-2">
                                            <input type="text" name="nama_barang[]" class="form-control w-full" required>
                                        </td>
                                        <td class="border px-4 py-2">
                                            <input type="number" name="qty[]" class="form-control w-full" required>
                                        </td>
                                        <td class="border px-4 py-2 text-center">
                                            <button type="button" class="btn btn-outline-danger removeItem">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" id="addItem" class="btn btn-secondary">
                                Tambah Barang
                            </button>
                        </div>

                        <!-- Tombol Add Item & Submit -->
                        <div class="flex space-x-4">
                            
                            <button type="submit" class="btn btn-primary">
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
