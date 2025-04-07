<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit Informasi SPK') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Form Edit -->
                    <form action="{{ route('spk.update', ['spk_id' => $spk->id]) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Metode update -->

                        <!-- Garage -->
                        <div class="mb-4">
                            <label for="garage" class="block text-lg font-medium mb-2">Garage</label>
                            <select name="garage" id="garage" class="form-control w-full" required>
                                <option value="{{ $spk->garage }}">{{ $spk->garage }}</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Bekasi">Bekasi</option>
                                <option value="Bintaro">Bintaro</option>
                                <option value="Cengkareng">Cengkareng</option>
                                <option value="Cibubur">Cibubur</option>
                                <option value="Surabaya">Surabaya</option>
                            </select>
                        </div>

                        <!-- Tanggal -->
                        <div class="mb-4">
                            <label for="tanggal" class="block text-lg font-medium mb-2">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ $spk->tanggal }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300" required>
                        </div>

                        <!-- Teknisi 1 -->
                        <div class="mb-4">
                            <label for="teknisi_1" class="block text-lg font-medium mb-2">Teknisi 1</label>
                            <input type="text" id="teknisi_1" name="teknisi_1" value="{{ $spk->teknisi_1 }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
                        </div>

                        <!-- Teknisi 2 -->
                        <div class="mb-4">
                            <label for="teknisi_2" class="block text-lg font-medium mb-2">Teknisi 2</label>
                            <input type="text" id="teknisi_2" name="teknisi_2" value="{{ $spk->teknisi_2 }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
                        </div>

                        <!-- Customer -->
                        <div class="mb-4">
                            <label for="customer" class="block text-lg font-medium mb-2">Customer</label>
                            <input type="text" id="customer" name="customer" value="{{ $spk->customer }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300" required>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-4">
                            <label for="alamat" class="block text-lg font-medium mb-2">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">{{ $spk->alamat }}</textarea>
                        </div>

                        <!-- Nomor HP -->
                        <div class="mb-4">
                            <label for="no_hp" class="block text-lg font-medium mb-2">No. HP</label>
                            <input type="text" id="no_hp" name="no_hp" value="{{ $spk->no_hp }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300" required>
                        </div>

                        <!-- Jenis Mobil -->
                        <div class="mb-4">
                            <label for="jenis_mobil" class="block text-lg font-medium mb-2">Jenis Mobil</label>
                            <input type="text" id="jenis_mobil" name="jenis_mobil" value="{{ $spk->jenis_mobil }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
                        </div>

                        <!-- No. Plat -->
                        <div class="mb-4">
                            <label for="no_plat" class="block text-lg font-medium mb-2">No. Plat</label>
                            <input type="text" id="no_plat" name="no_plat" value="{{ $spk->no_plat }}"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">
                        </div>

                        <!-- Catatan -->
                        <div class="mb-4">
                            <label for="catatan" class="block text-lg font-medium mb-2">Catatan</label>
                            <textarea id="catatan" name="catatan" rows="3"
                                class="w-full p-4 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300">{{ $spk->catatan }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center mt-6">
                            <button type="submit" class="btn btn-primary text-lg px-6 py-3">
                                Simpan Perubahan
                            </button>
                        </div>


                        <!-- Barang Section (invisible) -->
                        <div style="display: none;"></div>
                            <!-- <h2 class="section-title font-semibold text-lg mb-4">Barang</h2> -->
                            <div class="table-container mb-6">
                                <table class="table-barang w-full text-left border-collapse">
                                    <thead>
                                        <tr>
                                            <!-- <th class="border-barang px-4 py-2">Nama Barang</th>
                                            <th class="border-barang px-4 py-2" style="width: 80px;">Quantity</th>
                                            <th class="border-barang px-4 py-2" style="width: 80px;"></th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="itemContainer">
                                        @foreach ($spk->items as $index => $item)
                                        <tr>
                                            <td class="custom-td">
                                                <input type="hidden" name="nama_barang[]" class="form-control w-full"
                                                    value="{{ $item->nama_barang }}">
                                            </td>
                                            <td class="custom-td">
                                                <input type="hidden" name="qty[]" class="form-control"
                                                    value="{{ $item->qty }}" style="width: 80px;">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>