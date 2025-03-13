<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Detail') }} {{ $spk->no_spk }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="content-wrapper">
            <div class="card">
                <div class="card-body">

                    {{-- Tombol Aksi --}}
                    @if (in_array($spk->status, ['Baru Diterbitkan', 'Dalam Pengerjaan']) && Auth::user()->level === 'mekanik')
                    <a href="{{ route('kerja.mekanik', ['spk_id' => $spk->id]) }}" class="btn btn-primary mb-4">
                        Mulai Kerja
                    </a>
                    @endif


                    @if ($spk->status === 'Baru Diterbitkan')
                    <form action="{{ route('spk.cancel', ['spk_id' => $spk->id]) }}" method="POST" onsubmit="return confirm('Apakah yakin ingin membatalkan SPK ini?')">
                        @csrf
                        @method('PUT') {{-- Jika pakai update, atau POST sesuai route yang dibuat --}}
                        <button type="submit" class="btn btn-danger mb-4">Cancel</button>
                    </form>
                    @endif

                    {{-- Tabel Detail SPK --}}
                    <div class="table-container mb-6">
                        <table class="detail-table">
                            <tbody>
                                <tr>
                                    <td class="label-header">SPK No.</td>
                                    <td class="label-header">{{ $spk->no_spk }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Garage</td>
                                    <td>{{ $spk->garage }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Tanggal</td>
                                    <td>{{ $spk->tanggal }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teknisi 1</td>
                                    <td>{{ $spk->teknisi_1 }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teknisi 2</td>
                                    <td>{{ $spk->teknisi_2 }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Customer</td>
                                    <td>{{ $spk->customer }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Alamat</td>
                                    <td>{{ $spk->alamat }}</td>
                                </tr>
                                <tr>
                                    <td class="label">No. HP</td>
                                    <td>{{ $spk->no_hp }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Jenis Mobil</td>
                                    <td>{{ $spk->jenis_mobil }}</td>
                                </tr>
                                <tr>
                                    <td class="label">No. Plat</td>
                                    <td>{{ $spk->no_plat }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Catatan</td>
                                    <td>{{ $spk->catatan }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Status</td>
                                    <td>{{ $spk->status }}</td>
                                </tr>

                                @if ($spk->status === 'Sudah Selesai')
                                <tr>
                                    <td class="label">Waktu Kerja</td>
                                    <td>{{ $spk->waktu_kerja }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Catatan Kerja</td>
                                    <td>{{ $spk->catatan_kerja }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teknisi Selesai</td>
                                    <td>{{ $spk->teknisi_selesai }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabel Barang --}}
                    <h2 class="section-title">Barang</h2>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah (QTY)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($spk->items && $spk->items->isNotEmpty())
                                @foreach ($spk->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->qty }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada barang ditemukan.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>