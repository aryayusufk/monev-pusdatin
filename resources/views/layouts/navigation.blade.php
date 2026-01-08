<nav class="bg-bg-base-default border-r border-bor-base-default w-72 shrink-0 overflow-x-hidden">
    <!-- Primary Navigation Menu -->
    <div class="flex flex-col w-full h-full min-w-0">
        <div class="flex flex-col h-full justify-between">
            <div class="flex flex-col py-8 gap-4">
                <!-- Logo -->
                <div class="flex items-center justify-center">
                    <a href="#">
                        <x-application-logo class="block h-12 w-auto fill-current text-txt-base-default" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @php
                    $user = Auth::user();
                    $navBidangsFiltered = $navBidangs;

                    // Role berbasis bidang: hanya tampilkan bidang miliknya.
                    if ($user && $user->hasRole(['monev_bidang', 'user_bidang'])) {
                        $navBidangsFiltered = collect($navBidangs)->where('id', (int) $user->bidang_id);
                    }
                @endphp

                <div class="flex flex-col gap-2" x-data="{ openKonfigurasi: false, openLaporanBidang: false, openLaporanMonev: false }">

                    @can('reports.bidang.view')
                        <button type="button" @click="openLaporanBidang = !openLaporanBidang"
                            class="flex p-2 gap-2 text-center items-center justify-center text-base font-normal text-txt-base-default transition duration-150 ease-in-out">
                            <span>Laporan Bidang</span>
                            <x-heroicon-m-chevron-down class="w-[20px] h-[20px] transform transition"
                                x-bind:class="openLaporanBidang ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="openLaporanBidang" x-collapse class="flex flex-col gap-2 p-2">
                            @forelse ($navBidangsFiltered as $bidang)
                                <x-nav-link :href="route('reports.bidang.index', ['bidang_id' => $bidang->id])" :active="request()->routeIs('reports.bidang.index') &&
                                    (int) request('bidang_id') === $bidang->id">
                                    {{ $bidang->nama }}
                                </x-nav-link>
                            @empty
                                <div
                                    class="flex p-2 text-center items-center justify-center text-base font-normal text-acc-base-default">
                                    Belum ada bidang</div>
                            @endforelse
                        </div>
                    @endcan

                    @can('reports.monev.view')
                        <button type="button" @click="openLaporanMonev = !openLaporanMonev"
                            class="flex p-2 gap-2 text-center items-center justify-center text-base font-normal text-txt-base-default transition duration-150 ease-in-out">
                            <span>Laporan Monitoring & Evaluasi</span>
                            <x-heroicon-m-chevron-down class="w-[20px] h-[20px] transform transition"
                                x-bind:class="openLaporanMonev ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="openLaporanMonev" x-collapse class="flex flex-col gap-2 p-2">
                            @forelse ($navBidangsFiltered as $bidang)
                                <x-nav-link :href="route('reports.monev.index', ['bidang_id' => $bidang->id])" :active="request()->routeIs('reports.monev.index') &&
                                    (int) request('bidang_id') === $bidang->id">
                                    {{ $bidang->nama }}
                                </x-nav-link>
                            @empty
                                <div
                                    class="flex p-2 text-center items-center justify-center text-base font-normal text-acc-base-default">
                                    Belum ada bidang</div>
                            @endforelse
                        </div>
                    @endcan

                    @can('activities.create')
                        <x-nav-link :href="route('activities.create')" :active="request()->routeIs('activities.create')">
                            {{ __('Buat Laporan') }}
                        </x-nav-link>
                    @endcan

                    @can('activitylog.view')
                        <x-nav-link :href="route('logs.index')" :active="request()->routeIs('logs.index')">
                            {{ __('Catatan Aktivitas') }}
                        </x-nav-link>
                    @endcan

                    @if (Gate::any(['config.iku', 'config.users', 'config.bidang']))
                        <button type="button" @click="openKonfigurasi = !openKonfigurasi"
                            class="flex p-2 text-center items-center justify-center text-base font-normal text-txt-base-default transition duration-150 ease-in-out">
                            <span>Konfigurasi</span>
                            <x-heroicon-m-chevron-down class="w-[20px] h-[20px] transform transition"
                                x-bind:class="openKonfigurasi ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="openKonfigurasi" x-collapse class="flex flex-col gap-2 p-2">
                            @can('config.iku')
                                <x-nav-link :href="route('ikus.index')" :active="request()->routeIs('ikus.index')">
                                    {{ __('Manajemen IKU') }}
                                </x-nav-link>
                            @endcan

                            @can('config.users')
                                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                                    {{ __('Manajemen Pengguna') }}
                                </x-nav-link>
                            @endcan

                            @can('config.bidang')
                                <x-nav-link :href="route('bidangs.index')" :active="request()->routeIs('bidangs.index')">
                                    {{ __('Manajemen Bidang') }}
                                </x-nav-link>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="flex w-full items-center justify-center p-2">
                <x-dropdown align="top" width="w-full">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <x-heroicon-m-chevron-down class="w-[20px] h-[20px] transform transition" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
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
        </div>
    </div>
</nav>
