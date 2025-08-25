<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 xl:max-w-[85%] 2xl:max-w-[1700px]">
        <div class="flex justify-between h-16 lg:h-20 items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-application-logo class="block h-9 lg:h-12 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:ms-10 lg:ms-12 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-base lg:text-lg">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if (Auth::check())
                        {{-- Admin Dashboard - hanya muncul di halaman admin --}}
                        @if (Auth::user()->level == 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-base lg:text-lg">
                                {{ __('Admin') }}
                            </x-nav-link>
                        @endif
                        
                        {{-- SPK Module Navigation - hanya muncul ketika sudah masuk ke modul SPK --}}
                        @hasAccess('spk_garage')
                            @if (request()->routeIs('spk.*') || request()->routeIs('report.spk.*') || request()->routeIs('kerja.*') || request()->routeIs('mekanik.spk.*'))
                                <x-nav-link :href="route('spk.daily')" :active="request()->routeIs('spk.daily')" class="text-base lg:text-lg">
                                    {{ __('Daily SPK') }}
                                </x-nav-link>
                                <x-nav-link :href="route('spk.index')" :active="request()->routeIs('spk.index', 'spk.create', 'spk.edit', 'spk.editBarang')" class="text-base lg:text-lg">
                                    {{ __('Daftar SPK') }}
                                </x-nav-link>
                                <x-nav-link :href="route('report.spk.index')" :active="request()->routeIs('report.spk.*')" class="text-base lg:text-lg">
                                    {{ __('Report') }}
                                </x-nav-link>
                            @endif
                        @endhasAccess
                        
                        {{-- PR Module Navigation - hanya muncul ketika sudah masuk ke modul PR --}}
                        @hasAccess('pr')
                            @if (request()->routeIs('pr.*'))
                                <x-nav-link href="#" class="text-base lg:text-lg cursor-not-allowed opacity-50">
                                    {{ __('Coming Soon') }}
                                </x-nav-link>
                            @endif
                        @endhasAccess
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if (Auth::check())
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 lg:px-4 lg:py-3 border border-transparent text-sm lg:text-base leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} - {{ auth()->user()->level }} {{ Auth::user()->garage }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 lg:h-5 lg:w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a 1 0 01-1.414 0l-4-4a1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (Auth::user()->level == 'admin')
                        <x-dropdown-link :href="route('profile.edit')" class="text-sm lg:text-base">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-sm lg:text-base">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div style="text-align:center; margin-top:20px;">
                    <span class="text-red-600 font-bold">Anda harus login</span><br>
                    <a href="{{ route('login') }}">
                        <button style="padding:8px 18px; font-size:15px; margin-top:8px;" class="bg-blue-600 text-white rounded">Login</button>
                    </a>
                </div>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 lg:p-3 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150">
                    <svg class="h-6 w-6 lg:h-8 lg:w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-base lg:text-lg">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if (Auth::check())
                {{-- Admin Dashboard - hanya muncul di halaman admin --}}
                @if (Auth::user()->level == 'admin' && request()->routeIs('admin.*'))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-base lg:text-lg">
                        {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>
                @endif
                
                {{-- SPK Module Navigation - hanya muncul ketika sudah masuk ke modul SPK --}}
                @hasAccess('spk_garage')
                    @if (request()->routeIs('spk.*') || request()->routeIs('report.spk.*') || request()->routeIs('kerja.*') || request()->routeIs('mekanik.spk.*'))
                        <x-responsive-nav-link :href="route('spk.daily')" :active="request()->routeIs('spk.daily')" class="text-base lg:text-lg">
                            {{ __('Daily SPK') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('spk.index')" :active="request()->routeIs('spk.index', 'spk.create', 'spk.edit', 'spk.editBarang')" class="text-base lg:text-lg">
                            {{ __('Daftar SPK') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('report.spk.index')" :active="request()->routeIs('report.spk.*')" class="text-base lg:text-lg">
                            {{ __('Report') }}
                        </x-responsive-nav-link>
                    @endif
                @endhasAccess
                
                {{-- PR Module Navigation - hanya muncul ketika sudah masuk ke modul PR --}}
                @hasAccess('pr')
                    @if (request()->routeIs('pr.*'))
                        <x-responsive-nav-link href="#" class="text-base lg:text-lg cursor-not-allowed opacity-50">
                            {{ __('Coming Soon') }}
                        </x-responsive-nav-link>
                    @endif
                @endhasAccess
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @if (Auth::check())
            <div class="px-4">
                <div class="font-medium text-base lg:text-lg text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm lg:text-base text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                @if (Auth::user()->level == 'admin')
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @endif
                @if (Auth::user()->level == 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')">
                    {{ __('Admin Dashboard') }}
                </x-responsive-nav-link>
                @endif
                @if (in_array(Auth::user()->level, ['admin', 'kasir', 'mekanik', 'sales']))
                <x-responsive-nav-link :href="route('spk.index')">
                    {{ __('Daftar SPK') }}
                </x-responsive-nav-link>
                @endif
                @if (in_array(Auth::user()->level, ['admin', 'headstore', 'manager']))
                <x-responsive-nav-link :href="route('report.spk.index')">
                    {{ __('Lihat Report SPK') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('report.spk.barang')">
                    {{ __('Export Rata-rata Barang') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('report.spk.mekanik_product')">
                    {{ __('Export Rata-rata Mekanik per Produk') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('report.spk.mekanik')">
                    {{ __('Export Rata-rata Kerja Mekanik') }}
                </x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-base lg:text-lg">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
            @else
            <div class="px-4 py-4 text-center">
                <span class="text-red-600 font-bold">Anda harus login</span><br>
                <a href="{{ route('login') }}">
                    <button style="padding:8px 18px; font-size:15px; margin-top:8px;" class="bg-blue-600 text-white rounded">Login</button>
                </a>
            </div>
            @endif
        </div>
    </div>
</nav>