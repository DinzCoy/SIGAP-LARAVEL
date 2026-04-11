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
                <span class="text-sm font-semibold text-gray-600">BPS-PC Guardian</span>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-3">
            <!-- User Role Badge -->
            <span
                class="hidden md:inline-flex px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[11px] font-bold border border-blue-100 overflow-hidden whitespace-nowrap">
                @if(session('active_role_id') == 1)
                    Pimpinan
                @elseif(session('active_role_id') == 2)
                    Administrator
                @elseif(session('active_role_id') == 3)
                    Teknisi
                @elseif(session('active_role_id') == 4)
                    Pengelola Barang
                @elseif(session('active_role_id') == 5)
                    Pengelola Ruangan
                @else
                    Pengguna
                @endif
            </span>

            <!-- Notifications Dropdown -->
            @php
                $notifRoleId = session('active_role_id');
                $notifications = collect();
                
                // Menarik notifikasi berdasarkan pekerjaan tertunda user
                if ($notifRoleId == 3) { // Teknisi
                    $pending = \App\Models\Ticket::whereIn('status', ['Open', 'Diteruskan ke Teknisi'])->latest()->take(3)->get();
                    foreach($pending as $t) {
                        $notifications->push([
                            'title' => 'Tugas Teknisi Baru',
                            'desc' => 'Tiket ' . $t->ticket_number . ' butuh penanganan segera.',
                            'url' => route('tickets.show', $t->id),
                            'icon' => 'wrench'
                        ]);
                    }
                } elseif ($notifRoleId == 4) { // Pengelola Barang
                    $pending = \App\Models\Ticket::where('type', 'Asset')->where('status', 'Menunggu Pengecekan Pengelola')->latest()->take(3)->get();
                    foreach($pending as $t) {
                         $notifications->push([
                            'title' => 'Tinjauan Aset',
                            'desc' => 'Tiket ' . $t->ticket_number . ' memerlukan persetujuan/estimasi biaya Anda.',
                            'url' => route('tickets.show', $t->id),
                            'icon' => 'clipboard-check'
                        ]);
                    }
                } else { // Pengguna/Role Lain (Approval/Responses)
                    // Cek jika ada tiket miliknya yang menunggu persetujuan biaya
                    if(Auth::check()) {
                        $responses = \App\Models\Ticket::where('reported_by', Auth::id())->where('status', 'Menunggu Persetujuan Biaya')->latest()->take(3)->get();
                        foreach($responses as $t) {
                             $notifications->push([
                                'title' => 'Persetujuan Biaya',
                                'desc' => 'Tiket ' . $t->ticket_number . ' membutuhkan konfirmasi dana dari Anda.',
                                'url' => route('tickets.show', $t->id),
                                'icon' => 'credit-card'
                            ]);
                        }
                    }
                }
            @endphp
            
            <x-dropdown align="right" width="w-80">
                <x-slot name="trigger">
                    <button class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-blue-600 transition-all relative focus:outline-none">
                        <x-lucide-bell class="w-5 h-5" />
                        @if($notifications->count() > 0)
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse shadow-sm shadow-red-200"></span>
                        @endif
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-widest">Aksi Prioritas</span>
                        @if($notifications->count() > 0)
                        <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $notifications->count() }} Baru</span>
                        @endif
                    </div>
                    
                    <div class="max-h-80 overflow-y-auto w-full">
                        @forelse($notifications as $notif)
                        <a href="{{ $notif['url'] }}" class="px-4 py-3 border-b border-gray-50 hover:bg-slate-50 flex items-start gap-3 transition-colors group">
                            <div class="bg-blue-50 text-blue-600 p-2.5 rounded-full shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <x-dynamic-component :component="'lucide-' . ($notif['icon'])" class="w-4 h-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $notif['title'] }}</p>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed break-words whitespace-normal">{{ $notif['desc'] }}</p>
                            </div>
                        </a>
                        @empty
                        <div class="px-4 py-8 text-center text-gray-500">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <x-lucide-bell-off class="w-6 h-6 text-gray-300" />
                            </div>
                            <p class="text-xs font-bold text-gray-600">Anda sudah mengikuti semua alur kerja!</p>
                            <p class="text-[11px] text-gray-400 mt-1">Tidak ada aksi prioritas tertunda.</p>
                        </div>
                        @endforelse
                    </div>
                    
                    @if($notifications->count() > 0)
                    <div class="p-2 text-center border-t border-gray-100 bg-gray-50/80">
                        <a href="{{ route('tickets.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-block py-1 hover:underline">Kelola Semua Tiket Secara Penuh</a>
                    </div>
                    @endif
                </x-slot>
            </x-dropdown>

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