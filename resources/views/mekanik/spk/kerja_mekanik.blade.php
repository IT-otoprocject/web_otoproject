<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kerja Mekanik') }}
        </h2>
    </x-slot>
    <!-- Popup Notification -->
    @if (session('message'))
    <div id="notifPopup" class="notif-popup">
        <p>{{ session('message') }}</p>
    </div>
    @endif


    <div class="py-12 flex justify-center items-center">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100 text-center">
                    <!-- Judul -->
                    <h1 class="text-3xl font-bold mb-6">Durasi Kerja Mekanik</h1>

                    <!-- Timer -->
                    <div id="timer" class="text-4xl font-semibold mb-8">00:00:00</div>

                    <!-- Daftar Produk -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Product</h2>
                        @php
                            $barangLama = $spk->items()->where('is_new', false)->get();
                            $barangBaru = $spk->items()->where('is_new', true)->get();
                        @endphp
                        @if ($barangLama->isNotEmpty())
                        <div class="mb-4">
                            <table class="table-barang w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800">Nama Product</th>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800" style="width: 80px; text-align: center;">Jumlah (QTY)</th>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800" style="width: 120px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barangLama as $item)
                                    <tr>
                                        <td class="custom-td !text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                                        <td class="custom-td !text-gray-900 dark:text-white text-center">{{ $item->qty }}</td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-pasang bg-blue-600 text-white" data-barang-nama="{{ $item->nama_barang }}" data-barang-qty="{{ $item->qty }}">
                                                <span class="btn-label">Tandai Sudah Dipasang</span>
                                                <span class="btn-check hidden">&#10003;</span>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                        @if ($barangBaru->isNotEmpty())
                        <div>
                            <table class="table-barang w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800">Nama Product</th>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800" style="width: 80px; text-align: center;">Jumlah (QTY)</th>
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800" style="width: 120px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barangBaru as $item)
                                    <tr>
                                        <td class="custom-td !text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                                        <td class="custom-td !text-gray-900 dark:text-white text-center">{{ $item->qty }}</td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-pasang bg-blue-600 text-white" data-barang-nama="{{ $item->nama_barang }}" data-barang-qty="{{ $item->qty }}">
                                                <span class="btn-label">Tandai Sudah Dipasang</span>
                                                <span class="btn-check hidden">&#10003;</span>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>

                    <!-- Form -->
                    <form id="kerjaForm" action="{{ route('kerja.selesai', ['spk_id' => $spk->id]) }}" method="POST" class="space-y-6">
                        @csrf
                        <!-- Catatan -->
                        <div>
                            <label for="notes" class="text-lg font-medium">Catatan (Opsional):</label>
                            <br>
                            <textarea id="notes" name="notes" rows="10" cols="100" class="mt-2 w-full max-w-3xl p-6 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-blue-300"></textarea>

                        </div>

                        <!-- Hidden Input -->
                        <input type="hidden" name="worked_time" id="worked_time">

                        <!-- Submit Button -->
                        <div class="flex justify-center">
                            <button type="submit" class="btn btn-primary text-lg px-6 py-3">
                                Selesai Kerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to start the timer
        function startTimer() {
            let currentSPKId = "{{ $spk->id }}"; // Ambil SPK ID dari server
            let storedSPKId = localStorage.getItem('currentSPKId');

            // Jika SPK ID berubah, reset timer
            if (storedSPKId !== currentSPKId) {
                localStorage.setItem('currentSPKId', currentSPKId);
                localStorage.removeItem('startTime'); // Hapus waktu mulai sebelumnya
            }

            // Check if start time is already in localStorage
            let startTime = localStorage.getItem('startTime');
            if (!startTime) {
                // Set the current time as the start time
                startTime = new Date().getTime();
                localStorage.setItem('startTime', startTime);
            } else {
                startTime = parseInt(startTime, 10);
            }

            // Set interval to update the timer every second
            setInterval(() => {
                let now = new Date().getTime();
                let elapsedTime = now - startTime;
                let seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);
                let minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
                let hours = Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                document.getElementById('timer').innerText = hours.toString().padStart(2, '0') + ':' +
                    minutes.toString().padStart(2, '0') + ':' +
                    seconds.toString().padStart(2, '0');
            }, 1000);
        }

        // Checklist Product Dipasang berbasis nama produk
        document.addEventListener('DOMContentLoaded', function() {
            const spkId = "{{ $spk->id }}";
            const buttons = document.querySelectorAll('.btn-pasang');
            // Ambil semua nama barang yang ada di halaman
            const barangNames = Array.from(buttons).map(btn => btn.getAttribute('data-barang-nama'));
            const storageKey = `spk_${spkId}_barang_pasang_nama`;
            let checkedBarang = [];
            try {
                checkedBarang = JSON.parse(localStorage.getItem(storageKey)) || [];
            } catch (e) {
                checkedBarang = [];
            }

            // Filter hanya nama yang masih ada di halaman
            const filteredChecked = checkedBarang.filter(nama => barangNames.includes(nama));
            if (filteredChecked.length !== checkedBarang.length) {
                localStorage.setItem(storageKey, JSON.stringify(filteredChecked));
                checkedBarang = filteredChecked;
            }

            // Render tombol sesuai status
            buttons.forEach(function(btn) {
                const barangNama = btn.getAttribute('data-barang-nama');
                const barangQty = btn.getAttribute('data-barang-qty');
                if (checkedBarang.includes(barangNama)) {
                    btn.classList.add('bg-blue-600', 'text-white');
                    btn.querySelector('.btn-label').classList.add('hidden');
                    btn.querySelector('.btn-check').classList.remove('hidden');
                    btn.disabled = true;
                }
                btn.addEventListener('click', function() {
                    if (!checkedBarang.includes(barangNama)) {
                        if (confirm(`apakah benar product ${barangNama} dengan qty ${barangQty} sudah terpasang?`)) {
                            checkedBarang.push(barangNama);
                            localStorage.setItem(storageKey, JSON.stringify(checkedBarang));
                            btn.classList.add('bg-blue-600', 'text-white');
                            btn.querySelector('.btn-label').classList.add('hidden');
                            btn.querySelector('.btn-check').classList.remove('hidden');
                            btn.disabled = true;
                        }
                    }
                });
            });
        });

        // Function to handle form submission
        function handleFormSubmit(event) {
            event.preventDefault();
            let startTime = parseInt(localStorage.getItem('startTime'), 10);
            let now = new Date().getTime();
            let elapsedTime = now - startTime;
            let hours = Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);
            let workedTime = hours.toString().padStart(2, '0') + ':' +
                minutes.toString().padStart(2, '0') + ':' +
                seconds.toString().padStart(2, '0');

            // Set the worked time in the hidden input field
            let workedTimeInput = document.getElementById('worked_time');
            if (workedTimeInput) {
                workedTimeInput.value = workedTime;
            }

            // Submit the form
            event.target.submit();
        }

        // Start the timer when the page loads
        window.onload = startTimer;

        // Add event listener for form submission
        document.getElementById('kerjaForm').addEventListener('submit', handleFormSubmit);
    </script>
</x-app-layout>