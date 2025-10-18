<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Payment Methods</h2>
            <a href="{{ route('payment-methods.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="mb-4 flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/deskripsi" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <select name="status" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                    <button class="px-3 py-2 bg-gray-600 text-white rounded">Filter</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($methods as $m)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ $m->name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $m->description ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="px-2 py-1 text-xs rounded {{ $m->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">{{ $m->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="px-4 py-2 text-sm text-right">
                                    <a href="{{ route('payment-methods.edit', $m) }}" class="px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded">Edit</a>
                                    <button type="button" data-id="{{ $m->id }}" onclick="toggleStatus(this.dataset.id)" class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Toggle</button>
                                    <button type="button" data-id="{{ $m->id }}" onclick="confirmDelete(this.dataset.id)" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $methods->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>

    <script>
        async function toggleStatus(id){
            const res = await fetch(`{{ url('payment-methods') }}/${id}/toggle-status`, {method:'POST', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            if (res.ok) location.reload();
        }
        async function confirmDelete(id){
            if(!confirm('Hapus payment method ini?')) return;
            const res = await fetch(`{{ url('payment-methods') }}/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
            if (res.ok) location.reload();
        }
    </script>
</x-app-layout>
