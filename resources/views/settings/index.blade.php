<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <x-lucide-settings class="w-6 h-6 text-bps-blue" />
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    {{-- Success/Error Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
             class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
            <x-lucide-check-circle class="w-5 h-5 text-emerald-500 shrink-0" />
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <x-lucide-alert-circle class="w-5 h-5 text-red-500 shrink-0" />
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabbed Settings UI --}}
    <div x-data="{ activeTab: 'profil' }">
        {{-- Tab Navigation --}}
        <div class="flex flex-wrap gap-1 mb-8 bg-white rounded-xl p-1.5 shadow-sm border border-gray-100">
            @php
                $tabs = [
                    ['id' => 'profil', 'icon' => 'user', 'label' => 'Pengaturan Profil'],
                    ['id' => 'sistem', 'icon' => 'server', 'label' => 'Konfigurasi Sistem'],
                    ['id' => 'threshold', 'icon' => 'gauge', 'label' => 'Threshold Anomali'],
                    ['id' => 'maintenance', 'icon' => 'database', 'label' => 'Maintenance & Logs'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button @click="activeTab = '{{ $tab['id'] }}'"
                        :class="activeTab === '{{ $tab['id'] }}'
                            ? 'bg-bps-blue text-white shadow-md shadow-blue-200'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">
                    <x-dynamic-component :component="'lucide-' . ($tab['icon'])" class="w-4 h-4" />
                    <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- ========== TAB 1: PENGATURAN PROFIL ========== --}}
        <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

            {{-- Update Profil --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-edit-3 class="w-4 h-4 text-bps-blue" /> Ubah Profil
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Update nama dan email akun BPS Anda</p>
                </div>
                <form method="POST" action="{{ route('settings.updateProfile') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5 transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email BPS</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5 transition-colors" required>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-bps-blue text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 active:scale-[0.97]">
                            <x-lucide-save class="w-4 h-4 inline mr-1" /> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            {{-- Keamanan Akun --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-shield class="w-4 h-4 text-bps-blue" /> Keamanan Akun
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Ganti password secara berkala untuk keamanan jaringan</p>
                </div>
                <form method="POST" action="{{ route('settings.updatePassword') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5" required>
                        </div>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-bps-blue text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 active:scale-[0.97]">
                            <x-lucide-lock class="w-4 h-4 inline mr-1" /> Ubah Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Aktivitas Login --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-clock class="w-4 h-4 text-bps-blue" /> Aktivitas Login Saya
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Log 10 aktivitas login terakhir Anda</p>
                </div>
                <div class="p-6">
                    @if($loginActivities->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas login tercatat.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                                        <th class="pb-3 pr-4 font-semibold">Waktu</th>
                                        <th class="pb-3 pr-4 font-semibold">IP Address</th>
                                        <th class="pb-3 font-semibold">Browser / Agent</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($loginActivities as $activity)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="py-2.5 pr-4 text-gray-700 whitespace-nowrap">
                                                <span class="font-medium">{{ $activity->logged_in_at->format('d M Y') }}</span>
                                                <span class="text-gray-400 ml-1">{{ $activity->logged_in_at->format('H:i') }}</span>
                                            </td>
                                            <td class="py-2.5 pr-4">
                                                <code class="px-2 py-0.5 bg-gray-100 rounded text-xs font-mono text-gray-600">{{ $activity->ip_address }}</code>
                                            </td>
                                            <td class="py-2.5 text-gray-500 text-xs truncate max-w-[200px]">{{ Str::limit($activity->user_agent, 60) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ========== TAB 2: KONFIGURASI SISTEM ========== --}}
        <div x-show="activeTab === 'sistem'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

            {{-- API Key Management --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-key class="w-4 h-4 text-bps-orange" /> Manajemen API Key & Server
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Ganti API key secara berkala tanpa bongkar kode Laravel</p>
                </div>
                <form method="POST" action="{{ route('settings.updateSystem') }}" class="p-6 space-y-4" x-data="{ showKey: false }">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">X-API-KEY</label>
                        <div class="relative">
                            <input :type="showKey ? 'text' : 'password'" name="api_key" value="{{ $settings['api_key'] ?? '' }}"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5 pr-12 font-mono" required>
                            <button type="button" @click="showKey = !showKey" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <x-lucide-eye x-show="!showKey" class="w-4 h-4" />
                                <x-lucide-eye-off x-show="showKey" class="w-4 h-4" />
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5">Key ini digunakan oleh agent PowerShell di setiap PC untuk autentikasi ke server</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Internal DNS / URL Server</label>
                        <input type="text" name="server_url" value="{{ $settings['server_url'] ?? '' }}"
                               class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5 font-mono" required
                               placeholder="http://192.168.20.24">
                        <p class="text-[11px] text-gray-400 mt-1.5">URL utama server (bisa diganti dari IP ke domain lokal)</p>
                    </div>
                    @error('api_key') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-bps-blue text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 active:scale-[0.97]">
                            <x-lucide-save class="w-4 h-4 inline mr-1" /> Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>

            {{-- IP Whitelist --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-500" /> Whitelist IP
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Daftar IP PC yang diizinkan mengirim data ke server</p>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Add IP Form --}}
                    <form method="POST" action="{{ route('settings.addWhitelistIp') }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="text" name="ip_address" placeholder="Contoh: 192.168.1.100"
                               class="flex-1 rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5 font-mono" required>
                        <input type="text" name="label" placeholder="Label (opsional, misal: PC Keuangan)"
                               class="flex-1 rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5">
                        <button type="submit" class="px-4 py-2.5 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:bg-emerald-600 transition-colors shrink-0 active:scale-[0.97]">
                            <x-lucide-plus class="w-4 h-4 inline mr-1" /> Tambah
                        </button>
                    </form>
                    @error('ip_address') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    {{-- IP List --}}
                    @if($whitelistedIps->isEmpty())
                        <div class="text-center py-6">
                            <x-lucide-globe class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                            <p class="text-sm text-gray-400">Belum ada IP yang di-whitelist. Semua IP diizinkan.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($whitelistedIps as $ip)
                                <div class="flex items-center justify-between py-3 group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                        <code class="text-sm font-mono text-gray-700 font-medium">{{ $ip->ip_address }}</code>
                                        @if($ip->label)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-lg">{{ $ip->label }}</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('settings.removeWhitelistIp', $ip->id) }}" onsubmit="return confirm('Hapus IP ini dari whitelist?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ========== TAB 3: THRESHOLD ANOMALI ========== --}}
        <div x-show="activeTab === 'threshold'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-gauge class="w-4 h-4 text-amber-500" /> Ambang Batas Anomali
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Atur kapan sistem menandai PC sebagai bermasalah</p>
                </div>
                <form method="POST" action="{{ route('settings.updateThresholds') }}" class="p-6 space-y-6"
                      x-data="{ ramVal: {{ $settings['ram_threshold'] ?? 90 }} }">
                    @csrf

                    {{-- RAM Threshold Slider --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Alert Threshold RAM:
                            <span class="ml-2 px-2 py-0.5 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold" x-text="ramVal + '%'"></span>
                        </label>
                        <input type="range" name="ram_threshold" min="50" max="99" x-model="ramVal"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-bps-orange">
                        <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                            <span>50%</span>
                            <span>75%</span>
                            <span>99%</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2">PC dengan penggunaan RAM di atas nilai ini akan ditandai "Anomali"</p>
                    </div>

                    {{-- Disk Threshold --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alert Threshold Disk (GB tersisa)</label>
                        <div class="relative max-w-xs">
                            <input type="number" name="disk_threshold_gb" value="{{ $settings['disk_threshold_gb'] ?? 10 }}"
                                   min="1" max="500"
                                   class="w-full rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5 pr-12" required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">GB</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5">Notifikasi merah dikirim jika sisa disk kurang dari nilai ini</p>
                    </div>

                    {{-- Report Interval --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Interval Laporan Rutin</label>
                        <select name="report_interval_days" class="max-w-xs rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5">
                            @foreach([1 => 'Setiap Hari', 7 => 'Mingguan (7 hari)', 14 => '2 Minggu', 30 => 'Bulanan (30 hari)'] as $val => $label)
                                <option value="{{ $val }}" {{ ($settings['report_interval_days'] ?? 7) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1.5">Seberapa sering Agent di PC harus mengirim laporan rutin</p>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-bps-blue text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200 active:scale-[0.97]">
                            <x-lucide-save class="w-4 h-4 inline mr-1" /> Simpan Threshold
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========== TAB 4: MAINTENANCE & LOGS ========== --}}
        <div x-show="activeTab === 'maintenance'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

            {{-- Log Retention --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-calendar class="w-4 h-4 text-bps-blue" /> Log Retention
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Atur berapa lama data log PC disimpan sebelum dibersihkan</p>
                </div>
                <form method="POST" action="{{ route('settings.updateRetention') }}" class="p-6">
                    @csrf
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Simpan data selama</label>
                            <select name="log_retention_months" class="rounded-xl border-gray-200 focus:border-bps-blue focus:ring-bps-blue/20 text-sm py-2.5">
                                @foreach([3 => '3 Bulan', 6 => '6 Bulan', 12 => '12 Bulan', 24 => '24 Bulan'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($settings['log_retention_months'] ?? 6) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-bps-blue text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors active:scale-[0.97]">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Export Data --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                        <x-lucide-download class="w-6 h-6 text-emerald-500" />
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">Export Data</h4>
                    <p class="text-xs text-gray-400 mt-1 mb-4">Download semua data PC ke file CSV untuk laporan</p>
                    <a href="{{ route('settings.exportData') }}"
                       class="w-full px-4 py-2.5 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:bg-emerald-600 transition-colors text-center active:scale-[0.97]">
                        <x-lucide-file-spreadsheet class="w-4 h-4 inline mr-1" /> Download CSV
                    </a>
                </div>

                {{-- Backup Database --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                        <x-lucide-hard-drive class="w-6 h-6 text-bps-blue" />
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">Backup Database</h4>
                    <p class="text-xs text-gray-400 mt-1 mb-4">Cadangkan database MariaDB saat ini</p>
                    <form method="POST" action="{{ route('settings.backupDatabase') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-bps-blue text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors active:scale-[0.97]"
                                onclick="return confirm('Mulai backup database sekarang?')">
                            <x-lucide-database class="w-4 h-4 inline mr-1" /> Backup Sekarang
                        </button>
                    </form>
                </div>

                {{-- Clean Old Logs --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center mb-3">
                        <x-lucide-trash-2 class="w-6 h-6 text-red-400" />
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">Bersihkan Log Lama</h4>
                    <p class="text-xs text-gray-400 mt-1 mb-4">Hapus data lebih lama dari retensi ({{ $settings['log_retention_months'] ?? 6 }} bulan)</p>
                    <form method="POST" action="{{ route('settings.cleanOldLogs') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-bold hover:bg-red-600 transition-colors active:scale-[0.97]"
                                onclick="return confirm('YAKIN hapus semua data log yang lebih lama dari {{ $settings['log_retention_months'] ?? 6 }} bulan? Aksi ini tidak bisa dibatalkan!')">
                            <x-lucide-eraser class="w-4 h-4 inline mr-1" /> Bersihkan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Re-initialize Lucide icons after Alpine renders --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
</x-app-layout>
