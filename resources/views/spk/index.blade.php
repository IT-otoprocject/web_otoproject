<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Daftar SPK</h1>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($spks->isNotEmpty())  <!-- Memeriksa apakah koleksi tidak kosong -->
        <table class="table">
            <thead>
                <tr>
                    <th>No. SPK</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($spks as $spk)
                <tr>
                    <td>{{ $spk->no_spk }}</td>
                    <td>{{ $spk->tanggal }}</td>
                    <td>{{ $spk->customer }}</td>
                    <td>{{ $spk->status }}</td>
                    <td><a href="{{ route('mekanik.spk.show', $spk->id) }}">Lihat Detail</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>Tidak ada data SPK ditemukan.</p>
    @endif
        <a href="{{ route('spk.create') }}" class="btn btn-primary">Buat SPK Baru</a>
    </div>
</x-app-layout>