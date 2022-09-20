<nav x-data="{ open: false }" class="bg-white dark:bg-slate-900 border-b-2 border-mintpad-200 pb-2">
    <!-- Admin bar -->
    <div class="w-full bg-primary-600 text-white text-center text-sm">
        <div class="max-w-7xl mx-auto py-1 px-4 sm:px-6 lg:px-8">
            <p>You are logged in as admin</p>
        </div>
    </div>

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center text-4xl font-jpegdev">
                    <a href="{{ route('admin.collections.index') }}">
                        mintpad
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 lg:space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard.index')" :active="request()->routeIs('admin.dashboard.*')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.collections.index')" :active="request()->routeIs('admin.collections.*')">
                        {{ __('Collections') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-mintpad-300 text-sm font-medium hover:border-gray-300 hover:text-mintpad-300 focus:outline-none transition duration-150 ease-in-out">
                            <div><i class="fas fa-user-circle text-3xl"></i></div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('collections.index')">
                            {{ __('User panel') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('users.profile')">
                            {{ __('My profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('users.invoices')">
                            {{ __('Invoices') }}
                        </x-dropdown-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200 bg-gray-100">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-mintpad-300 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-mintpad-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
            <x-responsive-nav-link :href="route('admin.dashboard.index')" :active="request()->routeIs('admin.dashboard.*')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.collections.index')" :active="request()->routeIs('admin.collections.*')">
                {{ __('Collections') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-mintpad-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('collections.index')">
                    {{ __('User panel') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('users.profile')">
                    {{ __('My profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('users.invoices')">
                    {{ __('Invoices') }}
                </x-responsive-nav-link>
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
