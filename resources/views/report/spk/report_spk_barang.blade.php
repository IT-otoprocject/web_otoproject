<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report SPK') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> -->
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Export Rata-rata Waktu Pengerjaan Barang per SKU</h5>
                            <form method="GET" action="{{ route('report.spk.export_avg_barang') }}" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <label for="tanggal_mulai" class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    </div>
                                    <div class="col-span-1">
                                        <label for="tanggal_akhir" class="block text-sm font-medium mb-1">Tanggal Akhir</label>
                                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Export ke Excel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const startDate = document.getElementById('tanggal_mulai');
            const endDate = document.getElementById('tanggal_akhir');

            form.addEventListener('submit', function(e) {
                if (new Date(endDate.value) < new Date(startDate.value)) {
                    e.preventDefault();
                    alert('Tanggal Akhir tidak boleh lebih awal dari Tanggal Mulai');
                }
            });

            // Set max date for both inputs to today
            const today = new Date().toISOString().split('T')[0];
            startDate.setAttribute('max', today);
            endDate.setAttribute('max', today);

            // Update min date of end_date when start_date changes
            startDate.addEventListener('change', function() {
                endDate.setAttribute('min', this.value);
            });
        });
    </script>
    @endpush
</x-app-layout>
