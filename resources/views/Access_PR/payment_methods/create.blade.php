<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Payment Method</h2>
            <a href="{{ route('payment-methods.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('payment-methods.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Deskripsi</label>
                        <input type="text" name="description" value="{{ old('description') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="is_active" type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_active" class="text-sm">Active</label>
                    </div>
                    <div class="pt-2">
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
