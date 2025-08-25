<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center shadow-md mr-3">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    Create New User
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new user to the system</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-md">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900 dark:to-pink-900 border border-red-200 dark:border-red-700 rounded-lg p-4 shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">There were some errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-xl border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="px-6 py-6">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                            </div>

                            <!-- Level -->
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Level</label>
                                <select name="level" id="level" required 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">Select User Level</option>
                                    <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ old('level') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="kasir" {{ old('level') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                    <option value="mekanik" {{ old('level') == 'mekanik' ? 'selected' : '' }}>Mekanik</option>
                                    <option value="pr_user" {{ old('level') == 'pr_user' ? 'selected' : '' }}>PR User</option>
                                </select>
                            </div>

                            <!-- Garage -->
                            <div>
                                <label for="garage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Garage Assignment</label>
                                <select name="garage" id="garage" 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                    <option value="">Pilih Garage</option>
                                    <option value="Bandung" {{ old('garage') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                    <option value="Bekasi" {{ old('garage') == 'Bekasi' ? 'selected' : '' }}>Bekasi</option>
                                    <option value="Bintaro" {{ old('garage') == 'Bintaro' ? 'selected' : '' }}>Bintaro</option>
                                    <option value="Cengkareng" {{ old('garage') == 'Cengkareng' ? 'selected' : '' }}>Cengkareng</option>
                                    <option value="Cibubur" {{ old('garage') == 'Cibubur' ? 'selected' : '' }}>Cibubur</option>
                                    <option value="Surabaya" {{ old('garage') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="px-6 py-6 bg-yellow-50 dark:bg-yellow-900/20 border-t border-yellow-200 dark:border-yellow-800">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Password Setup
                        </h4>
                        
                        <div class="mb-4 flex justify-between items-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Set a secure password for the new user</p>
                            <button type="button" onclick="generateRandomPassword()" 
                                class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Generate Random
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required 
                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors pr-10">
                                    <button type="button" onclick="togglePasswordVisibility('password')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors pr-10">
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Access Section -->
                    <div class="px-6 py-6 bg-green-50 dark:bg-green-900/20 border-t border-green-200 dark:border-green-800">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.25-4.5a2.25 2.25 0 00-3.181 0l-7.5 7.5a2.25 2.25 0 003.181 3.181l7.5-7.5a2.25 2.25 0 000-3.181z"></path>
                            </svg>
                            System Access Permissions
                        </h4>
                        
                        <!-- Quick Access Presets -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Quick presets:</p>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="setAccess(['dashboard', 'spk_garage', 'pr', 'reports', 'users', 'settings'])" 
                                    class="px-3 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-800 dark:text-red-200 text-sm rounded-full transition-colors">
                                    All Access
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'spk_garage'])" 
                                    class="px-3 py-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-800 dark:text-blue-200 text-sm rounded-full transition-colors">
                                    Basic Access
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'spk_garage', 'reports'])" 
                                    class="px-3 py-1 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:hover:bg-green-800 text-green-800 dark:text-green-200 text-sm rounded-full transition-colors">
                                    SPK Only
                                </button>
                                <button type="button" onclick="setAccess(['dashboard', 'pr', 'reports'])" 
                                    class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900 dark:hover:bg-yellow-800 text-yellow-800 dark:text-yellow-200 text-sm rounded-full transition-colors">
                                    PR Only
                                </button>
                                <button type="button" onclick="clearAccess()" 
                                    class="px-3 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-full transition-colors">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Individual Access Controls -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @php
                                $modules = [
                                    'dashboard' => ['name' => 'Dashboard', 'icon' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z'],
                                    'spk_garage' => ['name' => 'SPK Garage', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                    'pr' => ['name' => 'Purchase Request', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M8 11v2a4 4 0 008 0v-2m-4 6h.01'],
                                    'reports' => ['name' => 'Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                                    'users' => ['name' => 'User Management', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                                    'settings' => ['name' => 'Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z']
                                ];
                            @endphp

                            @foreach($modules as $key => $module)
                                <div class="relative flex items-center">
                                    <div class="flex items-center h-5">
                                        <input id="access_{{ $key }}" name="system_access[]" value="{{ $key }}" type="checkbox" 
                                            {{ in_array($key, old('system_access', [])) ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded transition-colors">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="access_{{ $key }}" class="font-medium text-gray-700 dark:text-gray-300 flex items-center cursor-pointer">
                                            <svg class="h-4 w-4 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                                            </svg>
                                            {{ $module['name'] }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const button = event.target.closest('button');
            
            if (input.type === 'password') {
                input.type = 'text';
                button.innerHTML = `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                </svg>`;
            } else {
                input.type = 'password';
                button.innerHTML = `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>`;
            }
        }

        // Generate random password function
        function generateRandomPassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let password = "";
            
            // Ensure at least one character from each category
            const lowercase = "abcdefghijklmnopqrstuvwxyz";
            const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            const numbers = "0123456789";
            const symbols = "!@#$%^&*";
            
            password += lowercase[Math.floor(Math.random() * lowercase.length)];
            password += uppercase[Math.floor(Math.random() * uppercase.length)];
            password += numbers[Math.floor(Math.random() * numbers.length)];
            password += symbols[Math.floor(Math.random() * symbols.length)];
            
            // Fill the rest randomly
            for (let i = password.length; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }
            
            // Shuffle the password
            password = password.split('').sort(() => Math.random() - 0.5).join('');
            
            // Set the password fields
            document.getElementById('password').value = password;
            document.getElementById('password_confirmation').value = password;
            
            // Show password temporarily
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            passwordInput.type = 'text';
            confirmInput.type = 'text';
            
            // Show success message
            showNotification('Random password generated successfully!', 'success');
            
            // Hide password after 3 seconds
            setTimeout(() => {
                passwordInput.type = 'password';
                confirmInput.type = 'password';
            }, 3000);
        }

        // Access management functions
        function setAccess(modules) {
            // Clear all checkboxes first
            const checkboxes = document.querySelectorAll('input[name="system_access[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            
            // Check selected modules
            modules.forEach(module => {
                const checkbox = document.getElementById('access_' + module);
                if (checkbox) checkbox.checked = true;
            });
        }

        function clearAccess() {
            const checkboxes = document.querySelectorAll('input[name="system_access[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
        }

        // Show notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Slide in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Slide out and remove
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</x-app-layout>
