<x-app-layout>
    <div class="py-8 space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard Pengelola Ruangan</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau kondisi, sebaran aset, dan laporan pada ruangan yang Anda kelola.</p>
            </div>
            <a href="{{ route('ruangan.lapor') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200 active:scale-95 shrink-0">
                <x-lucide-triangle-alert class="w-4 h-4" />
                Laporkan Kerusakan
            </a>
        </div>

        <!-- Summary Cards (4 kolom) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

            <!-- Total Rooms -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-default group">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider group-hover:text-teal-600 transition-colors">Ruangan Dikelola</p>
                    <p class="text-3xl font-bold text-teal-600 mt-2">{{ $totalRooms }} <span class="text-lg font-medium text-gray-400 group-hover:text-teal-500 transition-colors">ruang</span></p>
                </div>
                <div class="p-3 bg-teal-50 rounded-full text-teal-600 group-hover:scale-110 group-hover:bg-teal-100 transition-all duration-300">
                    <x-lucide-door-open class="w-8 h-8" />
                </div>
            </div>

            <!-- Total Assets -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-default group">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider group-hover:text-bps-blue transition-colors">Aset di Ruangan</p>
                    <p class="text-3xl font-bold text-bps-blue mt-2">{{ $totalAssets }} <span class="text-lg font-medium text-gray-400 group-hover:text-blue-500 transition-colors">unit</span></p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-bps-blue group-hover:scale-110 group-hover:bg-blue-100 transition-all duration-300">
                    <x-lucide-monitor-speaker class="w-8 h-8" />
                </div>
            </div>

            <!-- Aset Bermasalah -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-default group">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider group-hover:text-amber-600 transition-colors">Aset Bermasalah</p>
                    <p class="text-3xl font-bold mt-2 {{ $brokenAssets > 0 ? 'text-amber-500' : 'text-gray-400' }}">
                        {{ $brokenAssets }}
                        <span class="text-lg font-medium text-gray-400 group-hover:text-amber-400 transition-colors">unit</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Rusak Ringan / Berat</p>
                </div>
                <div class="p-3 rounded-full transition-all duration-300 group-hover:scale-110 {{ $brokenAssets > 0 ? 'bg-amber-50 text-amber-500 group-hover:bg-amber-100' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100' }}">
                    <x-lucide-triangle-alert class="w-8 h-8" />
                </div>
            </div>

            <!-- Tiket Aktif -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-default group">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider group-hover:text-rose-600 transition-colors">Tiket Aktif</p>
                    <p class="text-3xl font-bold mt-2 {{ $activeTickets > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                        {{ $activeTickets }}
                        <span class="text-lg font-medium text-gray-400 group-hover:text-rose-400 transition-colors">tiket</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Belum selesai / dibatalkan</p>
                </div>
                <div class="p-3 rounded-full transition-all duration-300 group-hover:scale-110 {{ $activeTickets > 0 ? 'bg-rose-50 text-rose-500 group-hover:bg-rose-100' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100' }}">
                    <x-lucide-ticket class="w-8 h-8" />
                </div>
            </div>

        </div>

        <!-- Recent Assets Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 hover:shadow-md transition-shadow duration-300">
            <div class="p-5 border-b border-gray-200 bg-gray-50/80 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <x-lucide-map-pin class="w-5 h-5 text-teal-600" /> Aset Ruangan Terbaru
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Nama Perangkat</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">No BMN / Serial</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Ruangan</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Kondisi</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Ditambahkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentAssets as $asset)
                        <tr class="hover:bg-teal-50/50 transition-colors bg-white group cursor-default">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 group-hover:text-teal-600 transition-colors">{{ $asset->deviceName?->name ?? 'Unknown Device' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $asset->deviceName?->brand ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $asset->bmn_number ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500 font-mono mt-1">{{ $asset->serial_number ?? 'S/N Not Set' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($asset->room)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-teal-50 text-teal-700 ring-1 ring-inset ring-teal-600/20 group-hover:scale-105 transition-transform">
                                        <x-lucide-door-closed class="w-3.5 h-3.5 mr-1" /> {{ $asset->room?->nama_ruangan ?? $asset->room?->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 group-hover:scale-105 transition-transform">Belum Dialokasikan</span>
                                @endif
                            </td>

                            <!-- Badge Kondisi -->
                            <td class="px-6 py-4 text-center">
                                @php
                                    $kondisi = $asset->status_kondisi ?? 'Baik';
                                    $kondisiStyle = match($kondisi) {
                                        'Rusak Berat'  => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'Rusak Ringan' => 'bg-amber-50 text-amber-700 ring-amber-500/20',
                                        default        => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    };
                                    $kondisiIcon = match($kondisi) {
                                        'Rusak Berat'  => 'x-circle',
                                        'Rusak Ringan' => 'alert-circle',
                                        default        => 'check-circle',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-semibold ring-1 ring-inset group-hover:scale-105 transition-transform {{ $kondisiStyle }}">
                                    <x-dynamic-component :component="'lucide-' . $kondisiIcon" class="w-3.5 h-3.5" />
                                    {{ $kondisi }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ $asset->created_at->format('H:i') }}</div>
                                <div class="text-[11px] text-gray-400 mt-1">{{ $asset->created_at->translatedFormat('d M Y') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-lucide-layout-dashboard class="w-10 h-10 text-gray-300 mb-3" />
                                    <p>Belum ada aset terdaftar di ruangan Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
