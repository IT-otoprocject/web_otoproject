<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar SPK') }}
            <a href="{{ route('spk.create') }}" class="btn btn-primary float-end">Buat SPK Baru</a>
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

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <!-- Tombol Filter -->
                            <button type="button" id="openFilter" class="btn btn-secondary">
                                <i class="fas fa-filter"></i> Filter
                            </button>


                            <!-- Search Bar -->
                            <form method="GET" action="{{ route('spk.index') }}" class="search-bar">
                                <input type="text" name="search" class="form-control" placeholder="Cari Customer, No. HP, atau No. Plat" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </form>
                        </div>



                        <!-- Popup Filter -->
                        <div id="filterPopup" class="filter-popup d-none">
                            <div class="filter-content">
                                <!-- Tombol "X" untuk menutup popup -->
                                <button type="button" id="closePopup" class="close-button">X</button>

                                <h5 class="mb-3"><i class="fas fa-filter"></i> Filter SPK</h5>
                                <form method="GET" action="{{ route('spk.index') }}">
                                    <!-- Dropdown Garage -->
                                    <div class="form-group">
                                        <label for="garage">Garage:</label>
                                        <br>
                                        <select name="garage" id="garage" class="form-control">
                                            <option value="" selected>-- Pilih Garage --</option>
                                            <option value="Bandung" {{ request('garage') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                            <option value="Bekasi" {{ request('garage') == 'Bekasi' ? 'selected' : '' }}>Bekasi</option>
                                            <option value="Bintaro" {{ request('garage') == 'Bintaro' ? 'selected' : '' }}>Bintaro</option>
                                            <option value="Cengkareng" {{ request('garage') == 'Cengkareng' ? 'selected' : '' }}>Cengkareng</option>
                                            <option value="Cibubur" {{ request('garage') == 'Cibubur' ? 'selected' : '' }}>Cibubur</option>
                                            <option value="Surabaya" {{ request('garage') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                        </select>
                                    </div>

                                    <!-- Input Tanggal -->
                                    <div class="form-group mt-3">
                                        <label for="tanggal_mulai">Tanggal Mulai:</label>
                                        <br>
                                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="tanggal_selesai">Tanggal Selesai:</label>
                                        <br>
                                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                                    </div>


                                    <!-- Dropdown Status -->
                                    <div class="form-group mt-3">
                                        <label for="status">Status:</label>
                                        <br>
                                        <select name="status" id="status" class="form-control">
                                            <option value="" selected>-- Pilih Status --</option>
                                            <option value="Baru Diterbitkan" {{ request('status') == 'Baru Diterbitkan' ? 'selected' : '' }}>Baru Diterbitkan</option>
                                            <option value="Dalam Proses" {{ request('status') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                                            <option value="Sudah Selesai" {{ request('status') == 'Sudah Selesai' ? 'selected' : '' }}>Sudah Selesai</option>
                                        </select>
                                    </div>

                                    <!-- Tombol Aksi -->
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        <a href="{{ route('spk.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tabel Data SPK -->
                        @if ($spks->isNotEmpty())
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No. SPK</th>
                                    <th>Garage</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>No. HP</th>
                                    <th>No. Plat</th>
                                    <th>Status</th>
                                    <th>Durasi</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($spks as $spk)
                                <tr>
                                    <td>{{ $spk->no_spk }}</td>
                                    <td>{{ $spk->garage }}</td>
                                    <td>{{ $spk->tanggal }}</td>
                                    <td>{{ $spk->customer }}</td>
                                    <td>{{ $spk->no_hp }}</td>
                                    <td>{{ $spk->no_plat }}</td>
                                    <td class="status-cell {{ strtolower(str_replace(' ', '-', $spk->status)) }}"
                                        style="text-align: center; font-size: 0.85rem; padding: 4px 10px; border-radius: 0px; border: 1px solid #ccc;">
                                        {{ $spk->status }}
                                    </td>


                                    <td>{{ $spk->waktu_kerja }}</td>
                                    <td><a href="{{ route('mekanik.spk.show', $spk->id) }}">Lihat Detail</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @else
                        <p class="text-center">Tidak ada data yang sesuai dengan pencarian.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSS -->
    <style>

    </style>

    <!-- Script -->
    <script>
        document.getElementById('openFilter').addEventListener('click', function() {
            document.getElementById('filterPopup').classList.remove('d-none');
        });

        document.getElementById('closePopup').addEventListener('click', function() {
            document.getElementById('filterPopup').classList.add('d-none');
        });
    </script>
</x-app-layout>