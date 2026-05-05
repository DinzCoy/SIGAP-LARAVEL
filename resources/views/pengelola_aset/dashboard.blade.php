<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengelola Aset') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6">

        <!-- Hero Header -->
        <div
            class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-blue-800 to-indigo-900 rounded-3xl shadow-2xl mb-10 p-8 md:p-10 flex flex-col md:flex-row items-center justify-between text-white border border-white/10 group">
            <!-- Decorative floating background elements -->
            <div
                class="absolute -top-24 -right-24 w-72 h-72 bg-blue-400 rounded-full mix-blend-overlay filter blur-3xl opacity-40 group-hover:scale-125 transition-transform duration-1000">
            </div>
            <div
                class="absolute -bottom-24 -left-24 w-72 h-72 bg-indigo-500 rounded-full mix-blend-overlay filter blur-3xl opacity-40 group-hover:scale-125 transition-transform duration-1000">
            </div>

            <div class="relative z-10 w-full md:w-2/3">
                <span
                    class="inline-block py-1.5 px-4 rounded-full bg-white/10 border border-white/20 text-xs font-bold tracking-widest uppercase mb-4 hover:bg-white/20 transition-colors backdrop-blur-md">Command
                    Center</span>
                <h1
                    class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4 leading-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-indigo-100">
                    Manajemen Aset</h1>
                <p class="text-indigo-100/90 max-w-2xl text-sm md:text-base leading-relaxed font-medium">
                    Pantau kondisi seluruh perangkat operasional secara realtime, kelola data barang milik negara (BMN),
                    dan tinjau laporan kerusakan aset sebelum ditangani oleh teknisi.
                </p>
            </div>
            <div class="relative z-10 mt-8 md:mt-0 flex shrink-0 flex-wrap gap-3">
                <a href="{{ route('master-aset.index') }}"
                    class="bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md text-white font-semibold py-3 px-6 rounded-2xl transition-all duration-300 flex items-center gap-2 hover:shadow-lg hover:shadow-indigo-500/30 active:scale-95">
                    <x-lucide-plus-circle class="w-5 h-5" /> Tambah Aset
                </a>
                <a href="{{ route('assets.index') }}"
                    class="bg-white/95 text-indigo-800 hover:bg-white shadow-lg hover:shadow-xl font-bold py-3 px-7 rounded-2xl transition-all duration-300 active:scale-95">
                    Kelola Aset
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Total Assets -->
            <div
                class="bg-white rounded-3xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden relative group transition-all duration-300 hover:border-indigo-100">
                <div class="p-8 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total Seluruh Aset
                            </p>
                            <p class="text-5xl font-black text-indigo-700 tracking-tight">{{ $totalAssets }} <span
                                    class="text-lg font-bold text-gray-300 ml-1">unit</span></p>
                        </div>
                        <div
                            class="bg-indigo-50 text-indigo-600 p-4 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-indigo-200">
                            <x-lucide-package class="w-9 h-9" />
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out">
                </div>
            </div>

            <!-- Broken Assets -->
            <div
                class="bg-white rounded-3xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden relative group transition-all duration-300 hover:border-orange-100">
                <div class="p-8 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Aset Bermasalah
                            </p>
                            <p class="text-5xl font-black text-orange-500 tracking-tight">{{ $brokenAssets }} <span
                                    class="text-lg font-bold text-gray-300 ml-1">unit</span></p>
                        </div>
                        <div
                            class="bg-orange-50 text-orange-500 p-4 rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-orange-200">
                            <x-lucide-alert-triangle class="w-9 h-9" />
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-red-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out">
                </div>
            </div>

            <!-- Tiket Butuh Pengecekan -->
            <a href="{{ route('tickets.index') }}"
                class="bg-white rounded-3xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden relative group block cursor-pointer transition-all duration-300 hover:border-rose-100">
                <div class="p-8 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tiket Menunggu</p>
                            <p class="text-5xl font-black text-rose-500 tracking-tight">{{ $pendingTickets }} <span
                                    class="text-lg font-bold text-gray-300 ml-1">laporan</span></p>
                        </div>
                        <div
                            class="bg-rose-50 text-rose-600 p-4 rounded-2xl group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 shadow-sm group-hover:shadow-rose-200">
                            <x-lucide-clipboard-check class="w-9 h-9 relative z-10" />
                            @if($pendingTickets > 0)
                                <span class="absolute top-3 right-3 flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white"></span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-rose-400 to-pink-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out">
                </div>
            </a>
        </div>

        <!-- Recent Assets Table -->
        <div
            class="bg-white overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 rounded-3xl border border-gray-100 mb-8">
            <div
                class="p-8 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-1">
                        <x-lucide-clock class="w-6 h-6 text-indigo-500" /> Aset Baru Ditambahkan
                    </h3>
                    <p class="text-sm text-gray-500 ml-8">Daftar perangkat keras terbaru yang dimasukkan ke dalam
                        inventaris</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="whitespace-nowrap">
                        <tr
                            class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-400 font-bold uppercase tracking-widest">
                            <th class="px-8 py-5 whitespace-nowrap">Nama Perangkat & Merek</th>
                            <th class="px-8 py-5 whitespace-nowrap">Tahun - BMN / Serial</th>
                            <th class="px-8 py-5 whitespace-nowrap">Lokasi / Pengguna</th>
                            <th class="px-8 py-5 whitespace-nowrap">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentAssets as $asset)
                            <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                                        {{ $asset->deviceName?->name ?? 'Unknown Device' }}</div>
                                    <div class="text-xs text-gray-500 mt-1 font-medium">
                                        {{ Str::limit($asset->deviceName?->brand ?? '-', 20) }}</div>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="text-sm text-gray-700 font-semibold">Tahun
                                        {{ $asset->acquisition_year ?? '-' }}</div>
                                    <div
                                        class="text-xs text-gray-500 font-mono mt-1 bg-gray-100 px-2 py-0.5 rounded inline-block">
                                        {{ $asset->bmn_number ?? 'Non-BMN' }}
                                    </div>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    @if($asset->room)
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100/50 shadow-sm">
                                            <x-lucide-door-closed class="w-3.5 h-3.5 mr-1.5" /> {{ $asset->room?->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs font-semibold italic">Belum Dialokasikan</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    @if($asset->status_kondisi == 'Baik')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-full shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                            Baik
                                        </span>
                                    @elseif($asset->status_kondisi == 'Rusak Ringan')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60 rounded-full shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Rusak Ringan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-bold bg-red-50 text-red-700 border border-red-200/60 rounded-full shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Rusak Berat
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-sm text-gray-500 whitespace-nowrap">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 p-4 rounded-full mb-3">
                                            <x-lucide-package-search class="w-10 h-10 text-gray-300" />
                                        </div>
                                        <p class="font-medium text-gray-600">Belum ada data aset terdaftar.</p>
                                        <p class="text-xs text-gray-400 mt-1">Tambahkan aset baru melalui menu Master Aset.
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
</x-app-layout>
