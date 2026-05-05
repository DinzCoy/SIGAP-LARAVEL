<nav class="bg-white border-b border-gray-100 h-16 shrink-0 relative z-50">
    <!-- Primary Navigation Menu -->
    <div class="px-4 h-full flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Sidebar Toggle button -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-bps-blue transition-colors focus:outline-none">
                <x-lucide-menu class="w-6 h-6" />
            </button>

            <!-- Breadcrumb -->
            <div class="hidden sm:block">
                <span class="text-sm font-semibold text-gray-600">SIGAP</span>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-3">
            <!-- User Role Badge -->
            <span
                class="hidden md:inline-flex px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[11px] font-bold border border-blue-100 overflow-hidden whitespace-nowrap">
                {{ \App\Models\User::getRoleName(session('active_role_id')) }}
            </span>

            <!-- Notifications Dropdown -->


            <div x-data="notifPanel()" class="relative">
                {{-- Bell Trigger --}}
                <button @click="toggle()"
                    class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-blue-600 transition-all relative focus:outline-none">
                    <x-lucide-bell class="w-5 h-5" />
                    @if($notifications->count() > 0)
                        <span x-show="visibleCount > 0" x-cloak
                            class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse shadow-sm shadow-red-200"></span>
                    @endif
                </button>

                {{-- Dropdown Panel --}}
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    @click.outside="open = false"
                    class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-200/80 z-50 overflow-hidden"
                    style="max-width: calc(100vw - 2rem);">

                    {{-- Header --}}
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-widest">Notifikasi</span>
                        <div class="flex items-center gap-2">
                            @if($notifications->count() > 0)
                                <span x-show="visibleCount > 0" x-cloak
                                    class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold"
                                    x-text="visibleCount + ' Baru'"></span>
                            @endif
                        </div>
                    </div>

                    {{-- Notification List --}}
                    <div class="max-h-[24rem] overflow-y-auto overscroll-contain">
                        @forelse($notifications as $idx => $notif)
                            <a href="{{ $notif['url'] }}" x-show="!isDismissed({{ $idx }})" x-cloak
                                title="{{ $notif['time'] ? \Carbon\Carbon::parse($notif['time'])->format('d M Y, H:i') : '' }}"
                                class="px-4 py-2.5 border-b border-gray-50 hover:bg-slate-50/80 flex items-start gap-3 transition-colors group">
                                <div
                                    class="bg-blue-50 text-blue-600 p-2 rounded-lg shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors mt-0.5">
                                    <x-dynamic-component :component="'lucide-' . ($notif['icon'])" class="w-4 h-4" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $notif['title'] }}</p>
                                        @if($notif['time'])
                                            <span
                                                class="text-[10px] text-gray-400 whitespace-nowrap shrink-0">{{ \Carbon\Carbon::parse($notif['time'])->format('d M Y, H:i') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed break-words whitespace-normal">
                                        {{ $notif['desc'] }}</p>
                                </div>
                            </a>
                        @empty
                        @endforelse

                        {{-- Empty State --}}
                        <div x-show="visibleCount === 0" x-cloak class="px-4 py-8 text-center text-gray-500">
                            <div
                                class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <x-lucide-bell-off class="w-6 h-6 text-gray-300" />
                            </div>
                            <p class="text-xs font-bold text-gray-600">Tidak ada notifikasi!</p>
                            <p class="text-[11px] text-gray-400 mt-1">Semua tugas Anda sudah beres.</p>
                            @if($notifications->count() > 0)
                                <button @click="restoreAll()"
                                    class="mt-3 text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Kembalikan Semua
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    @if($notifications->count() > 0)
                        <div class="border-t border-gray-100 px-3 py-2 flex items-center justify-between bg-gray-50">
                            <a href="{{ route('tickets.index') }}"
                                class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors whitespace-nowrap">
                                Kelola Tiket
                            </a>
                            <button x-show="visibleCount > 0" x-cloak @click="dismissAll()"
                                class="text-[10px] font-semibold text-gray-400 hover:text-red-600 px-2 py-1 rounded hover:bg-red-50 transition-colors inline-flex items-center gap-1 whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Sembunyikan
                            </button>
                            <button x-show="visibleCount === 0 && totalCount > 0" x-cloak @click="restoreAll()"
                                class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-50 transition-colors inline-flex items-center gap-1 whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Tampilkan Semua
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <script>
                function notifPanel() {
                    const STORAGE_KEY = 'bps_dismissed_notifs';
                    const totalCount = {{ $notifications->count() }};
                    return {
                        open: false,
                        totalCount: totalCount,
                        dismissed: JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'),
                        get visibleCount() {
                            return this.totalCount - this.dismissed.filter(i => i < this.totalCount).length;
                        },
                        toggle() { this.open = !this.open; },
                        isDismissed(idx) { return this.dismissed.includes(idx); },
                        dismissAll() {
                            this.dismissed = Array.from({ length: this.totalCount }, (_, i) => i);
                            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.dismissed));
                        },
                        restoreAll() {
                            this.dismissed = [];
                            localStorage.setItem(STORAGE_KEY, JSON.stringify(this.dismissed));
                        },
                        init() {
                            // Auto-reset dimissed list when notification count changes (new notif arrived)
                            const storedTotal = parseInt(localStorage.getItem(STORAGE_KEY + '_total') || '0');
                            if (storedTotal !== this.totalCount) {
                                this.dismissed = [];
                                localStorage.setItem(STORAGE_KEY, '[]');
                            }
                            localStorage.setItem(STORAGE_KEY + '_total', this.totalCount.toString());
                        }
                    }
                }
            </script>

            {{-- Dark Mode Toggle —— Vanilla JS, no Alpine dependency --}}
            <button onclick="window.toggleDarkMode()" id="darkModeToggle"
                class="p-2 rounded-lg hover:bg-gray-100 transition-all focus:outline-none"
                title="Toggle Dark Mode">
                {{-- Moon icon (shown in light mode) --}}
                <svg id="icon-moon" class="w-5 h-5 text-gray-400 hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                {{-- Sun icon (shown in dark mode) --}}
                <svg id="icon-sun" class="w-5 h-5 text-yellow-400 hover:text-yellow-300 transition-colors" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
            <script>
                // Initialize icon state on load
                (function() {
                    const isDark = document.documentElement.classList.contains('dark');
                    document.getElementById('icon-moon').style.display = isDark ? 'none' : 'block';
                    document.getElementById('icon-sun').style.display = isDark ? 'block' : 'none';
                    // Dark hover bg for toggle button
                    if (isDark) document.getElementById('darkModeToggle').classList.replace('hover:bg-gray-100', 'hover:bg-gray-700');
                })();

                window.toggleDarkMode = function() {
                    const html = document.documentElement;
                    const isDark = html.classList.toggle('dark');
                    localStorage.setItem('bps_dark_mode', isDark);

                    // Swap icons
                    document.getElementById('icon-moon').style.display = isDark ? 'none' : 'block';
                    document.getElementById('icon-sun').style.display = isDark ? 'block' : 'none';

                    // Toggle hover style
                    const btn = document.getElementById('darkModeToggle');
                    if (isDark) {
                        btn.classList.replace('hover:bg-gray-100', 'hover:bg-gray-700');
                    } else {
                        btn.classList.replace('hover:bg-gray-700', 'hover:bg-gray-100');
                    }
                };
            </script>

            <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>

            <!-- Settings Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition-all focus:outline-none group">
                        <div class="flex flex-col items-end mr-1 hidden sm:flex">
                            <span class="text-xs font-bold text-gray-900 group-hover:text-bps-blue">{{
    Auth::user()->name }}</span>
                            <span class="text-[10px] text-gray-500">Online</span>
                        </div>
                        <div
                            class="w-8 h-8 rounded-lg bg-bps-blue text-white flex items-center justify-center font-bold text-xs shadow-sm shadow-blue-200 group-hover:scale-105 transition-transform uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <x-lucide-chevron-down class="w-4 h-4 text-gray-400" />
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div
                        class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider font-bold border-b border-gray-100">
                        Akun Saya</div>
                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                        <x-lucide-user class="w-4 h-4" />
                        {{ __('Profil Saya') }}
                    </x-dropdown-link>

                    <div class="border-t border-gray-100"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                            this.closest('form').submit();"
                            class="text-red-600 flex items-center gap-2 hover:bg-red-50">
                            <x-lucide-log-out class="w-4 h-4" />
                            {{ __('Keluar') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>