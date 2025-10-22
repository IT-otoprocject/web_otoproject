<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">Bulk Edit Users</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Apply changes to multiple users at once</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg">Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-xl border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.users.bulk-update') }}" method="POST">
                    @csrf
                    @foreach($users as $u)
                        <input type="hidden" name="ids[]" value="{{ $u->id }}">
                    @endforeach

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="apply[level]" value="1">
                                Level
                            </label>
                            <select name="level" class="mt-2 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700">
                                <option value="">-- Select Level --</option>
                                <option value="admin">Admin</option>
                                <option value="ceo">CEO</option>
                                <option value="cfo">CFO</option>
                                <option value="manager">Manager</option>
                                <option value="spv">SPV</option>
                                <option value="staff">Staff</option>
                                <option value="headstore">Head Store</option>
                                <option value="kasir">Kasir</option>
                                <option value="sales">Sales</option>
                                <option value="mekanik">Mekanik</option>
                            </select>
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="apply[divisi]" value="1">
                                Divisi
                            </label>
                            <input type="text" name="divisi" class="mt-2 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700" placeholder="e.g. HCGA, PURCHASING, FAT">
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="apply[garage]" value="1">
                                Garage
                            </label>
                            <input type="text" name="garage" class="mt-2 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700" placeholder="e.g. GARAGE-01">
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="apply[password]" value="1">
                                Set Password
                            </label>
                            <input type="text" name="password" class="mt-2 block w-full border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700" placeholder="New password for all selected">
                        </div>

                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="apply[system_access]" value="1">
                                System Access Permissions
                            </label>
                            <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($availableModules as $key => $label)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="system_access[]" value="{{ $key }}">
                                    {{ $label }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-300">Applying to {{ $users->count() }} users</div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Apply Changes</button>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Selected Users</h3>
                </div>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $u)
                    <li class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300 flex items-center justify-between">
                        <div>
                            <span class="font-medium">#{{ $u->id }}</span> {{ $u->name }} — {{ $u->email }}
                        </div>
                        <div class="text-xs text-gray-500">Level: {{ $u->level }} | Divisi: {{ $u->divisi }}</div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
