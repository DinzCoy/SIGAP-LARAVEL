<x-app-layout>
    <div class="py-8 space-y-6">

        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Header Titles -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Ringkasan Sistem</h2>
            <p class="text-sm text-gray-500 mt-1">Pantauan real-time aset komputer BPS Sulawesi Selatan.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total PC -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total PC Terdaftar</p>
                    <p class="text-3xl font-bold text-bps-blue mt-2">{{ $totalPcs }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-bps-blue">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <!-- Online -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Online (Aktif)</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $onlinePcs }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Offline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Offline</p>
                    <p class="text-3xl font-bold text-gray-500 mt-2">{{ $offlinePcs }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-full text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
                </div>
            </div>

            <!-- Anomalies -->
            <div class="{{ $anomalyPcs > 0 ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200' }} rounded-xl shadow-sm border p-6 flex items-center justify-between hover:shadow-md transition-shadow relative overflow-hidden">
                @if($anomalyPcs > 0)
                <div class="absolute top-0 right-0 w-2 h-full bg-red-500 animate-pulse"></div>
                @endif
                <div>
                    <p class="text-sm font-medium {{ $anomalyPcs > 0 ? 'text-red-700' : 'text-gray-500' }} uppercase tracking-wider">Terdeteksi Anomali</p>
                    <p class="text-3xl font-bold {{ $anomalyPcs > 0 ? 'text-red-600' : 'text-gray-400' }} mt-2">{{ $anomalyPcs }}</p>
                </div>
                <div class="p-3 {{ $anomalyPcs > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-50 text-gray-400' }} rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filter and Search Section -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wider">Filter Data</h3>
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col md:flex-row gap-5 items-end">
                <!-- Search Box -->
                <div class="flex-1 w-full">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Hostname / IP</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="focus-ring-bps block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 border"
                            placeholder="Contoh: PC-KEUANGAN-01">
                    </div>
                </div>

                <!-- Special Filter -->
                <div class="md:w-64">
                    <label for="filter_spesifik" class="block text-sm font-medium text-gray-700 mb-1">Filter Khusus</label>
                    <select id="filter_spesifik" name="filter_spesifik"
                        class="focus-ring-bps block w-full pl-3 pr-10 py-2 text-base border-gray-300 sm:text-sm rounded-md border">
                        <option value="">-- Semua PC --</option>
                        <option value="bit_defender" {{ request('filter_spesifik') == 'bit_defender' ? 'selected' : '' }}>Bit Defender</option>
                        <option value="office_365" {{ request('filter_spesifik') == 'office_365' ? 'selected' : '' }}>Microsoft Office 365</option>
                        <option value="no_bmn" {{ request('filter_spesifik') == 'no_bmn' ? 'selected' : '' }}>Belum Link Nomor BMN</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-3 w-full md:w-auto mt-4 md:mt-0">
                    <button type="submit"
                        class="bg-bps-orange hover:bg-orange-600 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-colors w-full md:w-auto flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Terapkan Filter
                    </button>
                    @if(request()->filled('search') || request()->filled('filter_spesifik'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="bg-gray-100 border border-gray-300 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center justify-center w-full md:w-auto">
                        Reset
                    </a>
                    @endif
                    
                    <a href="{{ route('admin.reports.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center justify-center w-full md:w-auto gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Data
                    </a>
                </div>
            </form>
        </div>

        <!-- Dashboard Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Daftar PC Status</h2>
                <span class="bg-blue-100 text-bps-blue text-xs font-bold px-3 py-1 rounded-full border border-blue-200">
                    Menampilkan {{ $reports->count() }} dari {{ $reports->total() }} PC
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Perangkat</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Jaringan</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Aset BMN</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Memory</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Penyimpanan</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Terakhir Aktif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($reports as $report)
                        @php
                            $isOffline = $report->last_seen ? \Carbon\Carbon::parse($report->last_seen) < now()->subMinutes(5) : true;
                            $freeRamGb = ($report->ram_free_kb ?? 0) / 1024 / 1024;
                            $totalRamGb = ($report->total_ram_kb ?? 0) / 1024 / 1024;
                            $freeDiskGb = ($report->disk_free_b ?? 0) / 1024 / 1024 / 1024;
                            $totalDiskGb = ($report->total_disk_b ?? 0) / 1024 / 1024 / 1024;

                            $ramWarning = $freeRamGb < 2.0; 
                            $diskCritical = $freeDiskGb < 10.0; 
                            $diskWarning = $freeDiskGb >= 10.0 && $freeDiskGb < 25.0; 
                            
                            $diskStatusColor = $report->disk_status === 'HEALTHY' ? 'text-green-600' : 'text-red-600 font-bold animate-pulse';
                        @endphp

                        <tr onclick="window.location.href='{{ route('admin.reports.show', $report->id) }}'" 
                            class="group hover:bg-blue-50/50 transition-all cursor-pointer {{ $isOffline ? 'bg-gray-50/50 grayscale-[20%]' : 'bg-white' }} {{ $report->is_trouble ? 'border-l-4 border-l-red-500 bg-red-50/30' : 'border-l-4 border-l-transparent' }}">
                            
                            <!-- Perangkat -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative flex min-h-[12px] min-w-[12px] items-center justify-center">
                                        @if(!$isOffline)
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        @endif
                                        <span class="relative inline-flex rounded-full h-3 w-3 {{ $isOffline ? 'bg-gray-400' : 'bg-green-500' }}" title="{{ $isOffline ? 'Offline' : 'Online' }}"></span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 flex items-center gap-2 group-hover:text-bps-blue transition-colors text-base">
                                            {{ $report->hostname }}
                                            @if($report->is_trouble)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase tracking-wide" title="{{ $report->trouble_note }}">Anomali</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                            {{ $report->asset ? $report->asset->bmn_number : 'Belum Link Nomor BMN' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Jaringan -->
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $report->ip_address }}</div>
                                <div class="font-mono text-[11px] text-gray-400 mt-1">{{ $report->mac_address }}</div>
                            </td>

                            <!-- Aset BMN -->
                            <td class="px-6 py-4">
                                @if($report->asset)
                                    <div class="text-sm font-semibold text-gray-900">{{ $report->asset->bmn_number }}</div>
                                    <div class="mt-1">
                                        @if($report->asset->status_kondisi == 'Baik')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">Baik</span>
                                        @elseif($report->asset->status_kondisi == 'Rusak Ringan')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800">Rusak Berat</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Belum Ditautkan</span>
                                @endif
                            </td>

                            <!-- Memory -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold {{ $ramWarning ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-500/10' }}">
                                        {{ number_format($freeRamGb, 1) }} GB free
                                    </span>
                                    <span class="text-xs text-gray-400">/ {{ number_format($totalRamGb, 1) }} GB</span>
                                </div>
                            </td>

                            <!-- Penyimpanan -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col justify-center">
                                    <div class="flex items-center gap-2">
                                        @if($diskCritical)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 animate-pulse" title="Kritis! Disk C penuh">
                                                ⚠️ {{ number_format($freeDiskGb, 1) }} GB free
                                            </span>
                                        @elseif($diskWarning)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20" title="Peringatan Storage">
                                                {{ number_format($freeDiskGb, 1) }} GB free
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-500/10">
                                                {{ number_format($freeDiskGb, 1) }} GB free
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-400">/ {{ number_format($totalDiskGb, 0) }} GB</span>
                                    </div>
                                    @if($report->disk_status && $report->disk_status !== 'HEALTHY')
                                        <div class="text-[10px] mt-2 font-semibold {{ $diskStatusColor }}">S.M.A.R.T: {{ $report->disk_status }}</div>
                                    @endif
                                </div>
                            </td>

                            <!-- Terakhir Aktif -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($report->last_seen)
                                    <div class="text-sm font-medium {{ $isOffline ? 'text-gray-500' : 'text-gray-900' }}">{{ \Carbon\Carbon::parse($report->last_seen)->timezone(config('app.timezone'))->diffForHumans() }}</div>
                                    <div class="text-[11px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($report->last_seen)->timezone(config('app.timezone'))->format('d M y, H:i') }}</div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Belum Ada Data</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 p-4 rounded-full mb-4">
                                        <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900">Tidak ada data PC</h3>
                                    <p class="mt-1 text-sm text-gray-500 max-w-sm">Belum ada perangkat yang terhubung atau tidak ada hasil pencarian yang cocok dengan filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $reports->links() }}
            </div>
            @endif
        </div>

    </div>
</x-app-layout>