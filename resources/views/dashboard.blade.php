<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in! - test") }}
                    @if (Auth::user()->level == 'kasir')
                    <br>
                    <a href="{{ route('spk.create') }}" class="btn btn-primary">Buat SPK</a>
                    @endif
                    @if (Auth::user()->level == 'mekanik')
                    <br>
                    <a href="{{ route('mekanik.spk.index') }}" class="btn btn-primary">Daftar SPK Baru</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>