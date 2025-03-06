<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kerja Mekanik') }}
        </h2>
    </x-slot>
    <div class="container">
        <h1>Kerja Mekanik</h1>
        <div id="timer">00:00:00</div>
        <form id="kerjaForm" action="{{ route('kerja.selesai', ['spk_id' => $spk->id]) }}" method="POST">
            @csrf
            <label for="notes">Catatan (Opsional):</label>
            <textarea id="notes" name="notes" rows="4" cols="50"></textarea>
            <input type="hidden" name="worked_time" id="worked_time">
            <button type="submit">Selesai Kerja</button>
        </form>
    </div>
    <script>
        let startTime = new Date().getTime();
        let interval = setInterval(() => {
            let now = new Date().getTime();
            let elapsedTime = now - startTime;
            let seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);
            let minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            let hours = Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            document.getElementById('timer').innerText = hours.toString().padStart(2, '0') + ':' +
                minutes.toString().padStart(2, '0') + ':' +
                seconds.toString().padStart(2, '0');
        }, 1000);

        document.getElementById('kerjaForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let elapsedTime = new Date().getTime() - startTime;
            let hours = Math.floor((elapsedTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);
            let workedTime = hours.toString().padStart(2, '0') + ':' +
                minutes.toString().padStart(2, '0') + ':' +
                seconds.toString().padStart(2, '0');
            // Pastikan input tersembunyi ada sebelum mengatur nilainya
            let workedTimeInput = document.getElementById('worked_time');
            if (workedTimeInput) {
                workedTimeInput.value = workedTime; // Mengatur nilai untuk input tersembunyi
            }

            this.submit(); // Mengirim formulir
        });
        console.log('Worked Time:', workedTime);
        console.log('Input Element:', workedTimeInput);
    </script>
</x-app-layout>