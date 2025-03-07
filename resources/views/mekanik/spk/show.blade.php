<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail SPK') }} {{ $spk->no_spk }}
        </h2>
    </x-slot>
    <div class="container">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        @if ($spk->status !== 'Sudah Selesai' && Auth::user()->level === 'mekanik')
                        <a href="{{ route('kerja.mekanik', ['spk_id' => $spk->id]) }}" class="btn btn-primary">Mulai Kerja</a>
                        @endif


                        <p><strong>Garage:</strong> {{ $spk->garage }}</p>
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
                        <p><strong>Status:</strong> {{ $spk->status }}</p>

                        @if ($spk->status === 'Sudah Selesai')
                        <p><strong>Waktu Kerja:</strong> {{ $spk->waktu_kerja }}</p>
                        <p><strong>Catatan Kerja:</strong> {{ $spk->catatan_kerja }}</p>
                        <p><strong>Teknisi Selesai:</strong> {{ $spk->teknisi_selesai }}</p>
                        @endif

                        <h2>Barang :</h2>
                        <ul>
                            @if ($spk->items && $spk->items->isNotEmpty()) <!-- Memeriksa apakah items tidak null dan tidak kosong -->
                            @foreach ($spk->items as $item)
                            <li>{{ $item->nama_barang }} - {{ $item->qty }} QTY</li>
                            @endforeach
                            @else
                            <li>Tidak ada barang ditemukan.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>