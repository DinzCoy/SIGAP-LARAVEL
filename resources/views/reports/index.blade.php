<x-app-layout>
    <div class="space-y-6">

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

        <!-- Filter and Search Section -->
        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 mb-6">
            <form action="{{ url('/') }}" method="GET" class="flex flex-col md:flex-row gap-4">

                <!-- Search Box -->
                <div class="flex-1">
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
                    <label for="filter_spesifik" class="block text-sm font-medium text-gray-700 mb-1">Filter
                        Khusus</label>
                    <select id="filter_spesifik" name="filter_spesifik"
                        class="focus-ring-bps block w-full pl-3 pr-10 py-2 text-base border-gray-300 sm:text-sm rounded-md border">
                        <option value="">-- Semua PC --</option>
                        <option value="bit_defender" {{ request('filter_spesifik') == 'bit_defender' ? 'selected' : '' }}>
                            Bit Defender</option>
                        <option value="office_365" {{ request('filter_spesifik') == 'office_365' ? 'selected' : '' }}>
                            Microsoft Office 365</option>
                        <option value="no_bmn" {{ request('filter_spesifik') == 'no_bmn' ? 'selected' : '' }}>Belum Link
                            Nomor BMN</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="bg-bps-orange hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-md shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Terapkan
                    </button>
                    @if(request()->filled('search') || request()->filled('filter_spesifik'))
                        <a href="{{ url('/') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md shadow-sm transition-colors text-sm flex items-center">
                            Reset
                        </a>
                    @endif
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
                    <thead
                        class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200 whitespace-nowrap">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold whitespace-nowrap">Status / Hostname</th>
                            <th scope="col" class="px-6 py-4 font-semibold whitespace-nowrap">Ruangan</th>
                            <th scope="col" class="px-6 py-4 font-semibold whitespace-nowrap">IP / MAC</th>
                            <th scope="col" class="px-6 py-4 font-semibold whitespace-nowrap">OS Detail</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-center whitespace-nowrap">RAM Free /
                                Total</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-center whitespace-nowrap">Disk C: Free /
                                Total</th>
                            <th scope="col" class="px-6 py-4 font-semibold whitespace-nowrap">Terakhir Aktif</th>
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

                                /* Warning Logic */
                                $ramWarning = $freeRamGb < 2.0;
                                $diskCritical = $freeDiskGb < 10.0;
                                $diskWarning = $freeDiskGb >= 10.0 && $freeDiskGb < 25.0;
                                $diskStatusColor = in_array($report->disk_status, ['HEALTHY', 'SEHAT']) ? 'text-green-600' : 'text-red-600 font-bold animate-pulse';
                            @endphp

                            <tr
                                class="{{ $report->is_trouble ? 'bg-red-50 hover:bg-red-100' : 'bg-white hover:bg-blue-50' }} transition-colors {{ $isOffline ? 'opacity-75' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex h-3 w-3">
                                            @if(!$isOffline)
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            @endif
                                            <span
                                                class="relative inline-flex rounded-full h-3 w-3 {{ $isOffline ? 'bg-red-500' : 'bg-green-500' }}"
                                                title="{{ $isOffline ? 'Offline' : 'Online' }}"></span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 flex items-center gap-2">
                                                {{ $report->hostname }}
                                                @if($report->is_trouble)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-600 text-white shadow-sm animate-pulse"
                                                        title="{{ $report->trouble_note }}">
                                                        ANOMALY
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-4 font-medium {{ $report->is_trouble ? 'text-red-700' : 'text-bps-blue' }} whitespace-nowrap">
                                    {{ $report->room_name ?: '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @auth
                                        <div class="font-mono text-sm text-gray-800">{{ $report->ip_address }}</div>
                                    @else
                                        <div class="font-mono text-sm text-gray-500 italic">Hidden (Login Required)
                                        </div>
                                    @endauth
                                    <div class="font-mono text-xs text-gray-400">{{ $report->mac_address }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="block text-gray-800 font-medium truncate max-w-[150px]"
                                        title="{{ $report->os_name }}">{{ $report->os_name }}</span>
                                    <span class="block text-xs text-gray-500">Build: {{ $report->os_build }}</span>
                                    @if($report->last_patch)
                                        <span class="block text-[10px] text-gray-400 mt-1" title="Last Windows Patch">Patch:
                                            {{ $report->last_patch }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center justify-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ramWarning ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-gray-100 text-gray-800' }}">
                                            {{ number_format($freeRamGb, 2) }} GB
                                        </span>
                                        <span class="text-[10px] text-gray-400 mt-1">
                                            of {{ number_format($totalRamGb, 2) }} GB
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center justify-center">
                                        @if($diskCritical)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border-2 border-red-300 animate-pulse"
                                                title="Sangat Kritis! Segera bersihkan Disk C:">
                                                ⚠️ {{ number_format($freeDiskGb, 2) }} GB
                                            </span>
                                        @elseif($diskWarning)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-300"
                                                title="Peringatan Storage Hampir Penuh">
                                                {{ number_format($freeDiskGb, 2) }} GB
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ number_format($freeDiskGb, 2) }} GB
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-gray-400 mt-1">
                                            of {{ number_format($totalDiskGb, 2) }} GB
                                        </span>
                                        @if($report->disk_status)
                                            <span class="text-[10px] mt-1 {{ $diskStatusColor }}">
                                                S.M.A.R.T: {{ $report->disk_status }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->last_seen)
                                        <span class="block font-medium text-gray-700">
                                            {{ \Carbon\Carbon::parse($report->last_seen)->timezone(config('app.timezone'))->format('d M y, H:i') }}
                                        </span>
                                        <span class="text-xs {{ $isOffline ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                            {{ \Carbon\Carbon::parse($report->last_seen)->timezone(config('app.timezone'))->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="block font-medium text-gray-500 italic">Belum Ada Data</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center whitespace-nowrap">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" aria-hidden="true">
                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data PC</h3>
                                    <p class="mt-1 text-sm text-gray-500">Belum ada agent yang terhubung atau tidak
                                        ada hasil pencarian.</p>
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