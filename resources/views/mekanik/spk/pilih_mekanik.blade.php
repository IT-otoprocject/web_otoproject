<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-x2 text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mekanik SPK') }}
        </h2>
    </x-slot>

    <form action="{{ route('spk.items.assignMekanik', $spk->id) }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Pilih Mekanik</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spk->items as $item)
                <tr>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>
                        <select name="mekanik[{{ $item->id }}]" class="form-control" required>
                            <option value="">-- Pilih Mekanik --</option>
                            @foreach($mekaniks as $mekanik)
                                <option value="{{ $mekanik->id }}"
                                    @if(isset($item->mekanik_id) && $item->mekanik_id == $mekanik->id) selected @endif>
                                    {{ $mekanik->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Simpan Mekanik</button>
    </form>

</x-app-layout>