<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat SPK') }}
        </h2>
    </x-slot>
    <div class="container" style="color: white;">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <form action="{{ route('spk.store') }}" method="POST">
                            @csrf
                            <!-- <div class="form-group">
                                <label for="no_spk">No. SPK</label>
                                <input style="color: black;" type="text" class="form-control" id="no_spk" name="no_spk" required>
                            </div> -->
                            <div class="form-group">
                                <label for="garage">Garage</label>
                                <select name="garage" id="garage" class="form-control" required>
                                    <option value="Bandung">Bandung</option>
                                    <option value="Bekasi">Bekasi</option>
                                    <option value="Bintaro">Bintaro</option>
                                    <option value="Cengkareng">Cengkareng</option>
                                    <option value="Cibubur">Cibubur</option>
                                    <option value="Surabaya">Surabaya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>

                            <div class="form-group">
                                <label for="teknisi_1">Teknisi 1</label>
                                <input type="text" class="form-control" id="teknisi_1" name="teknisi_1" required>
                            </div>
                            <div class="form-group">
                                <label for="teknisi_2">Teknisi 2</label>
                                <input type="text" class="form-control" id="teknisi_2" name="teknisi_2">
                            </div>
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <input type="text" class="form-control" id="customer" name="customer" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            </div>
                            <div class="form-group">
                                <label for="jenis_mobil">Jenis Mobil</label>
                                <input type="text" class="form-control" id="jenis_mobil" name="jenis_mobil" required>
                            </div>
                            <div class="form-group">
                                <label for="no_plat">No. Plat</label>
                                <input type="text" class="form-control" id="no_plat" name="no_plat" required>
                            </div>

                            <div class="form-group">
                                <label for="catatan">Catatan</label>
                                <textarea class="form-control" id="catatan" name="catatan"></textarea>
                            </div>

                            <div id="itemContainer">
                                <div class="form-group">
                                    <label for="nama_barang[]">Nama Barang:</label>
                                    <input type="text" name="nama_barang[]" required>
                                </div>
                                <div class="form-group">
                                    <label for="qty[]">Quantity:</label>
                                    <input type="number" name="qty[]" required>
                                </div>
                            </div>
                            <button type="button" id="addItem" class="btn btn-secondary">Add Item</button>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>