@php
    $roleId = (int) session('active_role_id');
    $dashboardRoute = \App\Models\User::getDashboardRoute($roleId) ?? 'dashboard';

    // mapping id role bos(1) sampe ketum(7)
    $menus = [
        [
            'route' => $dashboardRoute,
            'icon' => 'layout-dashboard',
            'label' => 'Dashboard',
            'roles' => [1, 2, 3, 4, 5, 6, 7],
            'badges' => [2 => 'Full', 1 => 'Summary', 3 => 'Trouble List', 4 => 'Status', 5 => 'Room View', 6 => 'My PC', 7 => 'Leader'],
        ],
        [
            'icon' => 'package',
            'label' => 'Pengelolaan Aset',
            'roles' => [2, 4, 7],
            'badges' => [4 => 'Full Edit', 7 => 'View Only'],
            'submenu' => [
                [
                    'route' => (in_array($roleId, [2, 4, 7], true) ? 'assets.index' : '#'),
                    'label' => 'Daftar Aset',
                    'icon' => 'list',
                    'active' => 'assets.*',
                ],
                [
                    'route' => (in_array($roleId, [2, 4, 7], true) ? 'rooms.index' : '#'),
                    'label' => 'Master Ruangan',
                    'icon' => 'door-open',
                    'active' => 'rooms.*',
                ],
                [
                    'route' => (in_array($roleId, [2, 4, 7], true) ? 'device-names.index' : '#'),
                    'label' => 'Master Perangkat',
                    'icon' => 'monitor',
                    'active' => 'device-names.*',
                ],
            ],
        ],
        [
            'route' => 'tickets.index',
            'icon' => 'headset',
            'label' => 'Layanan IT',
            'active' => 'tickets.*',
            'roles' => [1, 2, 3, 4, 5, 6, 7],
            'badges' => [1 => 'Monitor', 3 => 'Teknisi', 4 => 'Approval', 5 => 'Pelaporan', 6 => 'Pelaporan', 7 => 'Assign'],
        ],
        [
            'route' => 'users.index',
            'icon' => 'users',
            'label' => 'Pengelolaan Role',
            'active' => 'users.*',
            'roles' => [2],
        ],
        [
            'route' => 'settings.index',
            'icon' => 'settings',
            'label' => 'Pengaturan',
            'roles' => [2],
            'active' => 'settings.*',
        ],
        [
            'icon' => 'help-circle',
            'label' => 'FAQ / Knowledge',
            'roles' => [1, 2, 3, 4, 5, 6, 7],
            'spacer' => true,
            'route' => ($roleId == 2) ? '#' : 'faq.index',
            'active' => 'faq.*',
            ...($roleId == 2 ? [
                'submenu' => [
                    [
                        'route' => 'faq.index',
                        'label' => 'Buku Panduan',
                        'icon' => 'book-open',
                        'active' => 'faq.*',
                    ],
                    [
                        'route' => 'admin.faq.articles.index',
                        'label' => 'Kelola Artikel',
                        'icon' => 'file-text',
                        'active' => 'admin.faq.articles.*',
                    ],
                    [
                        'route' => 'admin.faq.categories.index',
                        'label' => 'Kategori FAQ',
                        'icon' => 'tags',
                        'active' => 'admin.faq.categories.*',
                    ],
                ]
            ] : []),
        ],
    ];

    // saring menu yg boleh diakses aja
    $visibleMenus = array_values(array_filter($menus, fn($m) => in_array($roleId, $m['roles'], true)));
@endphp

{{-- sidebar cuy --}}
<aside
    class="fixed lg:relative inset-y-0 left-0 flex flex-col h-screen bg-bps-blue text-white z-[70] shrink-0 shadow-2xl lg:shadow-none sidebar-transition overflow-x-hidden border-r border-white/5"
    :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-20 -translate-x-full lg:translate-x-0'" id="main-sidebar" x-cloak>

    {{-- logo n brand --}}
    <div class="flex items-center h-16 border-b border-white/10 shrink-0 bg-bps-blue z-20 overflow-hidden px-0">
        <div class="w-20 shrink-0 flex justify-center items-center">
            <div
                class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 font-extrabold text-bps-blue text-xs shadow-lg border-2 border-bps-orange hover:rotate-12 hover:scale-110 transition-all duration-300 cursor-pointer select-none">
                BPS
            </div>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-[400ms]"
            x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-[100ms]"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2"
            class="overflow-hidden whitespace-nowrap min-w-0 pointer-events-none flex-1 ml-1">
            <h1 class="text-xs font-black tracking-tight leading-tight uppercase text-white">SIGAP</h1>
            <p class="text-[9px] text-blue-300 uppercase tracking-widest font-bold">Monitoring System</p>
        </div>
        {{-- tombol close hp --}}
        <button @click="sidebarOpen = false"
            class="lg:hidden ml-auto text-white/70 hover:text-white hover:bg-white/10 p-2 rounded-xl transition-all duration-200 active:scale-90 shrink-0 mr-2">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    {{-- list menu utama --}}
    <nav
        class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto overflow-x-hidden min-h-0 sidebar-scrollbar text-center lg:text-left">
        @foreach($visibleMenus as $index => $menu)

            @if(isset($menu['spacer']))
                <div class="pt-3 mt-3 border-t border-white/10"></div>
            @endif

            {{-- ======================= MENU YG ADA ANAKNYA ======================= --}}
            @if(isset($menu['submenu']))
                @php
                    $isSubmenuActive = false;
                    foreach ($menu['submenu'] as $sub) {
                        if (isset($sub['active']) && request()->routeIs($sub['active'])) {
                            $isSubmenuActive = true;
                            break;
                        }
                    }
                    $badge = $menu['badges'][$roleId] ?? null;
                @endphp

                {{-- bungkus alpine js biar state g gilas --}}
                <div x-data="{ open: {{ $isSubmenuActive ? 'true' : 'false' }} }"
                    x-init="$watch('sidebarOpen', value => { if(!value) open = false })" class="sidebar-menu-item"
                    style="animation-delay: {{ $index * 50 }}ms">

                    {{-- tombol induk buat expand --}}
                    <button type="button" @click="open = !open" class="w-full flex items-center rounded-xl transition-all duration-200 relative py-3
                                            {{ $isSubmenuActive
                    ? 'bg-white/15 text-white font-bold shadow-lg shadow-black/10 ring-1 ring-white/20'
                    : 'text-blue-100/70 hover:bg-white/[0.07] hover:text-white' }}">

                        {{-- garis oren penanda aktif --}}
                        @if($isSubmenuActive)
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-6 bg-bps-orange rounded-r-full shadow-sm shadow-orange-400/50">
                            </div>
                        @endif

                        {{-- tempat icon --}}
                        <div class="w-12 h-full flex-shrink-0 flex justify-center items-center">
                            <x-dynamic-component :component="'lucide-' . ($menu['icon'])"
                                class="w-5 h-5 transition-all duration-200 {{ $isSubmenuActive ? 'text-bps-orange' : 'text-blue-300/80' }}" />
                        </div>

                        {{-- tulisan + label + panah (pas sidebar mangap thx) --}}
                        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-[400ms]"
                            x-transition:enter-start="opacity-0 -translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-[100ms]"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 -translate-x-2"
                            class="flex items-center gap-2 overflow-hidden whitespace-nowrap min-w-0 flex-1 pr-4">
                            <span class="font-semibold text-sm truncate">{{ $menu['label'] }}</span>
                            @if($badge)
                                <span
                                    class="px-1.5 py-0.5 text-[9px] font-bold rounded-md bg-bps-orange/20 text-bps-orange border border-bps-orange/30 uppercase tracking-wide leading-none whitespace-nowrap shrink-0">
                                    {{ $badge }}
                                </span>
                            @endif
                            {{-- panah kecil --}}
                            <x-lucide-chevron-down
                                class="w-4 h-4 ml-auto shrink-0 transition-transform duration-300 text-blue-300/60"
                                x-bind:class="open ? 'rotate-180 !text-bps-orange' : ''" />
                        </div>
                    </button>

                    {{-- anak-anak menu --}}
                    <div x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-6 pl-4 border-l-2 border-white/10 space-y-1">

                        @foreach($menu['submenu'] as $sub)
                            @php
                                $isSubActive = isset($sub['active'])
                                    ? request()->routeIs($sub['active'])
                                    : ($sub['route'] !== '#' && request()->routeIs($sub['route']));
                            @endphp
                            <a href="{{ $sub['route'] !== '#' ? route($sub['route']) : '#' }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-[13px]
                                                            {{ $isSubActive
                            ? 'bg-white/10 text-white font-bold'
                            : 'text-blue-200/60 hover:bg-white/[0.05] hover:text-white' }}">
                                <x-dynamic-component :component="'lucide-' . ($sub['icon'])"
                                    class="w-4 h-4 shrink-0 {{ $isSubActive ? 'text-bps-orange' : 'text-blue-300/50' }}" />
                                <span>{{ $sub['label'] }}</span>
                                @if($isSubActive)
                                    <div class="w-1.5 h-1.5 rounded-full bg-bps-orange ml-auto shrink-0 animate-pulse"></div>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    {{-- pop-up tulisan pas sidebar diciutkan --}}
                    <div x-show="!sidebarOpen" class="relative hidden lg:block">
                        <div
                            class="absolute left-12 top-0 -translate-y-10 px-3 py-2 bg-gray-900 text-white text-[11px] rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap z-[100] pointer-events-none shadow-xl border border-gray-700/50 font-bold uppercase tracking-wider">
                            {{ $menu['label'] }}
                        </div>
                    </div>

                </div>

                {{-- ======================= MENU BIASA (JOMBLO) ======================= --}}
            @else
                @php
                    $isActive = isset($menu['active'])
                        ? request()->routeIs($menu['active'])
                        : (($menu['route'] ?? '#') !== '#' && request()->routeIs($menu['route']));
                    $badge = $menu['badges'][$roleId] ?? null;
                @endphp

                <div class="sidebar-menu-item relative group" style="animation-delay: {{ $index * 50 }}ms">
                    <a href="{{ ($menu['route'] ?? '#') !== '#' ? route($menu['route']) : '#' }}" class="w-full flex items-center rounded-xl transition-all duration-200 relative py-3
                                            {{ $isActive
                    ? 'bg-white/15 text-white font-bold shadow-lg shadow-black/10 ring-1 ring-white/20'
                    : 'text-blue-100/70 hover:bg-white/[0.07] hover:text-white' }}">

                        {{-- garis oren penanda aktif --}}
                        @if($isActive)
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-6 bg-bps-orange rounded-r-full shadow-sm shadow-orange-400/50">
                            </div>
                        @endif

                        {{-- tempat icon --}}
                        <div class="w-12 h-full flex-shrink-0 flex justify-center items-center">
                            <x-dynamic-component :component="'lucide-' . ($menu['icon'])"
                                class="w-5 h-5 transition-all duration-200 {{ $isActive ? 'text-bps-orange' : 'text-blue-300/80' }}" />
                        </div>

                        {{-- teks n label --}}
                        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-[400ms]"
                            x-transition:enter-start="opacity-0 -translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-[100ms]"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 -translate-x-2"
                            class="flex items-center gap-2 overflow-hidden whitespace-nowrap min-w-0 flex-1 pr-4">
                            <span class="font-semibold text-sm truncate">{{ $menu['label'] }}</span>
                            @if($badge)
                                <span
                                    class="px-1.5 py-0.5 text-[9px] font-bold rounded-md bg-bps-orange/20 text-bps-orange border border-bps-orange/30 uppercase tracking-wide leading-none whitespace-nowrap shrink-0">
                                    {{ $badge }}
                                </span>
                            @endif
                        </div>
                    </a>

                    {{-- pop-up menu pas sidebar diciut --}}
                    <div x-show="!sidebarOpen"
                        class="hidden lg:block absolute left-full top-1/2 -translate-y-1/2 ml-3 px-3 py-2 bg-gray-900 text-white text-[11px] rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap z-[100] pointer-events-none shadow-xl border border-gray-700/50 font-bold uppercase tracking-wider">
                        {{ $menu['label'] }}
                        @if($badge)
                            <span class="text-bps-orange font-normal normal-case tracking-normal ml-1">({{ $badge }})</span>
                        @endif
                    </div>
                </div>

            @endif
        @endforeach
    </nav>

    {{-- kolom profil user di bawah --}}
    <div
        class="shrink-0 p-3 border-t border-white/10 bg-gradient-to-t from-blue-900/60 to-transparent backdrop-blur-sm z-20 px-0">
        <div class="flex items-center overflow-hidden whitespace-nowrap">
            <div class="w-20 shrink-0 flex justify-center items-center">
                <div
                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shrink-0 shadow-lg ring-1 ring-white/10 hover:scale-105 transition-transform cursor-pointer">
                    <x-lucide-user class="w-4 h-4 text-blue-50" />
                </div>
            </div>
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-[400ms]"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-[100ms]"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-2"
                class="overflow-hidden min-w-0 whitespace-nowrap flex-1 ml-1">
                <p class="text-xs font-bold truncate text-white tracking-tight">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-blue-300 font-semibold truncate uppercase tracking-wider">
                    @if($roleId == 1) Pimpinan
                    @elseif($roleId == 2) Administrator
                    @elseif($roleId == 3) Teknisi
                    @elseif($roleId == 4) Pengelola Barang
                    @elseif($roleId == 5) Pengelola Ruangan
                    @elseif($roleId == 7) Ketua Tim
                    @else User @endif
                </p>
            </div>
        </div>
    </div>
</aside>