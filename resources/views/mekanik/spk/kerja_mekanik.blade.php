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
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800" style="width: 140px; text-align: center;">Waktu Pengerjaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barangLama as $item)
                                    <tr>
                                        <td class="custom-td !text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                                        <td class="custom-td !text-gray-900 dark:text-white text-center">{{ $item->qty }}</td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-pasang bg-blue-600 text-white"
                                                data-barang-id="{{ $item->id }}"
                                                data-barang-nama="{{ $item->nama_barang }}"
                                                data-barang-qty="{{ $item->qty }}">
                                                <span class="btn-label">Tandai Sudah Dipasang</span>
                                                <span class="btn-check hidden">&#10003;</span>
                                            </button>
                                        </td>
                                        <td class="custom-td text-center" data-waktu-pengerjaan>
                                            {{ $item->waktu_pengerjaan_barang ?? '-' }}
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
                                        <th class="border-barang px-4 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800" style="width: 140px; text-align: center;">Waktu Pengerjaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barangBaru as $item)
                                    <tr>
                                        <td class="custom-td !text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                                        <td class="custom-td !text-gray-900 dark:text-white text-center">{{ $item->qty }}</td>
                                        <td class="custom-td text-center">
                                            <button type="button" class="btn btn-pasang bg-blue-600 text-white"
                                                data-barang-id="{{ $item->id }}"
                                                data-barang-nama="{{ $item->nama_barang }}"
                                                data-barang-qty="{{ $item->qty }}">
                                                <span class="btn-label">Tandai Sudah Dipasang</span>
                                                <span class="btn-check hidden">&#10003;</span>
                                            </button>
                                        </td>
                                        <td class="custom-td text-center" data-waktu-pengerjaan>
                                            {{ $item->waktu_pengerjaan_barang ?? '-' }}
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

        // Checklist Product Dipasang berbasis nama produk dan qty
        document.addEventListener('DOMContentLoaded', function() {
            const spkId = "{{ $spk->id }}";
            const buttons = document.querySelectorAll('.btn-pasang');
            // Ambil semua nama barang dan qty yang ada di halaman
            const barangList = Array.from(buttons).map(btn => ({
                nama: btn.getAttribute('data-barang-nama'),
                qty: btn.getAttribute('data-barang-qty')
            }));
            const storageKey = `spk_${spkId}_barang_pasang_nama`;
            let checkedBarang = [];
            try {
                checkedBarang = JSON.parse(localStorage.getItem(storageKey)) || [];
            } catch (e) {
                checkedBarang = [];
            }

            // Cek perubahan qty: jika nama sama tapi qty berbeda, reset checklist dan beri notifikasi
            let changed = false;
            checkedBarang = checkedBarang.filter(saved => {
                const current = barangList.find(b => b.nama === saved.nama);
                if (current) {
                    if (String(current.qty) !== String(saved.qty)) {
                        // Qty berubah, beri notifikasi
                        alert(`qty dari product ${saved.nama} berubah menjadi ${current.qty}`);
                        changed = true;
                        return false; // hapus dari checklist
                    }
                    return true;
                }
                return false;
            });
            if (changed) {
                localStorage.setItem(storageKey, JSON.stringify(checkedBarang));
            }

            // Render tombol sesuai status
            buttons.forEach(function(btn) {
                const barangNama = btn.getAttribute('data-barang-nama');
                const barangQty = btn.getAttribute('data-barang-qty');
                const barangId = btn.getAttribute('data-barang-id');
                const isChecked = checkedBarang.some(b => b.nama === barangNama && String(b.qty) === String(barangQty));
                if (isChecked) {
                    btn.classList.add('bg-blue-600', 'text-white');
                    btn.querySelector('.btn-label').classList.add('hidden');
                    btn.querySelector('.btn-check').classList.remove('hidden');
                    btn.disabled = true;
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.querySelector('.btn-label').classList.remove('hidden');
                    btn.querySelector('.btn-check').classList.add('hidden');
                    btn.disabled = false;
                }
                btn.addEventListener('click', function() {
                    if (!checkedBarang.some(b => b.nama === barangNama && String(b.qty) === String(barangQty))) {
                        if (confirm(`apakah benar product ${barangNama} dengan qty ${barangQty} sudah terpasang?`)) {
                            checkedBarang.push({ nama: barangNama, qty: barangQty });
                            localStorage.setItem(storageKey, JSON.stringify(checkedBarang));
                            btn.classList.add('bg-blue-600', 'text-white');
                            btn.querySelector('.btn-label').classList.add('hidden');
                            btn.querySelector('.btn-check').classList.remove('hidden');
                            btn.disabled = true;

                            // Ambil waktu dari timer
                            let timerText = document.getElementById('timer').innerText.trim(); // format: HH:mm:ss
                            // Kirim waktu pengerjaan barang via AJAX
                            fetch("{{ route('spk.item.waktu_pengerjaan') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    item_id: barangId,
                                    waktu_pengerjaan_barang: timerText
                                })
                            }).then(res => res.json())
                            .then(data => {
                                // Optional: tampilkan notifikasi jika perlu
                                // alert(data.message);
                            });
                        }
                    }
                });
            });

            // Restore catatan (notes) dari localStorage jika ada
            const notesInput = document.getElementById('notes');
            if (notesInput) {
                const savedNotes = localStorage.getItem('spk_notes_{{ $spk->id }}');
                if (savedNotes !== null) {
                    notesInput.value = savedNotes;
                }
                notesInput.addEventListener('input', function() {
                    localStorage.setItem('spk_notes_{{ $spk->id }}', notesInput.value);
                });
            }
        });

        // Function to handle form submission
        function handleFormSubmit(event) {
            // Konfirmasi sebelum submit
            if (!confirm('Apakah Semua Perkerjaan Sudah Selesai?')) {
                event.preventDefault();
                return false;
            }

            // Versi dinamis (JS-only, lebih baik):
            let waktuCells = document.querySelectorAll('td[data-waktu-pengerjaan]');
            let allDone = Array.from(waktuCells).every(td => td.innerText.trim() !== '-');

            if (!allDone) {
                alert('Masih ada product belum dipasang Silahkan di cek kembali');
                event.preventDefault();
                return false;
            }

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

        // Refresh halaman setiap 5 detik, pastikan catatan tetap tersimpan
        setInterval(function() {
            // Simpan catatan sebelum reload (jaga-jaga jika ada perubahan terakhir)
            const notesInput = document.getElementById('notes');
            if (notesInput) {
                localStorage.setItem('spk_notes_{{ $spk->id }}', notesInput.value);
            }
            location.reload();
        }, 10000);
    </script>
</x-app-layout>