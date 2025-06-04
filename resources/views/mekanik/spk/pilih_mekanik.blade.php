<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mekanik SPK') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-4">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form action="{{ route('spk.items.assignMekanik', $spk->id) }}" method="POST">
                    @csrf
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-6">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Pilih Mekanik</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($spk->items as $item)
                            <tr>
                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $item->nama_barang }}</td>
                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $item->qty }}</td>
                                <td class="px-4 py-2">
                                    <select name="mekanik[{{ $item->id }}]" class="form-select w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required>
                                        <option value="" class="dark:bg-gray-700 dark:text-gray-100">-- Pilih Mekanik --</option>
                                        @foreach($mekaniks as $mekanik)
                                            <option value="{{ $mekanik->id }}"
                                                @if(isset($item->mekanik_id) && $item->mekanik_id == $mekanik->id) selected @endif
                                                class="dark:bg-gray-700 dark:text-gray-100"
                                            >
                                                {{ $mekanik->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Simpan Mekanik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>