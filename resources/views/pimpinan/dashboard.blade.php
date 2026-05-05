<x-app-layout>
    <div class="py-8 space-y-6" x-data="pimpinanDash()" x-init="init()">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Command Center Eksekutif</h2>
                <p class="text-sm text-gray-500 mt-1">Pantauan strategis kondisi aset BMN dan performa layanan IT secara
                    real-time.</p>
            </div>
            <div class="flex items-center gap-3 print:hidden">
                <button onclick="window.print()"
                    class="bg-white border border-gray-300 hover:bg-gray-50 hover:text-bps-blue active:scale-95 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm transition-all duration-200 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

        <!-- KPI Ringkasan (High-Level Overview) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Laporan -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Laporan</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-4xl font-black text-gray-900">{{ $totalTickets }}</h3>
                    <span class="text-sm text-gray-400 font-medium">Tiket</span>
                </div>
            </div>

            <!-- Tiket Selesai -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 rounded-xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 bg-green-100 text-green-700 rounded-full group-hover:bg-green-700 group-hover:text-white transition-colors">{{ $completionRate }}% Rate</span>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tiket Selesai</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-4xl font-black text-gray-900">{{ $completedTickets }}</h3>
                    <span class="text-sm text-gray-400 font-medium">Solved</span>
                </div>
            </div>

            <!-- Kepatuhan SLA -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 {{ $slaComplianceRate >= 80 ? 'bg-green-100 text-green-700 group-hover:bg-green-700 group-hover:text-white' : 'bg-red-100 text-red-700 group-hover:bg-red-700 group-hover:text-white' }} rounded-full transition-colors">{{ $slaComplianceRate }}% Terpenuhi</span>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Kepatuhan SLA</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-4xl font-black text-gray-900">{{ $slaFulfilled }}</h3>
                    <span class="text-sm text-gray-400 font-medium">Laporan Tepat Waktu</span>
                </div>
            </div>

            <!-- Usia Rata-Rata -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Usia Rata-Rata</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-4xl font-black text-gray-900">{{ number_format($avgAssetAge, 1) }}</h3>
                    <span class="text-sm text-gray-400 font-medium">Tahun</span>
                </div>
            </div>
        </div>

        <!-- Analitik Laporan dan Aset -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div
                class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Tren Laporan Bulanan</h3>
                <div class="relative h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-md transition-shadow duration-300 print:shadow-none"
                x-data="{ tab: 'kondisi' }">
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg mb-6 print:hidden">
                    <button @click="tab = 'kondisi'"
                        :class="{ 'bg-white shadow-sm text-gray-900 border border-gray-200/50': tab === 'kondisi', 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50': tab !== 'kondisi' }"
                        class="flex-1 py-1.5 text-sm font-medium rounded-md transition-all active:scale-95 duration-200">
                        Kondisi Aset
                    </button>
                    <button @click="tab = 'usia'"
                        :class="{ 'bg-white shadow-sm text-gray-900 border border-gray-200/50': tab === 'usia', 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50': tab !== 'usia' }"
                        class="flex-1 py-1.5 text-sm font-medium rounded-md transition-all active:scale-95 duration-200">
                        Sebaran Usia
                    </button>
                </div>

                <div x-show="tab === 'kondisi'" class="flex-1 flex flex-col items-center justify-center relative"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="relative h-52 w-full flex items-center justify-center">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>

                <div x-show="tab === 'usia'" style="display: none;"
                    class="flex-1 flex flex-col items-center justify-center relative"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="relative h-52 w-full flex items-center justify-center">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performa Teknisi dan Evaluasi -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="p-5 border-b border-gray-200 bg-gray-50/80">
                    <h3 class="text-lg font-semibold text-gray-800">Performa Teknisi</h3>
                </div>
                <div class="p-3">
                    <ul class="space-y-1">
                        @forelse($technicianStats as $tech)
                        @php
                        $total = $tech->total_count > 0 ? $tech->total_count : 1;
                        $pct = round(($tech->completed_count / $total) * 100);
                        @endphp
                        <li>
                            <div
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors group cursor-default">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-bps-blue flex items-center justify-center font-bold text-sm ring-2 ring-white group-hover:bg-bps-blue group-hover:text-white transition-colors">
                                        {{ strtoupper(substr($tech?->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-900 group-hover:text-bps-blue transition-colors">
                                            {{ $tech?->name }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">
                                            <span class="text-green-600 font-medium">{{ $tech->completed_count }}
                                                Selesai</span>
                                            <span class="mx-1 text-gray-300">•</span>
                                            <span class="text-indigo-600 font-medium">{{ $tech->in_progress_count }}
                                                Aktif</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-block px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-md shadow-sm group-hover:bg-bps-blue group-hover:text-white transition-colors">
                                        {{ $pct }}%
                                    </span>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center py-6 text-sm text-gray-500">Belum ada data teknisi.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="p-5 border-b border-gray-200 bg-gray-50/80">
                    <h3 class="text-lg font-semibold text-gray-800">Evaluasi Peremajaan (Aset Tertua)</h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($oldestAssets as $asset)
                        @php
                        $procDate = $asset->deviceName->procurement_date ?? null;
                        $ageYears = $procDate ? (int) abs(now()->diffInYears($procDate)) : 0;
                        $isUrgent = $ageYears >= 5;
                        $badgeClass = $isUrgent ? 'bg-red-50 text-red-700 ring-1 ring-red-600/20 ring-inset' : 'bg-yellow-50 text-yellow-800 ring-1 ring-yellow-600/20 ring-inset';
                        @endphp
                        <li
                            class="p-5 flex justify-between items-center hover:bg-blue-50/50 cursor-pointer transition-colors group">
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 group-hover:text-bps-blue transition-colors">
                                    {{ $asset->deviceName?->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Ruangan: {{ $asset->room?->nama_ruangan ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badgeClass }} group-hover:scale-105 transition-transform">
                                    {{ $ageYears }} Tahun
                                </span>
                                <p class="text-[11px] text-gray-400 mt-1">Pengadaan
                                    {{ $procDate ? $procDate->format('Y') : '-' }}
                                </p>
                            </div>
                        </li>
                        @empty
                        <li class="p-6 text-center text-sm text-gray-500">Data aset tidak ditemukan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Log Laporan Terbaru -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 hover:shadow-md transition-shadow duration-300">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/80">
                <h3 class="text-lg font-semibold text-gray-800">Log Laporan Terbaru</h3>
                <a href="{{ route('tickets.index') }}"
                    class="text-sm font-medium text-bps-blue hover:text-blue-800 hover:underline underline-offset-4 transition-all print:hidden">Lihat
                    Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Judul & BMN</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Pelapor / Teknisi</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr data-href="{{ route('tickets.show', $ticket->id) }}" onclick="window.location.href=this.dataset.href;"
                            class="hover:bg-blue-50/50 transition-colors bg-white cursor-pointer group">
                            <td class="px-6 py-4">
                                <div
                                    class="font-medium text-gray-900 truncate max-w-xs group-hover:text-bps-blue transition-colors">
                                    {{ $ticket->title }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                    {{ $ticket->asset?->bmn_number ?? 'NON-BMN' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $ticket->creator?->name ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500 mt-1">Teknisi:
                                    {{ $ticket->technician?->name ?? 'Belum Ditugaskan' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($ticket->status === 'Selesai')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 group-hover:scale-105 transition-transform">Selesai</span>
                                @elseif($ticket->status === 'In Progress')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 group-hover:scale-105 transition-transform">In
                                    Progress</span>
                                @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-500/10 group-hover:scale-105 transition-transform">{{ $ticket->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->created_at->format('H:i') }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-1">
                                    {{ $ticket->created_at->translatedFormat('d M y') }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada laporan tiket
                                masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Chart Configuration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.pimpinanDashboardData = <?php echo json_encode([
            'totalAssets' => $totalAssets,
            'trendLabels' => $trendLabels,
            'trendValues' => $trendValues,
            'baikAssets' => $baikAssets,
            'rusakRinganAssets' => $rusakRinganAssets,
            'rusakBeratAssets' => $rusakBeratAssets,
            'ageDistLabels' => $ageDistData->keys(),
            'ageDistValues' => $ageDistData->values()
        ]); ?>;
    </script>
</x-app-layout>