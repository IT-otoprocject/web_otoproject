<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Detail SPK') }}
        </h2>
    </x-slot>
    <!-- Popup Notification -->
    @if (session('message'))
    <div id="notifPopup" class="notif-popup">
        <p>{{ session('message') }}</p>
    </div>
    @endif

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        @if ($spk->status === 'Baru Diterbitkan')
                        <button type="button" class="btn btn-danger mb-4" onclick="showCancelPopup()">Cancel</button>
                        @endif

                        <!-- Popup Alasan Cancel -->
                        <div id="cancelPopup" class="popup d-none">
                            <div class="popup-content">
                                <h5>Alasan Pembatalan</h5>
                                <form id="cancelForm" action="{{ route('spk.cancel', ['spk_id' => $spk->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT') {{-- Jika route menggunakan update --}}

                                    <!-- Input Alasan -->
                                    <div class="form-group mb-3">
                                        <label for="cancelReason" class="form-label">Alasan:</label>
                                        <textarea id="cancelReason" name="reason" class="form-control w-full" rows="3" required></textarea>
                                    </div>

                                    <!-- Tombol Konfirmasi dan Batal -->
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="closeCancelPopup()">Batal</button>
                                        <button type="submit" class="btn btn-danger">Konfirmasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>



                        @if ($spk->status === 'Dalam Proses')
                        <a href="{{ route('spk.edit', ['spk_id' => $spk->id]) }}"
                            class="btn btn-warning mb-4"
                            onclick="return confirm('Apakah Anda yakin ingin mengedit SPK ini?')">
                            Edit SPK
                        </a>
                        @endif

                        @if (in_array($spk->status, ['Baru Diterbitkan', 'Dalam Proses']) && Auth::user()->level === 'mekanik')
                        <form action="{{ route('spk.waktuMulaiKerja', ['spk_id' => $spk->id]) }}" method="POST" onsubmit="return startWorkConfirmation(event)">
                            @csrf
                            <button type="submit" class="btn btn-primary mb-4">
                                Mulai Kerja
                            </button>
                        </form>
                        @endif



                    </div>


                    {{-- Tabel Detail SPK --}}
                    <div class="table-container mb-6">
                        <table class="detail-table">
                            <h2>{{ $spk->no_spk }}</h2>

                            @if ($spk->status === 'Cancel')
                            <div class="alert alert-danger">
                                <h4 style="color : #ff6347;">SPK Dibatalkan</h4>
                                <p>Alasan: {{ $spk->cancel_reason }}</p>
                            </div>
                            <br>
                            @endif


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
                                    <td class="label">Waktu Terbit</td>
                                    <td>: {{ $spk->waktu_terbit_spk }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Waktu klik Mulai Kerja</td>
                                    <td>: {{ $spk->waktu_mulai_kerja }}</td>
                                </tr>


                                @if ($spk->status === 'Sudah Selesai' || $spk->status === 'Dalam Proses')


                                <tr>
                                    <td class="label">Durasi Waktu Tunggu</td>
                                    <td>: {{ $spk->durasi }}</td>
                                </tr>
                                </tr>
                                @endif

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
                    @if ($barangBaru->isNotEmpty() || $barangLama->isNotEmpty())
                    <h3>Product :</h3>
                    @endif

                    @if ($barangLama->isNotEmpty())
                    <!-- <h3>Barang Lama:</h3> -->
                    <div class="table-container">
                        <table class="table-barang-show w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th>Nama Product</th>
                                    <th style="width: 80px; text-align: center;">Jumlah (QTY)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangLama as $item)
                                <tr>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td style="text-align: center;">{{ $item->qty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if ($barangBaru->isNotEmpty())
                    <!-- <h3>Barang Baru:</h3> -->
                    <div class="table-container">
                        <table class="table-barang-show w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th>Nama Product</th>
                                    <th style="width: 80px; text-align: center;">Jumlah (QTY)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangBaru as $item)
                                <tr>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td style="text-align: center;">{{ $item->qty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    {{-- Tombol Edit Barang --}}
                    @if ($spk->status === 'Dalam Proses')
                    <br>
                    <a href="{{ route('spk.editBarang', ['spk_id' => $spk->id]) }}" class="btn btn-warning mb-4">
                        Edit Product
                    </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>