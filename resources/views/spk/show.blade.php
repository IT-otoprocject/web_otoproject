<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail SPK') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Detail SPK</h1>
        <p><strong>No. SPK:</strong> {{ $spk->no_spk }}</p>
        <p><strong>Tanggal:</strong> {{ $spk->tanggal }}</p>
        <p><strong>Teknisi 1:</strong> {{ $spk->teknisi_1 }}</p>
        <p><strong>Teknisi 2:</strong> {{ $spk->teknisi_2 }}</p>
        <p><strong>Customer:</strong> {{ $spk->customer }}</p>
        <p><strong>Alamat:</strong> {{ $spk->alamat }}</p>
        <p><strong>No. HP:</strong> {{ $spk->no_hp }}</p>
        <p><strong>Jenis Mobil:</strong> {{ $spk->jenis_mobil }}</p>
        <p><strong>No. Plat:</strong> {{ $spk->no_plat }}</p>
        <p><strong>Catatan:</strong> {{ $spk->catatan }}</p>
        <h2>Barang</h2>
        <ul>
            @foreach ($spk->items as $item)
                <li>{{ $item->nama_barang }} - {{ $item->qty }}</li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
