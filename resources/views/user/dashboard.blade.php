<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8 space-y-8">
        <!-- Hero Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 rounded-3xl shadow-2xl p-8 border border-white/10 group">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white/10 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-110"></div>
            <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-blue-400/20 rounded-full blur-2xl transition-transform duration-700 group-hover:scale-125"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    @php
                        $hour = date('H');
                        $greeting = 'Selamat Datang';
                        if ($hour >= 5 && $hour < 11) $greeting = 'Selamat Pagi';
                        elseif ($hour >= 11 && $hour < 15) $greeting = 'Selamat Siang';
                        elseif ($hour >= 15 && $hour < 18) $greeting = 'Selamat Sore';
                        else $greeting = 'Selamat Malam';
                    @endphp
                    <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-3 flex items-center gap-3">
                        {{ $greeting }}, {{ Auth::user()->name }} <span class="animate-bounce" style="animation-duration: 2s; transform-origin: bottom center;">👋</span>
                    </h1>
                    <p class="text-blue-100/90 max-w-2xl text-base md:text-lg leading-relaxed font-medium">
                        Sistem Guardian siap membantu Anda memantau aset teknologi dan menangani kendala teknis dengan cepat.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center bg-white text-blue-700 hover:bg-blue-50 font-bold py-3 px-8 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 focus:outline-none">
                        <x-lucide-plus-circle class="w-5 h-5 mr-2" /> Buat Laporan
                    </a>
                    <a href="#" class="inline-flex items-center justify-center bg-blue-500/20 hover:bg-blue-500/30 text-white font-semibold py-3 px-6 rounded-2xl border border-white/20 transition-all backdrop-blur-md hover:-translate-y-1 active:scale-95">
                        <x-lucide-book-open class="w-5 h-5 mr-2" /> Panduan
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Reports Stats -->
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-md border border-gray-100 p-1 flex items-stretch transition-all duration-300 transform hover:-translate-y-1 cursor-default group">
                <div class="flex-1 p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 group-hover:text-blue-500 transition-colors">Statistik Laporan</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-blue-600 group-hover:scale-110 transition-transform origin-left">{{ $myTicketsCount }}</h3>
                        <span class="text-gray-500 font-semibold">Total Tiket</span>
                    </div>
                </div>
                <div class="bg-blue-50 group-hover:bg-blue-600 rounded-2xl m-2 px-6 flex items-center justify-center text-blue-500 group-hover:text-white transition-all duration-300">
                    <x-lucide-mail class="w-10 h-10 group-hover:scale-110 transition-transform" />
                </div>
            </div>

            <!-- Assigned Assets Stats -->
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-md border border-gray-100 p-1 flex items-stretch transition-all duration-300 transform hover:-translate-y-1 cursor-default group">
                <div class="flex-1 p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 group-hover:text-bps-orange transition-colors">Aset Dipertanggungjawabkan</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-bps-orange group-hover:scale-110 transition-transform origin-left">{{ $myAssetsCount }}</h3>
                        <span class="text-gray-500 font-semibold">Unit Perangkat</span>
                    </div>
                </div>
                <div class="bg-orange-50 group-hover:bg-bps-orange rounded-2xl m-2 px-6 flex items-center justify-center text-bps-orange group-hover:text-white transition-all duration-300">
                    <x-lucide-monitor class="w-10 h-10 group-hover:scale-110 transition-transform" />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col space-y-8 pb-8">
            <!-- Recent Tickets -->
            <div class="flex flex-col space-y-4">
                <div class="flex justify-between items-end px-2">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Aktivitas Laporan</h3>
                        <p class="text-sm text-gray-500">Update terbaru tiket bantuan Anda</p>
                    </div>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 focus:outline-none focus:underline flex items-center gap-1 group transition-colors">
                        Lihat Semua <x-lucide-chevron-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </div>
                
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex-1 group/table">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <tbody class="divide-y divide-gray-50">
                                @forelse($myTickets as $ticket)
                                <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" class="hover:bg-blue-50/50 transition duration-200 group/row cursor-pointer relative">
                                    <td class="px-6 py-5">
                                        <div class="flex items-start gap-4 z-10 relative">
                                            <div class="p-2.5 rounded-xl bg-gray-50 text-gray-400 group-hover/row:bg-blue-600 group-hover/row:text-white group-hover/row:shadow-lg group-hover/row:shadow-blue-600/20 transition-all duration-300 transform group-hover/row:scale-110 group-hover/row:rotate-3">
                                                <x-lucide-file-text class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 group-hover/row:text-blue-700 transition-colors">{{ \Illuminate\Support\Str::limit($ticket->title, 50) }}</div>
                                                <div class="text-xs font-medium text-gray-400 mt-1.5 flex items-center gap-4">
                                                    <span class="flex items-center gap-1"><x-lucide-calendar class="w-3.5 h-3.5" /> {{ $ticket->created_at->diffForHumans() }}</span>
                                                    <span class="flex items-center gap-1"><x-lucide-alert-circle class="w-3.5 h-3.5" /> {{ $ticket->priority }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right relative z-10 w-48">
                                        <div class="flex items-center justify-end gap-4">
                                            @php
                                                $statusColors = [
                                                    'Open' => 'bg-gray-100 text-gray-700 border-gray-200',
                                                    'In Progress' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                    'Menunggu Persetujuan Biaya' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                                    'Approved' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                                    'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'Dibatalkan' => 'bg-red-50 text-red-700 border-red-100',
                                                ];
                                                $badgeClass = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                            @endphp
                                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-bold border shadow-sm {{ $badgeClass }}">
                                                {{ $ticket->status }}
                                            </span>
                                            <x-lucide-chevron-right class="w-5 h-5 text-gray-300 group-hover/row:text-blue-500 group-hover/row:translate-x-1 transition-all" />
                                        </div>
                                    </td>
                                    <!-- Hover Highlight Overlay -->
                                    <div class="absolute inset-0 bg-blue-50/0 group-hover/row:bg-blue-50/50 pointer-events-none transition-colors duration-200"></div>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center max-w-[280px] mx-auto">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                                <x-lucide-inbox class="w-8 h-8 text-gray-300" />
                                            </div>
                                            <h4 class="text-gray-900 font-bold mb-1">Belum Ada Laporan</h4>
                                            <p class="text-sm text-gray-500 leading-relaxed">Saat ini Anda tidak memiliki tiket bantuan yang sedang aktif.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Assets -->
            <div class="flex flex-col space-y-4">
                <div class="flex justify-between items-end px-2">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Perangkat Saya</h3>
                        <p class="text-sm text-gray-500">Daftar inventaris di tangan Anda</p>
                    </div>
                    <a href="#" class="text-sm font-bold text-bps-orange hover:text-orange-700 flex items-center gap-1 group">
                        Detail Aset <x-lucide-chevron-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </div>
                
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex-1">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <tbody class="divide-y divide-gray-50">
                                @forelse($myAssets as $asset)
                                <tr class="hover:bg-orange-50/30 transition duration-150 group">
                                    <td class="px-6 py-5">
                                        <div class="flex items-start gap-4">
                                            <div class="p-2.5 rounded-xl bg-gray-50 text-gray-400 group-hover:bg-orange-100 group-hover:text-bps-orange transition-colors">
                                                <x-dynamic-component :component="'lucide-' . ($asset->deviceName?->type == 'Laptop' ? 'laptop' : 'monitor')" class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 group-hover:text-bps-orange transition-colors">
                                                    {{ $asset->deviceName->name ?? 'Unknown Device' }}
                                                </div>
                                                <div class="text-xs font-mono text-gray-500 mt-1.5 p-1 px-2 bg-gray-50 rounded inline-block border border-gray-100">
                                                    {{ $asset->bmn_number ?? $asset->serial_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-white text-gray-700 border border-gray-200">
                                            <span class="w-2 h-2 rounded-full mr-2 {{ $asset->status_kondisi == 'Baik' ? 'bg-emerald-400' : 'bg-yellow-400' }}"></span>
                                            {{ $asset->status_kondisi }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center max-w-[280px] mx-auto">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                                <x-lucide-shield-alert class="w-8 h-8 text-gray-300" />
                                            </div>
                                            <h4 class="text-gray-900 font-bold mb-1">Belum Ada Aset</h4>
                                            <p class="text-sm text-gray-500 leading-relaxed">Belum ada perangkat BMN yang tercatat atas nama Anda.</p>
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
    </div>
</x-app-layout>
