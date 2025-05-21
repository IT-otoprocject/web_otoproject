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