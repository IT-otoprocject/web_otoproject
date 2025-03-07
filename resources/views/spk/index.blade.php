<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar SPK') }}
        </h2>
    </x-slot>
    <div class="container">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($spks->isNotEmpty()) <!-- Memeriksa apakah koleksi tidak kosong -->
                        <table class="table">
                            <a href="{{ route('spk.create') }}" class="btn btn-primary">Buat SPK Baru</a>
                            <thead>
                                <tr>
                                    <th>No. SPK</th>
                                    <th>Garage</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>No. Plat</th>
                                    <th>Status</th>
                                    <th>Durasi Pengerjaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($spks as $spk)
                                <tr>
                                    <td>{{ $spk->no_spk }}</td>
                                    <td>{{ $spk->garage }}</td>
                                    <td>{{ $spk->tanggal }}</td>
                                    <td>{{ $spk->customer }}</td>
                                    <td>{{ $spk->no_plat }}</td>
                                    <td>{{ $spk->status }}</td>
                                    <td>{{ $spk->waktu_kerja }}</td>
                                    <td><a href="{{ route('mekanik.spk.show', $spk->id) }}">Lihat Detail</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p>Tidak ada data SPK ditemukan.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>