<nav x-data="{ open: false }" class="relative bg-white dark:bg-mintpad-500 border-b border-mintpad-200 dark:border-mintpad-900 p-2">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center">
                    @include('partials.logo')
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 lg:space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard.index')" :active="request()->routeIs('admin.dashboard.*')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.collections.index')" :active="request()->routeIs('admin.collections.*')">
                        {{ __('Collections') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Users') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.import.index')" :active="request()->routeIs('admin.import.*')">
                        {{ __('Import') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.upcoming.index')" :active="request()->routeIs('admin.upcoming.*')">
                        {{ __('Upcoming') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.status.index')" :active="request()->routeIs('admin.status.*')">
                        {{ __('Status') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-x-4">
                <dark-mode></dark-mode>

                <dropdown refname="dropdown-2">
                    <template v-slot:button>
                        <button class="flex items-center text-mintpad-700 dark:text-gray-200 text-sm font-medium hover:border-gray-300 hover:text-mintpad-300 focus:outline-none transition duration-150 ease-in-out">
                            <div><i class="fas fa-user-circle text-3xl"></i></div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </template>
                    <template v-slot:links>
                        @if (Auth::user())
                            <div class="px-4 py-2 text-xs border-b border-gray-200 dark:border-mintpad-900">
                                <div class="text-sm text-mintpad-700 dark:text-white">{{ Auth::user()->name }}</div>
                            </div>
                        @endif

                        <dropdown-link href="{{ route('collections.index') }}">
                            {{ __('User panel') }}
                        </dropdown-link>
                        <dropdown-link href="{{ route('users.profile') }}">
                            {{ __('My profile') }}
                        </dropdown-link>
                        <dropdown-link href="{{ route('users.2fa') }}">
                            {{ __('2fa settings') }}
                        </dropdown-link>
                        <dropdown-link href="{{ route('users.invoices') }}">
                            {{ __('Invoices') }}
                        </dropdown-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" class="rounded-b-md bg-gray-100 dark:bg-mintpad-700">
                            @csrf
                            <dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </dropdown-link>
                        </form>
                    </template>
                </dropdown>
            </div>
        </div>

        <hamburger-menu>
            <template v-slot:main>
                <hamburger-menu-link href="{{ route('admin.dashboard.index') }}" active="{{ request()->routeIs('admin.dashboard.*') }}">
                    {{ __('Dashboard') }}
                </hamburger-menu-link>
                <hamburger-menu-link href="{{ route('admin.collections.index') }}" active="{{ request()->routeIs('admin.collections.*') }}">
                    {{ __('Collections') }}
                </hamburger-menu-link>
                <hamburger-menu-link href="{{ route('admin.users.index') }}" active="{{ request()->routeIs('admin.users.*') }}">
                    {{ __('Users') }}
                </hamburger-menu-link>
                <hamburger-menu-link href="{{ route('admin.import.index') }}" active="{{ request()->routeIs('admin.import.*') }}">
                    {{ __('Import') }}
                </hamburger-menu-link>
                <hamburger-menu-link href="{{ route('admin.upcoming.index') }}" active="{{ request()->routeIs('admin.upcoming.*') }}">
                    {{ __('Upcoming') }}
                </hamburger-menu-link>
                <hamburger-menu-link href="{{ route('admin.status.index') }}" active="{{ request()->routeIs('admin.status.*') }}">
                    {{ __('Status') }}
                </hamburger-menu-link>
            </template>
            <template v-slot:settings>
                @if (Auth::user())
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                    </div>
                @endif

                <div class="mt-3 space-y-1">
                    <hamburger-menu-link href="{{ route('collections.index') }}">
                        {{ __('User panel') }}
                    </hamburger-menu-link>
                    <hamburger-menu-link href="{{ route('users.profile') }}">
                        {{ __('My profile') }}
                    </hamburger-menu-link>
                    <hamburger-menu-link href="{{ route('users.2fa') }}">
                        {{ __('2fa settings') }}
                    </hamburger-menu-link>
                    <hamburger-menu-link href="{{ route('users.invoices') }}">
                        {{ __('Invoices') }}
                    </hamburger-menu-link>
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <hamburger-menu-link href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </hamburger-menu-link>
                    </form>
                </div>
            </template>
        </hamburger-menu>
    </div>
</nav>
