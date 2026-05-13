<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-bps-blue to-blue-900 px-6 py-8 -mx-4 sm:-mx-6 lg:-mx-8 -mt-6 mb-6 shadow-lg border-b-4 border-bps-orange group">
            <!-- Abstract Background Patterns -->
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg class="absolute top-0 right-0 w-96 h-96 transform translate-x-1/2 -translate-y-1/2" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="0.5" />
                    <circle cx="50" cy="50" r="30" fill="none" stroke="white" stroke-width="0.5" />
                    <line x1="0" y1="50" x2="100" y2="50" stroke="white" stroke-width="0.2" />
                    <line x1="50" y1="0" x2="50" y2="100" stroke="white" stroke-width="0.2" />
                </svg>
                <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white to-transparent opacity-20"></div>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-3 text-blue-100 mb-1">
                        <a href="{{ url()->previous() }}" class="flex items-center gap-1.5 px-3 py-1 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full text-xs font-bold transition-all backdrop-blur-md group/back">
                            <svg class="w-3.5 h-3.5 transition-transform group-hover/back:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                        <span class="text-white/30">/</span>
                        <span class="text-xs uppercase tracking-widest font-black opacity-70">Device Analytics</span>
                    </div>
                    <h2 class="font-black text-3xl sm:text-4xl text-white tracking-tight flex items-center gap-3 drop-shadow-md">
                        <span class="bg-bps-orange w-2 h-10 rounded-full hidden sm:block"></span>
                        {{ $report->hostname }}
                        <span class="text-blue-300/50 text-xl font-light">#{{ substr($report->id, -4) }}</span>
                    </h2>
                </div>

                <div class="flex items-center gap-4 bg-black/20 p-2 rounded-2xl backdrop-blur-md border border-white/10">
                    <div class="flex items-center gap-3 px-4 py-2 bg-white/5 rounded-xl border border-white/10">
                        @if(!$isOffline)
                        <div class="relative flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-green-300 uppercase tracking-tighter leading-none">Status</span>
                            <span class="text-sm font-black text-white leading-tight uppercase">Connected</span>
                        </div>
                        @else
                        <div class="relative flex h-4 w-4">
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-red-300 uppercase tracking-tighter leading-none">Status</span>
                            <span class="text-sm font-black text-white leading-tight uppercase">Offline</span>
                        </div>
                        @endif
                    </div>

                    <div x-data="{ showDeleteModal: false, confirmText: '' }" class="h-full">
                        <button @click="showDeleteModal = true" type="button" class="group/del h-full flex items-center gap-2 px-4 py-2 bg-red-500/20 hover:bg-red-600 border border-red-500/30 hover:border-red-600 text-red-200 hover:text-white rounded-xl text-sm font-black transition-all shadow-lg shadow-black/20">
                            <svg class="w-4 h-4 transition-transform group-hover/del:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span class="hidden sm:block">Terminate</span>
                        </button>

                        <!-- Modal code remains similar but styled -->
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
        </div>
    </x-slot>


    <div class="space-y-6">
        @if($report->is_trouble)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-bold text-lg">Indikasi Anomali Terdeteksi!</p>
                    <p class="text-sm mt-1">{{ $report->trouble_note }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column (Specs) -->
            <div class="col-span-1 md:col-span-1 space-y-6">

                <!-- ID Card -->
                <div class="bg-white overflow-hidden shadow-2xl shadow-blue-900/5 sm:rounded-3xl border border-gray-100 group/card">
                    <div class="p-6 bg-slate-900 text-white relative overflow-hidden">
                        <!-- Card Decorative background -->
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-bps-blue rounded-full blur-3xl opacity-30 group-hover/card:opacity-50 transition-opacity"></div>
                        
                        <div class="relative z-10 flex flex-col gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/10">
                                <svg class="w-7 h-7 text-bps-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black tracking-tight">{{ $report->hostname }}</h3>
                                <p class="text-xs font-bold text-blue-300 uppercase tracking-widest mt-1">System Identifier</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-bps-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Ruangan</p>
                                <p class="text-sm font-black text-gray-800">{{ $report->room_name ?: 'Unknown Location' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 pt-4 border-t border-gray-50">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-bps-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <div class="w-full">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Network Identity</p>
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-mono font-bold text-gray-700 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 w-fit">{{ $report->ip_address }}</span>
                                    <span class="text-[10px] font-mono text-gray-400">{{ $report->mac_address }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-50">
                            <div class="bg-slate-50 rounded-2xl p-4 border border-gray-100 flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Last Online Activity</p>
                                    <p class="text-xs font-black text-gray-700">{{ $report->last_seen ? $report->last_seen->diffForHumans() : 'No record' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aset BMN Info -->
                <div class="bg-white overflow-hidden shadow-2xl shadow-blue-900/5 sm:rounded-3xl border border-gray-100">
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-5 bg-bps-orange rounded-full"></div>
                            <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Inventory Asset</h3>
                        </div>
                        @if($report->asset)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter {{ $report->asset->status_kondisi == 'Baik' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $report->asset->status_kondisi }}
                        </span>
                        @endif
                    </div>
                    <div class="p-6">
                        @if($report->asset)
                        <div class="space-y-4">
                            <div class="p-4 bg-bps-blue/5 rounded-2xl border border-bps-blue/10">
                                <p class="text-[10px] font-black text-bps-blue/60 uppercase tracking-widest leading-none mb-1">BMN Number</p>
                                <p class="font-black text-gray-900 text-xl tracking-tight">{{ $report->asset->bmn_number }}</p>
                            </div>
                            <div class="flex justify-between items-center px-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase">Serial Number</span>
                                <span class="text-xs font-bold text-gray-800">{{ $report->asset->serial_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Unlinked Device</p>
                            <p class="text-[11px] text-gray-400 mt-1 px-4">Tautkan ke data BMN di menu Asset Manager</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>


            <!-- Right Column (Resources & Logs) -->
            <div class="col-span-1 md:col-span-2 space-y-6">

                <!-- Hardware Resources -->
                <div class="bg-white overflow-hidden shadow-2xl shadow-blue-900/5 sm:rounded-3xl border border-gray-100">
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-bps-blue rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Hardware Architecture</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $report->os_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-xl border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-xs font-black text-slate-600 font-mono">Build {{ $report->os_build }}</span>
                        </div>
                    </div>
                    
                    <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                        <!-- RAM Monitor -->
                        @php
                        $totalRamGb = round(($report->total_ram_kb ?? 0) / 1024 / 1024, 2);
                        $freeRamGb = round(($report->ram_free_kb ?? 0) / 1024 / 1024, 2);
                        $usedRamGb = $totalRamGb > 0 ? round($totalRamGb - $freeRamGb, 2) : 0;
                        $ramPercent = $totalRamGb > 0 ? round(($usedRamGb / $totalRamGb) * 100) : 0;
                        @endphp
                        <div class="relative group/metric">
                            <div class="flex justify-between items-end mb-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Random Access Memory</span>
                                    <span class="text-sm font-black text-gray-800">Physical Memory</span>
                                </div>
                                <span class="text-xl font-black {{ $ramPercent > 85 ? 'text-red-600' : 'text-bps-blue' }}">{{ $ramPercent }}%</span>
                            </div>
                            @php
                                $ramBarClass = $ramPercent > 85 
                                    ? 'bg-gradient-to-r from-red-400 to-red-600' 
                                    : ($ramPercent > 70 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-bps-blue to-blue-400');
                            @endphp
                            <div class="w-full bg-gray-100 rounded-full h-4 mb-4 p-1 overflow-hidden border border-gray-200/50">
                                <div @class(['h-full rounded-full transition-all duration-1000 shadow-sm', $ramBarClass]) @style(['width' => $ramPercent . '%'])></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Used</p>
                                    <p class="text-xs font-black text-gray-800">{{ $usedRamGb }} GB</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Available</p>
                                    <p class="text-xs font-black text-gray-800">{{ $totalRamGb }} GB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Storage Monitor -->
                        @php
                        $totalDiskGb = round(($report->total_disk_b ?? 0) / 1024 / 1024 / 1024, 2);
                        $freeDiskGb = round(($report->disk_free_b ?? 0) / 1024 / 1024 / 1024, 2);
                        $usedDiskGb = $totalDiskGb > 0 ? round($totalDiskGb - $freeDiskGb, 2) : 0;
                        $diskPercent = $totalDiskGb > 0 ? round(($usedDiskGb / $totalDiskGb) * 100) : 0;
                        @endphp
                        <div class="relative group/metric">
                            <div class="flex justify-between items-end mb-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Primary Partition</span>
                                    <span class="text-sm font-black text-gray-800">Local Disk (C:)</span>
                                </div>
                                <span class="text-xl font-black {{ $diskPercent > 90 ? 'text-red-600' : 'text-bps-blue' }}">{{ $diskPercent }}%</span>
                            </div>
                            @php
                                $diskBarClass = $diskPercent > 90 
                                    ? 'bg-gradient-to-r from-red-400 to-red-600' 
                                    : ($diskPercent > 80 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-green-400 to-green-600');
                            @endphp
                            <div class="w-full bg-gray-100 rounded-full h-4 mb-4 p-1 overflow-hidden border border-gray-200/50">
                                <div @class(['h-full rounded-full transition-all duration-1000 shadow-sm', $diskBarClass]) @style(['width' => $diskPercent . '%'])></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Remaining</p>
                                    <p class="text-xs font-black text-gray-800">{{ $freeDiskGb }} GB</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1">Capacity</p>
                                    <p class="text-xs font-black text-gray-800">{{ $totalDiskGb }} GB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($report->is_trouble)
                <!-- Anomaly Alert -->
                <div class="bg-red-50 overflow-hidden shadow-xl shadow-red-900/10 sm:rounded-3xl border-2 border-red-200 animate-pulse-slow">
                    <div class="p-4 border-b border-red-100 bg-red-100/50 flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center shadow-lg shadow-red-200">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-black text-red-900 uppercase tracking-widest">Security Anomaly Detected</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-white/50 rounded-2xl p-4 border border-red-100">
                            <p class="text-sm font-bold text-red-800 leading-relaxed">{{ $report->trouble_note }}</p>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-white border border-red-100 rounded-full text-[10px] font-black text-red-600 uppercase">Action Required</span>
                            <span class="px-3 py-1 bg-white border border-red-100 rounded-full text-[10px] font-black text-red-600 uppercase tracking-tighter">Check BitDefender status</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Software Inventory List -->
                <div class="bg-white overflow-hidden shadow-2xl shadow-blue-900/5 sm:rounded-3xl border border-gray-100">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                        <div class="flex flex-col">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Software Inventory</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Installed Applications & Packages</p>
                        </div>
                        <span class="px-4 py-1.5 bg-bps-blue text-white text-[11px] font-black rounded-full shadow-lg shadow-blue-200 uppercase tracking-widest">
                            {{ $report->installedSoftware->count() }} Items
                        </span>
                    </div>
                    <div class="p-0">
                        @if($report->installedSoftware->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/80 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                        <th class="px-6 py-4 border-b border-gray-100">Application Name</th>
                                        <th class="px-6 py-4 border-b border-gray-100">Version</th>
                                        <th class="px-6 py-4 border-b border-gray-100 hidden sm:table-cell">Publisher</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($report->installedSoftware->sortBy('software_name') as $software)
                                    <tr class="group hover:bg-blue-50/50 transition-all duration-300">
                                        <td class="px-6 py-4 relative">
                                            <div class="absolute inset-y-0 left-0 w-1 bg-bps-orange scale-y-0 group-hover:scale-y-100 transition-transform origin-top duration-300"></div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-gray-50 group-hover:bg-white flex items-center justify-center border border-gray-100 transition-colors shadow-sm">
                                                    <span class="text-[10px] font-black text-gray-400 group-hover:text-bps-blue">{{ substr($software->software_name, 0, 1) }}</span>
                                                </div>
                                                <span class="text-sm font-black text-gray-700 group-hover:text-bps-blue transition-colors">{{ $software->software_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-gray-50 text-gray-500 text-[10px] font-black rounded-md border border-gray-100 group-hover:bg-white transition-colors">
                                                {{ $software->software_version ?: 'v.0.0' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 hidden sm:table-cell">
                                            <span class="text-xs font-bold text-gray-400 group-hover:text-gray-500 transition-colors">{{ $software->software_publisher ?: 'Unknown' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="p-16 text-center bg-gray-50/30">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-inner">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-black text-gray-400 uppercase tracking-widest">No Applications Tracked</p>
                            <p class="text-[11px] text-gray-400 mt-1">Sistem sedang menunggu sinkronisasi data dari Agent.</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>