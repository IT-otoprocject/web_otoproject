<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-x2 text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail SPK') }}
        </h2>
    </x-slot>
    <!-- Popup Notification -->
    @if (session('message'))
    <div id="notifPopup" class="notif-popup">
        <p>{{ session('message') }}</p>
    </div>
    @endif

    <div class="py-8 lg:py-12">
        <div class="max-w-[95%] mx-auto sm:px-4 lg:px-8 xl:max-w-[85%] 2xl:max-w-[1700px]">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 lg:p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            @if ($spk->status === 'Baru Diterbitkan' && in_array(Auth::user()->level, ['admin', 'kasir']))
                            <button type="button" class="btn btn-danger" onclick="showCancelPopup()">Cancel</button>
                            @endif

                            @if ($spk->status === 'Dalam Proses' && in_array(Auth::user()->level, ['admin', 'kasir']))
                            <a href="{{ route('spk.edit', ['spk_id' => $spk->id]) }}"
                                class="btn btn-warning"
                                onclick="return confirm('Apakah Anda yakin ingin mengedit SPK ini?')">
                                Edit SPK
                            </a>
                            @endif
                        </div>

                        <div>
                            @if (in_array($spk->status, ['Baru Diterbitkan', 'Dalam Proses']) && Auth::user()->level === 'mekanik')
                            <form action="{{ route('spk.waktuMulaiKerja', ['spk_id' => $spk->id]) }}" method="POST" onsubmit="return startWorkConfirmation(event)">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    Mulai Kerja
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

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

                    {{-- Tabel Detail SPK --}}
                    <div class="table-container mb-4 lg:mb-6">
                        <table class="detail-table w-full lg:w-[95%] xl:w-[90%] mx-auto text-gray-900 dark:text-white">
                            <h2 class="text-gray-900 dark:text-white">{{ $spk->no_spk }}</h2>

                            @if ($spk->status === 'Cancel')
                            <div class="alert alert-danger">
                                <h4 style="color : #ff6347;">SPK Dibatalkan</h4>
                                <p class="text-gray-900 dark:text-white">Alasan: {{ $spk->cancel_reason }}</p>
                            </div>
                            <br>
                            @endif

                            <tbody>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="text-gray-900 dark:text-white">
                                        <h4 class="text-gray-900 dark:text-white">Detail SPK :</h4>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Garage</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->garage }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Tanggal</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->tanggal }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Teknisi 1</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->teknisi_1 }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Teknisi 2</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->teknisi_2 }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Customer</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->customer }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Alamat</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->alamat }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">No. HP</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->no_hp }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Jenis Mobil</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->jenis_mobil }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">No. Plat</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->no_plat }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Catatan</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->catatan }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Waktu Terbit</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->waktu_terbit_spk }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Waktu klik Mulai Kerja</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->waktu_mulai_kerja }}</span>
                                    </td>
                                </tr>

                                @if ($spk->status === 'Sudah Selesai' || $spk->status === 'Dalam Proses')
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Durasi Waktu Tunggu</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->durasi }}</span>
                                    </td>
                                </tr>
                                @endif

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label" style="border-bottom: 1px solid gray;">
                                        <span class="text-gray-900 dark:text-white">Status</span>
                                    </td>
                                    <td style="border-bottom: 1px solid gray;">
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->status }}</span>
                                    </td>
                                </tr>

                                @if ($spk->status === 'Sudah Selesai')
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <h4 class="text-gray-900 dark:text-white">Mekanik :</h4>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Waktu Kerja</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->waktu_kerja }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label">
                                        <span class="text-gray-900 dark:text-white">Catatan Kerja</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->catatan_kerja }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="label" style="border-bottom: 1px solid gray;">
                                        <span class="text-gray-900 dark:text-white">Akun Mekanik</span>
                                    </td>
                                    <td style="border-bottom: 1px solid gray;">
                                        <span class="text-gray-900 dark:text-white">: {{ $spk->teknisi_selesai }}</span>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabel Barang --}}
                    @if ($barangBaru->isNotEmpty() || $barangLama->isNotEmpty())
                    <table class="detail-table w-full lg:w-[95%] xl:w-[90%] mx-auto">
                        <td>
                            <h2 class="text-gray-900 dark:text-white">Product :</h2>
                        </td>
                    </table>
                    @endif

                    @if ($barangLama->isNotEmpty())
                    <div class="table-container mb-4">
                        <table class="table-barang-show w-full lg:w-[95%] xl:w-[90%] mx-auto">
                            <thead>
                                <tr>
                                    <th class="text-gray-900 dark:text-white">Nama Product</th>
                                    <th class="text-gray-900 dark:text-white" style="width: 80px; text-align: center;">Jumlah (QTY)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangLama as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="text-gray-900 dark:text-white">
                                        <span class="text-gray-900 dark:text-white">{{ $item->nama_barang }}</span>
                                    </td>
                                    <td class="text-gray-900 dark:text-white text-center">
                                        <span class="text-gray-900 dark:text-white">{{ $item->qty }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    @if ($barangBaru->isNotEmpty())
                    <div class="table-container">
                        <table class="table-barang-show w-full lg:w-[95%] xl:w-[90%] mx-auto">
                            <thead>
                                <tr>
                                    <th class="text-gray-900 dark:text-white">Nama Product</th>
                                    <th class="text-gray-900 dark:text-white" style="width: 80px; text-align: center;">Jumlah (QTY)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangBaru as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="text-gray-900 dark:text-white">
                                        <span class="text-gray-900 dark:text-white">{{ $item->nama_barang }}</span>
                                    </td>
                                    <td class="text-gray-900 dark:text-white text-center">
                                        <span class="text-gray-900 dark:text-white">{{ $item->qty }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- Tombol Edit Barang --}}
                    @if ($spk->status === 'Dalam Proses' && in_array(Auth::user()->level, ['admin', 'kasir']))
                    <br>
                    <div>
                        <a href="{{ route('spk.editBarang', ['spk_id' => $spk->id]) }}" class="btn btn-warning">
                            Edit Product
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>