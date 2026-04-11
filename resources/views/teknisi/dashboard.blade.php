<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Teknisi IT') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6">

        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl shadow-xl mb-8 p-8 flex flex-col md:flex-row items-center justify-between text-white">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-2">Workspace Perbaikan & Maintenance</h1>
                <p class="text-blue-100 max-w-2xl text-sm md:text-base leading-relaxed">
                    Pantau daftar tiket laporan masalah (PC, Jaringan, dan Aset). Prioritaskan penanganan dengan memantau status 'Open' dan 'In Progress'.
                </p>
            </div>
            <div class="mt-6 md:mt-0 flex shrink-0">
                <a href="{{ route('tickets.index') }}"
                    class="bg-white/10 hover:bg-white/20 border border-white/30 text-white font-semibold py-2.5 px-6 rounded-xl transition duration-200">
                    Lihat Beranda Tiket
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Active/Open Tickets -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tiket Perlu Ditangani</p>
                            <p class="text-4xl font-extrabold text-blue-600">{{ $openTickets }} <span class="text-lg font-medium text-gray-400">tiket</span></p>
                        </div>
                        <div class="bg-blue-50 text-blue-500 p-4 rounded-2xl">
                            <x-lucide-wrench class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-600 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>

            <!-- Completed Tickets -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Laporan Diselesaikan</p>
                            <p class="text-4xl font-extrabold text-emerald-500">{{ $completedTickets }} <span class="text-lg font-medium text-gray-400">tiket</span></p>
                        </div>
                        <div class="bg-emerald-50 text-emerald-500 p-4 rounded-2xl">
                            <x-lucide-check-circle class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-green-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>
        </div>

        <!-- Recent Tickets Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <x-lucide-clock class="w-5 h-5 text-bps-orange" /> Laporan Masalah Terbaru
                </h3>
                <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">Semua Tiket &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Judul Tiket</th>
                            <th class="px-6 py-4 font-semibold">Pelapor</th>
                            <th class="px-6 py-4 font-semibold">BMN Aset</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($ticket->title, 40) }}</div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <x-lucide-calendar class="w-3 h-3" /> {{ $ticket->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-700">{{ $ticket->reporter->name ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->asset)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $ticket->asset->bmn_number ?? 'No BMN' }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Open' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'In Progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Menunggu Persetujuan Biaya' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'Approved' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $badgeClass = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <x-lucide-arrow-right class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-lucide-inbox class="w-12 h-12 text-gray-300 mb-3" />
                                    <p>Belum ada tiket laporan masalah.</p>
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
