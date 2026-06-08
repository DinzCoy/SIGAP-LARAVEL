<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 text-slate-500 mb-1.5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-slate-50 border border-slate-200 rounded-full text-xs font-bold transition-all text-slate-600 shadow-sm group/back">
                        <svg class="w-3 h-3 transition-transform group-hover/back:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-[10px] uppercase tracking-[0.2em] font-extrabold" style="color: rgba(0, 90, 140, 0.8);">Device Analytics</span>
                </div>
                <h2 class="font-black text-2xl sm:text-3xl text-bps-blue tracking-tight flex items-center gap-3.5">
                    {{ $report->hostname }}
                    <span class="text-slate-400 text-lg font-mono font-light">#{{ substr($report->id, -4) }}</span>
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-3 px-4 py-2 rounded-xl {{ !$isOffline ? 'status-badge-connected' : 'status-badge-offline' }}">
                    @if(!$isOffline)
                    <div class="relative flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Status</span>
                        <span class="text-xs font-black leading-tight uppercase tracking-wide">Connected</span>
                    </div>
                    @else
                    <div class="relative flex h-3.5 w-3.5">
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.4)]"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Status</span>
                        <span class="text-xs font-black leading-tight uppercase tracking-wide">Offline</span>
                    </div>
                    @endif
                </div>

                <div x-data="{ showDeleteModal: false, confirmText: '' }" class="h-full">
                    <button @click="showDeleteModal = true" type="button" class="group/del h-full flex items-center gap-2 px-4 py-2.5 btn-terminate rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover/del:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Terminate</span>
                    </button>

                    <template x-teleport="body">
                        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">

                            <div @click.away="showDeleteModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-gray-100"
                                x-show="showDeleteModal"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-8 scale-95">

                                <div class="h-2 bg-red-600"></div>
                                <div class="p-8">
                                    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mb-6 ring-8 ring-red-50/50">
                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>

                                    <h3 class="text-2xl font-black text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
                                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                                        Anda akan menghapus <span class="font-black text-gray-800">{{ $report->hostname }}</span>. Data spesifikasi dan riwayat log akan hilang selamanya.
                                    </p>

                                    <div class="mb-8">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Type Hostname to Confirm</label>
                                        <input type="text" x-model="confirmText" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl text-sm font-bold focus:border-red-500 focus:ring-0 transition-all outline-none" placeholder="{{ $report->hostname }}">
                                    </div>

                                    <div class="flex gap-3">
                                        <button @click="showDeleteModal = false; confirmText = ''" class="flex-1 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black rounded-2xl transition-all uppercase text-xs tracking-widest">Cancel</button>
                                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" :disabled="confirmText !== '{{ $report->hostname }}'"
                                                class="w-full px-6 py-4 bg-red-600 disabled:bg-gray-200 text-white font-black rounded-2xl transition-all shadow-xl shadow-red-200 disabled:shadow-none uppercase text-xs tracking-widest">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-[1400px] mx-auto">
        @if($report->is_trouble)
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm border border-red-100 flex items-start gap-3 animate-pulse" role="alert">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="font-black text-sm">Indikasi Anomali Terdeteksi!</p>
                <p class="text-xs text-red-700/90 mt-1 leading-relaxed">{{ $report->trouble_note }}</p>
            </div>
        </div>
        @endif

        @php
            $totalRamGb = round(($report->total_ram_kb ?? 0) / 1024 / 1024, 2);
            $freeRamGb = round(($report->ram_free_kb ?? 0) / 1024 / 1024, 2);
            $usedRamGb = $totalRamGb > 0 ? round($totalRamGb - $freeRamGb, 2) : 0;
            $ramPercent = $totalRamGb > 0 ? round(($usedRamGb / $totalRamGb) * 100) : 0;

            $totalDiskGb = round(($report->total_disk_b ?? 0) / 1024 / 1024 / 1024, 2);
            $freeDiskGb = round(($report->disk_free_b ?? 0) / 1024 / 1024 / 1024, 2);
            $usedDiskGb = $totalDiskGb > 0 ? round($totalDiskGb - $freeDiskGb, 2) : 0;
            $diskUsedPercent = $totalDiskGb > 0 ? round(($usedDiskGb / $totalDiskGb) * 100) : 0;

            $ramColor = $ramPercent > 85 ? '#ef4444' : ($ramPercent > 70 ? '#f59e0b' : '#005A8C');
            $ramColorDark = $ramPercent > 85 ? '#f87171' : ($ramPercent > 70 ? '#fbbf24' : '#60a5fa');

            $diskColor = $diskUsedPercent > 85 ? '#ef4444' : ($diskUsedPercent > 70 ? '#f59e0b' : '#005A8C');
            $diskColorDark = $diskUsedPercent > 85 ? '#f87171' : ($diskUsedPercent > 70 ? '#fbbf24' : '#60a5fa');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="col-span-1 space-y-5">

                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.015)] overflow-hidden transition-all hover:shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                    <div class="p-5 border-b border-slate-200/80 dark:border-slate-800/80 relative">
                        <div class="flex flex-col gap-3">
                            <div class="w-10 h-10 card-icon-container-blue rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-5.5 h-5.5 card-icon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                             </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight text-bps-blue dark:text-slate-100">{{ $report->hostname }}</h3>
                                <p class="text-[9px] font-black text-bps-blue/80 dark:text-blue-400 uppercase tracking-widest mt-0.5">System Identifier</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-3">

                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50/50 dark:bg-slate-800/40 border border-slate-100/60 dark:border-slate-800/60 rounded-xl shadow-sm">
                            <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 shadow-sm flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5 text-bps-blue dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Ruangan</p>
                                <p class="text-xs font-black text-gray-800 dark:text-slate-200">{{ $report->room_name ?: 'Unknown Location' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50/50 dark:bg-slate-800/40 border border-slate-100/60 dark:border-slate-800/60 rounded-xl shadow-sm">
                            <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 shadow-sm flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5 text-bps-orange" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <rect x="16" y="16" width="6" height="6" rx="1"></rect>
                                    <rect x="2" y="16" width="6" height="6" rx="1"></rect>
                                    <rect x="9" y="2" width="6" height="6" rx="1"></rect>
                                    <path d="M12 8v8M5 16v-3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 dark:text-slate-550 uppercase tracking-wider mb-0.5">Network Identity</p>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs font-mono font-bold px-1.5 py-0.5 rounded w-fit" style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; color: #005A8C;">{{ $report->ip_address }}</span>
                                    <span class="text-[9px] font-mono text-gray-400 dark:text-slate-550 leading-none pl-0.5 mt-0.5">{{ $report->mac_address }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50/50 dark:bg-slate-800/40 border border-slate-100/60 dark:border-slate-800/60 rounded-xl shadow-sm">
                            <div class="w-9 h-9 rounded-lg {{ !$isOffline ? 'bg-green-50 dark:bg-green-950/20' : 'bg-red-50 dark:bg-red-950/20' }} flex items-center justify-center shrink-0">
                                <div class="w-2 h-2 rounded-full {{ !$isOffline ? 'bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.6)]' }}"></div>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Last Online Activity</p>
                                <p class="text-xs font-black text-gray-800 dark:text-slate-200">{{ $report->last_seen ? $report->last_seen->diffForHumans() : 'No record' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.015)] overflow-hidden transition-all hover:shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                    <div class="p-4 border-b border-slate-200/80 dark:border-slate-800/80 flex justify-between items-center bg-slate-50/30 dark:bg-slate-800/20">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-4 bg-bps-blue dark:bg-blue-400 rounded-full"></div>
                            <h3 class="text-xs font-black text-bps-blue uppercase tracking-widest">Inventory Asset</h3>
                        </div>
                        @if($report->asset)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-blue-50 dark:bg-blue-950/40 text-bps-blue dark:text-blue-400 border border-blue-100/60 dark:border-blue-900/30">
                            {{ $report->asset->status_kondisi }}
                        </span>
                        @endif
                    </div>

                    <div class="p-5">
                        @if($report->asset)
                        <div class="space-y-3">

                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 dark:bg-slate-800/40 border border-slate-100/60 dark:border-slate-800/60 rounded-xl shadow-sm">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Nomor BMN</p>
                                    <p class="font-mono font-black text-bps-blue text-sm tracking-tight">{{ $report->asset->bmn_number }}</p>
                                </div>
                                <svg class="w-12 h-6 text-gray-450 dark:text-slate-500 opacity-60 shrink-0" fill="currentColor" viewBox="0 0 40 20">
                                    <rect x="0" y="0" width="2" height="20"/>
                                    <rect x="3" y="0" width="1" height="20"/>
                                    <rect x="5" y="0" width="3" height="20"/>
                                    <rect x="9" y="0" width="1" height="20"/>
                                    <rect x="11" y="0" width="2" height="20"/>
                                    <rect x="14" y="0" width="1" height="20"/>
                                    <rect x="16" y="0" width="3" height="20"/>
                                    <rect x="20" y="0" width="2" height="20"/>
                                    <rect x="23" y="0" width="1" height="20"/>
                                    <rect x="25" y="0" width="2" height="20"/>
                                    <rect x="28" y="0" width="3" height="20"/>
                                    <rect x="32" y="0" width="1" height="20"/>
                                    <rect x="34" y="0" width="2" height="20"/>
                                    <rect x="37" y="0" width="3" height="20"/>
                                </svg>
                            </div>

                            <div class="flex items-center justify-between p-3.5 bg-slate-50/50 dark:bg-slate-800/40 border border-slate-100/60 dark:border-slate-800/60 rounded-xl shadow-sm">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Serial Number</p>
                                    <p class="font-mono font-bold text-bps-blue text-xs">{{ $report->asset->serial_number ?? 'N/A' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-450 dark:text-slate-550 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 border border-dashed border-gray-200 dark:border-slate-700">
                                <svg class="w-6 h-6 text-gray-300 dark:text-slate-650" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <p class="text-[11px] font-bold text-gray-450 dark:text-slate-500 uppercase tracking-widest">Unlinked Device</p>
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5 px-4">Tautkan ke data BMN di menu Asset Manager</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-span-1 space-y-5">

                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.015)] overflow-hidden transition-all hover:shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                    <div class="p-5 border-b border-slate-200/80 dark:border-slate-800/80 relative">
                        <div class="flex flex-col gap-3">
                            <div class="w-10 h-10 card-icon-container-blue rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 card-icon-blue" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                                    <rect x="9" y="9" width="6" height="6"></rect>
                                    <line x1="9" y1="1" x2="9" y2="4"></line>
                                    <line x1="15" y1="1" x2="15" y2="4"></line>
                                    <line x1="9" y1="20" x2="9" y2="23"></line>
                                    <line x1="15" y1="20" x2="15" y2="23"></line>
                                    <line x1="20" y1="9" x2="23" y2="9"></line>
                                    <line x1="20" y1="15" x2="23" y2="15"></line>
                                    <line x1="1" y1="9" x2="4" y2="9"></line>
                                    <line x1="1" y1="15" x2="4" y2="15"></line>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-bps-blue uppercase tracking-widest">Hardware Architecture</h3>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5 leading-tight">{{ $report->os_name }}</p>
                            </div>
                        </div>
                        <div class="absolute top-5 right-5 flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-850">
                            <span class="w-1 h-1 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></span>
                            <span class="text-[9px] font-bold text-slate-650 dark:text-slate-400 font-mono">Build {{ $report->os_build }}</span>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">

                        <div class="flex flex-col items-center justify-center py-2">
                            <span class="text-[10px] font-black text-slate-450 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Physical Memory</span>

                            <div class="relative flex items-center justify-center">

                                <svg class="w-28 h-28 transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="42" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="6.5" fill="transparent" />
                                    <circle cx="50" cy="50" r="42"
                                            class="gauge-ram transition-all duration-1000"
                                            style="stroke: {{ $ramColor }}; --ram-color-dark: {{ $ramColorDark }};"
                                            stroke-width="6.5"
                                            fill="transparent"
                                            stroke-dasharray="263.89"
                                            stroke-dashoffset="{{ 263.89 * (1 - $ramPercent / 100) }}"
                                            stroke-linecap="round" />
                                </svg>

                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black text-slate-850 dark:text-slate-100 tracking-tight">{{ $ramPercent }}%</span>
                                </div>
                            </div>

                            <div class="flex justify-center gap-8 mt-4 w-full max-w-[240px]">
                                <div class="flex flex-col items-center text-center">
                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Used</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $usedRamGb }} GB</span>
                                </div>
                                <div class="flex flex-col items-center text-center">
                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Capacity</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $totalRamGb }} GB</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center py-2 border-t border-slate-100 dark:border-slate-800/80 pt-4">
                            <span class="text-[10px] font-black text-slate-450 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Local Disk (C:)</span>

                            <div class="relative flex items-center justify-center">

                                <svg class="w-28 h-28 transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="42" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-width="6.5" fill="transparent" />
                                    <circle cx="50" cy="50" r="42"
                                            class="gauge-disk transition-all duration-1000"
                                            style="stroke: {{ $diskColor }}; --disk-color-dark: {{ $diskColorDark }};"
                                            stroke-width="6.5"
                                            fill="transparent"
                                            stroke-dasharray="263.89"
                                            stroke-dashoffset="{{ 263.89 * (1 - $diskUsedPercent / 100) }}"
                                            stroke-linecap="round" />
                                </svg>

                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black text-slate-850 dark:text-slate-100 tracking-tight">{{ $diskUsedPercent }}%</span>
                                </div>
                            </div>

                            <div class="flex justify-center gap-8 mt-4 w-full max-w-[240px]">
                                <div class="flex flex-col items-center text-center">
                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Remaining</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $freeDiskGb }} GB</span>
                                </div>
                                <div class="flex flex-col items-center text-center">
                                    <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Capacity</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $totalDiskGb }} GB</span>
                                </div>
                            </div>
                        </div>

                        @if($diskUsedPercent > 90)
                        <div class="p-3 warning-banner-red rounded-xl text-[11px] font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Low Disk Space Warning (C: is full!)</span>
                        </div>
                        @elseif($diskUsedPercent > 80)
                        <div class="p-3 warning-banner-amber rounded-xl text-[11px] font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Warning: Disk Space is running low.</span>
                        </div>
                        @endif

                        @if($ramPercent > 85)
                        <div class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-xl text-[11px] text-red-700 dark:text-red-400 font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Warning: High Memory Usage!</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($report->is_trouble)

                <div class="bg-red-50 dark:bg-slate-900 overflow-hidden shadow-sm rounded-2xl border border-red-200 dark:border-red-900/50">
                    <div class="p-4 border-b border-red-100 dark:border-red-900/30 bg-red-100/50 dark:bg-red-950/20 flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center shadow-md shrink-0">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-black text-red-900 dark:text-red-200 uppercase tracking-widest">Security Anomaly Detected</h3>
                    </div>
                    <div class="p-5">
                        <div class="bg-white dark:bg-slate-850 rounded-xl p-3 border border-red-100/60 dark:border-red-950/40">
                            <p class="text-xs font-bold text-red-800 dark:text-red-300 leading-relaxed">{{ $report->trouble_note }}</p>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="px-2.5 py-0.5 bg-white dark:bg-slate-800 border border-red-100 dark:border-red-950/40 rounded-full text-[9px] font-black text-red-600 dark:text-red-400 uppercase">Action Required</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-span-1 space-y-5">

                <div x-data="{
                    search: '',
                    currentPage: 1,
                    perPage: 15,
                    items: {{ $report->installedSoftware->sortBy('software_name')->map(fn($s) => [
                        'name' => $s->software_name,
                        'version' => $s->software_version ?: 'v.0.0',
                        'publisher' => $s->software_publisher ?: 'Unknown'
                    ])->values()->toJson() }},
                    get filteredItems() {
                        if (!this.search) return this.items;
                        const q = this.search.toLowerCase();
                        return this.items.filter(item => item.name.toLowerCase().includes(q));
                    },
                    get paginatedItems() {
                        const start = (this.currentPage - 1) * this.perPage;
                        return this.filteredItems.slice(start, start + this.perPage);
                    },
                    get totalPages() {
                        return Math.ceil(this.filteredItems.length / this.perPage);
                    },
                    get filteredCount() {
                        return this.filteredItems.length;
                    },
                    getVisiblePages() {
                        let pages = [];
                        const maxVisible = 5;
                        const half = Math.floor(maxVisible / 2);
                        let start = Math.max(this.currentPage - half, 1);
                        let end = Math.min(start + maxVisible - 1, this.totalPages);
                        if (end - start + 1 < maxVisible) {
                            start = Math.max(end - maxVisible + 1, 1);
                        }
                        for (let i = start; i <= end; i++) {
                            pages.push(i);
                        }
                        return pages;
                    },
                    init() {
                        this.$watch('search', () => this.currentPage = 1);
                    }
                }" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.015)] overflow-hidden transition-all hover:shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                    <div class="p-5 border-b border-slate-200/80 dark:border-slate-800/80 flex flex-col sm:flex-row justify-between sm:items-center gap-3 relative z-10">
                        <div class="flex flex-col">
                            <h3 class="text-sm font-black text-bps-blue uppercase tracking-widest">Software Inventory</h3>
                            <p class="text-[9px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-tighter mt-0.5">Installed Applications</p>
                        </div>
                        <div class="flex items-center gap-2.5 w-full sm:w-auto">

                            <div class="relative flex-1 sm:flex-initial">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input x-model="search" type="text" placeholder="Search software..."
                                    class="w-full sm:w-44 pl-8 pr-3 py-1 bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-700 focus:bg-white dark:focus:bg-slate-800 border border-slate-200/60 dark:border-slate-700/50 focus:border-bps-blue rounded-xl text-[11px] font-bold focus:ring-0 transition-all outline-none dark:text-slate-200">
                            </div>
                            <span class="px-3 py-1 bg-bps-blue text-white text-[9px] font-black rounded-full shadow-sm uppercase tracking-widest shrink-0">
                                <span x-text="filteredCount + (filteredCount === 1 ? ' Item' : ' Items')"></span>
                            </span>
                        </div>
                    </div>

                    <div class="p-0">
                        @if($report->installedSoftware->count() > 0)
                        <div class="overflow-x-auto max-h-[380px] overflow-y-auto custom-scrollbar will-change-transform">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-55 dark:bg-slate-800/80 text-gray-500 dark:text-slate-400 text-[9px] font-black uppercase tracking-[0.2em]">
                                        <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-800/50">Application Name</th>
                                        <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-800/50">Version</th>
                                        <th class="px-4 py-3 border-b border-slate-100 dark:border-slate-800/50 hidden sm:table-cell">Publisher</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    <template x-for="item in paginatedItems" :key="item.name">
                                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all duration-200">
                                            <td class="px-4 py-2.5 relative">
                                                <div class="absolute inset-y-0 left-0 w-1 bg-bps-orange scale-y-0 group-hover:scale-y-100 transition-transform origin-top duration-300"></div>
                                                <div class="flex items-center gap-2.5">
                                                    <div class="shrink-0 software-icon w-7 h-7 rounded-lg flex items-center justify-center border transition-all duration-300 shadow-sm"
                                                         :style="'--char-hue: ' + ((item.name.charCodeAt(0) * 23) % 360) + 'deg'">
                                                        <span class="text-[10px] font-black" x-text="item.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                    <span class="text-xs font-black text-bps-blue transition-colors leading-tight" x-text="item.name"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-850 text-slate-650 dark:text-slate-350 text-[9px] font-black rounded border border-slate-200/60 dark:border-slate-700/50 group-hover:bg-white dark:group-hover:bg-slate-800 transition-colors" x-text="item.version"></span>
                                            </td>
                                            <td class="px-4 py-2.5 hidden sm:table-cell">
                                                <span class="text-[11px] font-bold text-gray-450 dark:text-slate-550 group-hover:text-gray-600 dark:group-hover:text-slate-400 transition-colors leading-none" x-text="item.publisher"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            <div x-show="filteredItems.length === 0" class="text-center py-10">
                                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 border border-dashed border-slate-200 dark:border-slate-700">
                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-450 dark:text-slate-400 uppercase tracking-widest">No matching software found</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Try searching with a different term.</p>
                            </div>
                        </div>

                        <div x-show="totalPages > 1" class="px-4 py-3 bg-slate-50/50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                Showing <span class="font-black text-slate-700 dark:text-slate-200" x-text="((currentPage - 1) * perPage) + 1"></span> to
                                <span class="font-black text-slate-700 dark:text-slate-200" x-text="Math.min(currentPage * perPage, filteredCount)"></span> of
                                <span class="font-black text-slate-700 dark:text-slate-200" x-text="filteredCount"></span> entries
                            </div>
                            <div class="flex items-center gap-1">

                                <button @click="currentPage > 1 ? currentPage-- : null"
                                        :disabled="currentPage === 1"
                                        type="button"
                                        class="flex items-center justify-center p-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 text-slate-650 dark:text-slate-350 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>

                                <template x-for="p in getVisiblePages()" :key="p">
                                    <button @click="currentPage = p"
                                            class="min-w-[28px] h-7 px-1.5 rounded-lg text-[10px] font-black transition-all flex items-center justify-center border shadow-sm"
                                            :class="currentPage === p
                                                ? 'bg-bps-blue text-white border-bps-blue shadow-blue-200'
                                                : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-650 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 dark:text-slate-300'"
                                            x-text="p">
                                    </button>
                                </template>

                                <button @click="currentPage < totalPages ? currentPage++ : null"
                                        :disabled="currentPage === totalPages"
                                        type="button"
                                        class="flex items-center justify-center p-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 text-slate-650 dark:text-slate-350 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 border border-dashed border-gray-200 dark:border-slate-700">
                                <svg class="w-6 h-6 text-gray-300 dark:text-slate-655" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400 dark:text-slate-550 uppercase tracking-widest">No Software Installed</p>
                            <p class="text-[10px] text-gray-450 dark:text-slate-550 mt-0.5">Belum ada data aplikasi yang terdeteksi pada perangkat ini.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>

    .text-bps-blue {
        color: #005A8C !important;
    }
    .dark .text-bps-blue {
        color: #60a5fa !important;
    }
    .bg-bps-blue {
        background-color: #005A8C !important;
    }
    .dark .bg-bps-blue {
        background-color: #3b82f6 !important;
    }

    .card-icon-container-blue {
        background-color: #f0f9ff !important;
        border: 1.5px solid #e0f2fe !important;
    }
    .dark .card-icon-container-blue {
        background-color: rgba(59, 130, 246, 0.1) !important;
        border: 1.5px solid rgba(59, 130, 246, 0.2) !important;
    }
    .card-icon-blue {
        color: #005A8C !important;
    }
    .dark .card-icon-blue {
        color: #60a5fa !important;
    }

    .status-badge-offline {
        background-color: #fef2f2 !important;
        border: 1.5px solid #fca5a5 !important;
        color: #ef4444 !important;
    }
    .dark .status-badge-offline {
        background-color: rgba(239, 68, 68, 0.1) !important;
        border: 1.5px solid rgba(239, 68, 68, 0.3) !important;
        color: #f87171 !important;
    }
    .status-badge-connected {
        background-color: #f0fdf4 !important;
        border: 1.5px solid #bbf7d0 !important;
        color: #22c55e !important;
    }
    .dark .status-badge-connected {
        background-color: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid rgba(34, 197, 94, 0.3) !important;
        color: #4ade80 !important;
    }

    .btn-terminate {
        background-color: #ffffff !important;
        border: 1.5px solid #fca5a5 !important;
        color: #ef4444 !important;
    }
    .btn-terminate:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #ef4444 !important;
    }
    .dark .btn-terminate {
        background-color: rgba(239, 68, 68, 0.1) !important;
        border: 1.5px solid rgba(239, 68, 68, 0.3) !important;
        color: #f87171 !important;
    }
    .dark .btn-terminate:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
    }

    .warning-banner-red {
        background-color: #fef2f2 !important;
        border: 1.5px solid #fee2e2 !important;
        color: #ef4444 !important;
    }
    .dark .warning-banner-red {
        background-color: rgba(239, 68, 68, 0.1) !important;
        border: 1.5px solid rgba(239, 68, 68, 0.2) !important;
        color: #f87171 !important;
    }
    .warning-banner-amber {
        background-color: #fffbeb !important;
        border: 1.5px solid #fef3c7 !important;
        color: #d97706 !important;
    }
    .dark .warning-banner-amber {
        background-color: rgba(245, 158, 11, 0.1) !important;
        border: 1.5px solid rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
    }

    .software-icon {
        background: linear-gradient(135deg, hsl(var(--char-hue), 85%, 96%), hsl(var(--char-hue), 75%, 90%));
        border-color: hsl(var(--char-hue), 50%, 85%) !important;
        color: hsl(var(--char-hue), 80%, 35%) !important;
    }
    .dark .software-icon {
        background: linear-gradient(135deg, hsl(var(--char-hue), 40%, 20%), hsl(var(--char-hue), 35%, 15%));
        border-color: hsl(var(--char-hue), 30%, 30%) !important;
        color: hsl(var(--char-hue), 85%, 75%) !important;
    }

    .dark .gauge-ram {
        stroke: var(--ram-color-dark) !important;
    }
    .dark .gauge-disk {
        stroke: var(--disk-color-dark) !important;
    }
</style>