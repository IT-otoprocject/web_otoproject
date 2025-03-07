<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kerja Mekanik') }}
        </h2>
    </x-slot>
    <div class="container">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h1>Durasi Kerja Mekanik</h1>
                        <div id="timer">00:00:00</div>
                        <form id="kerjaForm" action="{{ route('kerja.selesai', ['spk_id' => $spk->id]) }}" method="POST">
                            @csrf
                            <label for="notes">Catatan (Opsional):</label>
                            <br>
                            <textarea id="notes" name="notes" rows="4" cols="50"></textarea>
                            <input type="hidden" name="worked_time" id="worked_time">
                            <br>
                            <button type="submit">Selesai Kerja</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Function to start the timer
        function startTimer() {
            // Check if start time is already in localStorage
            let startTime = localStorage.getItem('startTime');
            if (!startTime) {
                // If not, set the current time as the start time
                startTime = new Date().getTime();
                localStorage.setItem('startTime', startTime);
            } else {
                // If it is, convert it to a number
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