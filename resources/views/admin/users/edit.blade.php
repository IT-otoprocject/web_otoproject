<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                    <svg class="h-6 w-6 mr-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User: {{ $user->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update user information and permissions</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-md">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Details
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-md">
                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                    All Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium">Please fix the following errors:</h4>
                            <ul class="mt-1 text-sm list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-750 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="h-5 w-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Edit User Information
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update user details, password, and system access permissions</p>
                </div>

                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Basic Information
                        </h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>

                            <!-- Level -->
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Level</label>
                                <select name="level" id="level" required 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">Select User Level</option>
                                    <option value="admin" {{ old('level', $user->level) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="ceo" {{ old('level', $user->level) == 'ceo' ? 'selected' : '' }}>CEO</option>
                                    <option value="cfo" {{ old('level', $user->level) == 'cfo' ? 'selected' : '' }}>CFO</option>
                                    <option value="manager" {{ old('level', $user->level) == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="spv" {{ old('level', $user->level) == 'spv' ? 'selected' : '' }}>SPV</option>
                                    <option value="staff" {{ old('level', $user->level) == 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="headstore" {{ old('level', $user->level) == 'headstore' ? 'selected' : '' }}>Head Store</option>
                                    <option value="kasir" {{ old('level', $user->level) == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                    <option value="sales" {{ old('level', $user->level) == 'sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="mekanik" {{ old('level', $user->level) == 'mekanik' ? 'selected' : '' }}>Mekanik</option>
                                </select>
                            </div>

                            <!-- Divisi -->
                            <div>
                                <label for="divisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Divisi</label>
                                <select name="divisi" id="divisi" 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">Pilih Divisi</option>
                                    <option value="FACTORY" {{ old('divisi', $user->divisi) == 'FACTORY' ? 'selected' : '' }}>Factory</option>
                                    <option value="FAT" {{ old('divisi', $user->divisi) == 'FAT' ? 'selected' : '' }}>FAT</option>
                                    <option value="HCGA" {{ old('divisi', $user->divisi) == 'HCGA' ? 'selected' : '' }}>HCGA</option>
                                    <option value="RETAIL" {{ old('divisi', $user->divisi) == 'RETAIL' ? 'selected' : '' }}>Retail</option>
                                    <option value="PDCA" {{ old('divisi', $user->divisi) == 'PDCA' ? 'selected' : '' }}>PDCA</option>
                                    <option value="PURCHASING" {{ old('divisi', $user->divisi) == 'PURCHASING' ? 'selected' : '' }}>Purchasing</option>
                                    <option value="R&D" {{ old('divisi', $user->divisi) == 'R&D' ? 'selected' : '' }}>R&D</option>
                                    <option value="SALES" {{ old('divisi', $user->divisi) == 'SALES' ? 'selected' : '' }}>Sales</option>
                                    <option value="WAREHOUSE" {{ old('divisi', $user->divisi) == 'WAREHOUSE' ? 'selected' : '' }}>Warehouse</option>
                                    <option value="WAREHOUSE_SBY" {{ old('divisi', $user->divisi) == 'WAREHOUSE_SBY' ? 'selected' : '' }}>Warehouse Surabaya</option>
                                </select>
                            </div>
                            </div>

                            <!-- Garage -->
                            <div>
                                <label for="garage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Garage Assignment</label>
                                <select name="garage" id="garage" 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">Pilih Garage</option>
                                    <option value="Bandung" {{ old('garage', $user->garage) == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                    <option value="Bekasi" {{ old('garage', $user->garage) == 'Bekasi' ? 'selected' : '' }}>Bekasi</option>
                                    <option value="Bintaro" {{ old('garage', $user->garage) == 'Bintaro' ? 'selected' : '' }}>Bintaro</option>
                                    <option value="Cengkareng" {{ old('garage', $user->garage) == 'Cengkareng' ? 'selected' : '' }}>Cengkareng</option>
                                    <option value="Cibubur" {{ old('garage', $user->garage) == 'Cibubur' ? 'selected' : '' }}>Cibubur</option>
                                    <option value="Surabaya" {{ old('garage', $user->garage) == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- System Access -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.25-4.5a2.25 2.25 0 00-3.181 0l-7.5 7.5a2.25 2.25 0 003.181 3.181l7.5-7.5a2.25 2.25 0 000-3.181z"></path>
                            </svg>
                            System Access Permissions
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6 mb-6">
                            @foreach($availableModules as $key => $label)
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="access_{{ $key }}" name="system_access[]" type="checkbox" value="{{ $key }}" 
                                           @if(is_array(old('system_access', $user->system_access)) && in_array($key, old('system_access', $user->system_access))) checked @endif
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 transition-colors">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="access_{{ $key }}" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">{{ $label }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Quick Access Presets -->
                        <div class="border-t border-green-200 dark:border-green-800 pt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quick Access Presets</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="setAccess(['dashboard', 'user_management', 'pr', 'spk_management', 'inventory', 'reports', 'pr_reports', 'spk_reports', 'inventory_reports', 'settings'])" 
                                    class="px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-800 dark:text-red-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    Admin Access
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'pr', 'spk_management', 'reports', 'pr_reports', 'spk_reports'])" 
                                    class="px-3 py-2 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900 dark:hover:bg-purple-800 text-purple-800 dark:text-purple-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                    </svg>
                                    Manager Access
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'spk_management', 'spk_reports'])" 
                                    class="px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
                                    </svg>
                                    SPK Only
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'pr', 'pr_reports'])" 
                                    class="px-3 py-2 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900 dark:hover:bg-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Purchase Request Only
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'inventory'])" 
                                    class="px-3 py-2 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:hover:bg-green-800 text-green-800 dark:text-green-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 8a1 1 0 011-1h1V6a1 1 0 012 0v1h3V6a1 1 0 112 0v1h1a1 1 0 110 2H6a1 1 0 01-1-1z"></path>
                                        <path d="M2 10a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1z"></path>
                                        <path d="M2 14a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1z"></path>
                                    </svg>
                                    Inventory Only
                                </button>
                                <button type="button" onclick="setAccess(['dashboard'])" 
                                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                    </svg>
                                    Dashboard Only
                                </button>
                                <button type="button" onclick="clearAccess()" 
                                    class="px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-800 dark:text-red-200 rounded-lg text-xs font-medium transition-colors flex items-center">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.users.show', $user) }}" 
                            class="inline-flex items-center px-6 py-3 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg font-medium transition-all duration-200">
                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle level change untuk admin, CEO, dan CFO
        document.getElementById('level').addEventListener('change', function() {
            const divisiSelect = document.getElementById('divisi');
            const divisiLabel = divisiSelect.previousElementSibling;
            
            if (['admin', 'ceo', 'cfo'].includes(this.value)) {
                // Untuk admin, CEO, dan CFO, divisi tidak wajib
                divisiSelect.removeAttribute('required');
                if (this.value === 'admin') {
                    divisiLabel.textContent = 'Divisi (Optional untuk Admin)';
                } else if (this.value === 'ceo') {
                    divisiLabel.textContent = 'Divisi (Optional untuk CEO)';
                } else if (this.value === 'cfo') {
                    divisiLabel.textContent = 'Divisi (Optional untuk CFO)';
                }
                divisiSelect.style.borderColor = '#d1d5db'; // Normal border
            } else {
                // Untuk non-admin/CEO/CFO, divisi wajib
                divisiSelect.setAttribute('required', 'required');
                divisiLabel.textContent = 'Divisi';
                divisiSelect.style.borderColor = '#d1d5db'; // Normal border
            }
        });

        // Trigger saat halaman load untuk cek level yang sudah ada
        document.addEventListener('DOMContentLoaded', function() {
            const levelSelect = document.getElementById('level');
            if (['admin', 'ceo', 'cfo'].includes(levelSelect.value)) {
                const divisiSelect = document.getElementById('divisi');
                const divisiLabel = divisiSelect.previousElementSibling;
                divisiSelect.removeAttribute('required');
                if (levelSelect.value === 'admin') {
                    divisiLabel.textContent = 'Divisi (Optional untuk Admin)';
                } else if (levelSelect.value === 'ceo') {
                    divisiLabel.textContent = 'Divisi (Optional untuk CEO)';
                } else if (levelSelect.value === 'cfo') {
                    divisiLabel.textContent = 'Divisi (Optional untuk CFO)';
                }
            }
        });

        // Access management functions
        function setAccess(modules) {
            console.log('setAccess called with modules:', modules);
            
            // Clear all checkboxes first
            const checkboxes = document.querySelectorAll('input[name="system_access[]"]');
            console.log('Found checkboxes:', checkboxes.length);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                console.log('Cleared checkbox:', checkbox.id);
            });
            
            // Check selected modules
            modules.forEach(module => {
                const checkbox = document.getElementById('access_' + module);
                console.log('Looking for checkbox with ID: access_' + module, checkbox);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Checked checkbox:', checkbox.id);
                } else {
                    console.log('Checkbox not found for module:', module);
                }
            });
        }

        function clearAccess() {
            const checkboxes = document.querySelectorAll('input[name="system_access[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
        }
    </script>
</x-app-layout>
