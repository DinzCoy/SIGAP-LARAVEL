<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center leading-tight">
            <h2 class="font-semibold text-xl text-gray-800">
                {{ __('Dashboard Eksekutif') }}
            </h2>
            <button onclick="window.print()"
                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-bold py-1.5 px-3 rounded-lg transition shadow-sm text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Cetak Laporan
            </button>
        </div>
    </x-slot>

    <div class="py-12 space-y-6">

        <!-- Hero Header -->
        <div
            class="bg-gradient-to-r from-bps-blue to-blue-800 rounded-2xl shadow-lg mb-8 p-6 lg:p-10 flex flex-col md:flex-row items-center justify-between text-white border border-blue-900/50">
            <div>
                <h1 class="text-3xl font-black tracking-tight mb-2">Ringkasan Layanan & Aset</h1>
                <p class="text-blue-100 max-w-2xl text-sm md:text-base leading-relaxed">Pantau pergerakan aset BMN,
                    status hardware PC (Online/Offline) secara *real-time*, serta efisiensi pelaporan dan perbaikan unit
                    demi menjaga produktivitas instansi.</p>
            </div>
            <div class="mt-6 md:mt-0 flex space-x-4">
                <a href="{{ route('tickets.index') }}"
                    class="bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-sm text-white font-bold py-2.5 px-6 rounded-xl transition shadow-md whitespace-nowrap">
                    Semua Laporan &rarr;
                </a>
            </div>
        </div>

        <!-- 4 Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Aset -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group hover:border-blue-200 transition-colors">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Total Aset Fisik
                            </p>
                            <p class="mt-1 text-4xl font-extrabold text-bps-blue">{{ $totalAssets }}</p>
                        </div>
                        <div class="bg-blue-50 text-bps-blue p-3.5 rounded-2xl shadow-inner border border-blue-100">
                            <i data-lucide="package" class="w-6 h-6"></i>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-bps-blue transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                </div>
            </div>

            <!-- PC Online -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group hover:border-emerald-200 transition-colors">
                <div class="p-6 pb-4 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">PC Aktif Berjalan
                            </p>
                            <p class="mt-1 text-4xl font-extrabold text-emerald-600">{{ $onlinePcs }}</p>
                        </div>
                        <div
                            class="bg-emerald-50 text-emerald-600 p-3.5 rounded-2xl shadow-inner border border-emerald-100">
                            <i data-lucide="activity" class="w-6 h-6"></i>
                        </div>
                    </div>
                    @if($offlinePcs > 0 || $anomalyPcs > 0)
                        <div class="mt-3 bg-red-50 rounded-lg p-2 flex items-center gap-2 border border-red-100">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                            <p class="text-[10px] text-red-600 font-bold uppercase tracking-wide">{{ $offlinePcs }} Offline
                                &bull; {{ $anomalyPcs }} Anomali Suhu</p>
                        </div>
                    @endif
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-emerald-600 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                </div>
            </div>

            <!-- Tiket Selesai -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group hover:border-blue-200 transition-colors">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Laporan
                                Diselesaikan</p>
                            <div class="flex items-baseline gap-1.5 mt-1">
                                <p class="text-4xl font-extrabold text-blue-600">{{ $completedTickets }}</p>
                                <span class="text-sm font-bold text-gray-400">/ {{ $totalTickets }}</span>
                            </div>
                        </div>
                        <div class="bg-blue-50 text-blue-600 p-3.5 rounded-2xl shadow-inner border border-blue-100">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                    </div>
                    <div class="mt-4 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-500 h-1.5 rounded-full relative" style="width: {{ $completionRate }}%">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-wider">SLA:
                        {{ $completionRate }}% Tingkat Penyelesaian
                    </p>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-blue-600 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                </div>
            </div>

            <!-- Total Cost -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group hover:border-orange-200 transition-colors">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pengeluaran Biaya
                            </p>
                            <p class="mt-1 text-2xl font-extrabold text-bps-orange">Rp
                                {{ number_format($totalCost, 0, ',', '.') }}
                            </p>
                        </div>
                        <div
                            class="bg-orange-50 text-bps-orange p-3.5 rounded-2xl shadow-inner border border-orange-100">
                            <i data-lucide="wallet" class="w-6 h-6"></i>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-orange-400 to-bps-orange transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Tren Laporan (Bar Chart) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 md:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-bps-blue"></i>
                        Tren Laporan Masalah (5 Bulan Terakhir)
                    </h3>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Kondisi Fisik Asset (Pie Chart) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 md:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-bps-orange"></i>
                        Proporsi Kondisi Fisik Aset
                    </h3>
                </div>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="conditionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ticket Status Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl lg:col-span-1 border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="list-todo" class="w-5 h-5 text-bps-blue"></i>
                        Status Tiket Berjalan
                    </h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        @php
                            $statusIcons = [
                                'Open' => 'text-gray-500 bg-gray-100',
                                'In Progress' => 'text-blue-500 bg-blue-100',
                                'Menunggu Persetujuan Biaya' => 'text-yellow-600 bg-yellow-100',
                                'Approved' => 'text-emerald-500 bg-emerald-100',
                                'Selesai' => 'text-emerald-600 bg-emerald-100',
                                'Dibatalkan' => 'text-red-500 bg-red-100',
                            ];
                        @endphp
                        @foreach($ticketsByStatus as $status => $count)
                            <li
                                class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:shadow-md hover:border-bps-blue/30 transition-all duration-200">
                                <div class="flex items-center">
                                    <span
                                        class="w-2.5 h-2.5 rounded-full mr-3 shadow-inner {{ explode(' ', $statusIcons[$status] ?? 'text-gray-400 bg-gray-400')[1] ?? 'bg-gray-400' }}"></span>
                                    <span class="text-sm font-semibold text-gray-700">{{ $status }}</span>
                                </div>
                                <span
                                    class="text-xs font-bold bg-gray-50 border border-gray-200 text-gray-700 py-1 px-3 rounded-md shadow-sm">{{ $count }}</span>
                            </li>
                        @endforeach
                        @if($ticketsByStatus->isEmpty())
                            <li
                                class="text-center text-gray-500 text-sm py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                Belum ada aktivitas tiket berjalan.</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Recent Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl lg:col-span-2 border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="history" class="w-5 h-5 text-gray-400"></i>
                        Riwayat Layanan Terbaru
                    </h3>
                    <a href="{{ route('tickets.index') }}"
                        class="text-[13px] text-white bg-bps-blue hover:bg-blue-800 px-4 py-2 rounded-lg font-bold shadow-sm transition-colors">Semua
                        Transaksi &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Perihal Laporan</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Aset (BMN)</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    Estimasi Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($recentTickets as $ticket)
                                <tr class="hover:bg-blue-50/40 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">
                                            {{ \Illuminate\Support\Str::limit($ticket->title, 40) }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 font-medium mt-0.5 flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-600">
                                        {{ $ticket->asset->bmn_number ?? 'Non-BMN' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 inline-flex text-[11px] leading-5 font-bold rounded-lg bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-bps-orange">
                                        {{ $ticket->estimated_cost > 0 ? 'Rp ' . number_format($ticket->estimated_cost, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div
                                            class="bg-gray-50 rounded-xl px-4 py-8 border border-dashed border-gray-200 inline-block w-full">
                                            <p class="text-sm text-gray-500 font-semibold mb-1">Belum Ada Transaksi</p>
                                            <p class="text-[11px] text-gray-400">Tidak ada riwayat perbaikan yang tercatat.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Inject Chart.js directly in view for simplicity -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            // 1. Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($trendLabels) !!},
                    datasets: [{
                        label: 'Lap. Perbaikan',
                        data: {!! json_encode($trendValues) !!},
                        backgroundColor: '#1d4ed8', // bps-blue (tailwind blue-700)
                        borderRadius: 6,
                        barThickness: 24,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { family: "'Inter', sans-serif", size: 11, weight: '500' },
                                color: '#9ca3af'
                            },
                            grid: {
                                color: '#f3f4f6',
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                                color: '#6b7280'
                            }
                        }
                    }
                }
            });

            // 2. Condition Pie Chart
            const conditionCtx = document.getElementById('conditionChart').getContext('2d');
            new Chart(conditionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Sangat Baik', 'Rusak Ringan', 'Rusak Berat'],
                    datasets: [{
                        data: [{{ $baikAssets }}, {{ $rusakRinganAssets }}, {{ $rusakBeratAssets }}],
                        backgroundColor: [
                            '#10b981', // Emerald 500
                            '#f59e0b', // Amber 500
                            '#ef4444'  // Red 500
                        ],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                                color: '#4b5563'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>