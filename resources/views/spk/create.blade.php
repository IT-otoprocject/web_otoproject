<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container" style="color: white;">
        <h1>Buat SPK</h1>
        <form action="{{ route('spk.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="no_spk">No. SPK</label>
                <input type="text" class="form-control" id="no_spk" name="no_spk" required>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="no_so">No. SO</label>
                <input type="text" class="form-control" id="no_so" name="no_so" required>
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
                <label for="nama_barang">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            </div>
            <div class="form-group">
                <label for="qty">Qty</label>
                <input type="number" class="form-control" id="qty" name="qty" required>
            </div>
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</x-app-layout>