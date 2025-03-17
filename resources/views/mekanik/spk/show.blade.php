<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Detail SPK') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        @if ($spk->status === 'Baru Diterbitkan')
                        <form action="{{ route('spk.cancel', ['spk_id' => $spk->id]) }}" method="POST" onsubmit="return confirm('Apakah yakin ingin membatalkan SPK ini?')">
                            @csrf
                            @method('PUT') {{-- Jika pakai update, atau POST sesuai route yang dibuat --}}
                            <button type="submit" class="btn btn-danger mb-4">Cancel</button>
                        </form>
                        @endif

                        @if ($spk->status === 'Dalam Pengerjaan')
                        <a href="{{ route('spk.edit', ['spk_id' => $spk->id]) }}" class="btn btn-warning mb-4">
                            Edit SPK
                        </a>
                        @endif

                        @if (in_array($spk->status, ['Baru Diterbitkan', 'Dalam Pengerjaan']) && Auth::user()->level === 'mekanik')
                        <a href="{{ route('kerja.mekanik', ['spk_id' => $spk->id]) }}" class="btn btn-primary mb-4">
                            Mulai Kerja
                        </a>
                        @endif

                    </div>


                    {{-- Tabel Detail SPK --}}
                    <div class="table-container mb-6">
                        <table class="detail-table">
                            <h2>{{ $spk->no_spk }}</h2>
                            <tbody>

                                <tr>
                                    <td class="label">Garage</td>
                                    <td>: {{ $spk->garage }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Tanggal</td>
                                    <td>: {{ $spk->tanggal }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teknisi 1</td>
                                    <td>: {{ $spk->teknisi_1 }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teknisi 2</td>
                                    <td>: {{ $spk->teknisi_2 }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Customer</td>
                                    <td>: {{ $spk->customer }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Alamat</td>
                                    <td>: {{ $spk->alamat }}</td>
                                </tr>
                                <tr>
                                    <td class="label">No. HP</td>
                                    <td>: {{ $spk->no_hp }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Jenis Mobil</td>
                                    <td>: {{ $spk->jenis_mobil }}</td>
                                </tr>
                                <tr>
                                    <td class="label">No. Plat</td>
                                    <td>: {{ $spk->no_plat }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Catatan</td>
                                    <td>: {{ $spk->catatan }}</td>
                                </tr>
                                <tr>
                                    <td class="label" style="border-bottom: 1px solid white;">Status</td>
                                    <td style="border-bottom: 1px solid white;">: {{ $spk->status }}</td>
                                </tr>

                                @if ($spk->status === 'Sudah Selesai')
                                <tr>
                                    <td class="label">
                                        <h4>Mekanik :</h4>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="label">Waktu Kerja</td>
                                    <td>: {{ $spk->waktu_kerja }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Catatan Kerja</td>
                                    <td>: {{ $spk->catatan_kerja }}</td>
                                </tr>
                                <tr>
                                    <td class="label" style="border-bottom: 1px solid white;">Akun Mekanik</td>
                                    <td style="border-bottom: 1px solid white;">: {{ $spk->teknisi_selesai }}</td>
                                </tr>

                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabel Barang --}}
                    <h2 class="section-title">Barang :</h2>
                    <div class="table-container">
                        <table class="table-barang-show w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="border-bottom: 2px solid white;">
                                        <h5>Nama Barang</h5>
                                    </th>
                                    <th style="width: 80px; text-align: center; border-bottom: 2px solid white;">
                                        <h5>Jumlah (QTY)</h5>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($spk->items && $spk->items->isNotEmpty())
                                @foreach ($spk->items as $index => $item)
                                <tr>
                                    <td style="width: 15px;" class="custom-td"></td>
                                    <td class="custom-td" style="">{{ $item->nama_barang }}</td>
                                    <td class="custom-td" style="width: 80px; text-align: center; ">{{ $item->qty }}</td>
                                    <td style="width: 15px;"></td>
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