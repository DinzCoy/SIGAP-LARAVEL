<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengelola Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6">

        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl shadow-xl mb-8 p-8 flex flex-col md:flex-row items-center justify-between text-white">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-2">Manajemen Lokasi & Ruangan</h1>
                <p class="text-teal-100 max-w-2xl text-sm md:text-base leading-relaxed">
                    Pantau daftar ruangan dan distribusi aset pada setiap fasilitas BPS. Pastikan seluruh lokasi penempatan tercatat dengan akurat.
                </p>
            </div>
            <div class="mt-6 md:mt-0 flex shrink-0">
                <a href="{{ route('rooms.index') }}"
                    class="bg-white/10 hover:bg-white/20 border border-white/30 text-white font-semibold py-2.5 px-6 rounded-xl transition duration-200">
                    Kelola Ruangan
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Total Rooms -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Ruangan Terdaftar</p>
                            <p class="text-4xl font-extrabold text-teal-600">{{ $totalRooms }} <span class="text-lg font-medium text-gray-400">ruang</span></p>
                        </div>
                        <div class="bg-teal-50 text-teal-500 p-4 rounded-2xl">
                            <x-lucide-door-open class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-teal-400 to-emerald-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>

            <!-- Total Assets in Rooms -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Aset Terdistribusi</p>
                            <p class="text-4xl font-extrabold text-blue-500">{{ $totalAssets }} <span class="text-lg font-medium text-gray-400">unit</span></p>
                        </div>
                        <div class="bg-blue-50 text-blue-500 p-4 rounded-2xl">
                            <x-lucide-monitor-speaker class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-cyan-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>
        </div>

        <!-- Recent Assets Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <x-lucide-map-pin class="w-5 h-5 text-bps-orange" /> Distribusi Aset Terbaru
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Nama Perangkat</th>
                            <th class="px-6 py-4 font-semibold">No BMN / Serial</th>
                            <th class="px-6 py-4 font-semibold">Ruangan</th>
                            <th class="px-6 py-4 font-semibold text-right">Ditambahkan pada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentAssets as $asset)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $asset->deviceName->name ?? 'Unknown Device' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $asset->deviceName->brand ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-800 font-medium">{{ $asset->bmn_number ?? '-' }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $asset->serial_number ?? 'S/N Not Set' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($asset->room)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-teal-50 text-teal-800 border border-teal-100">
                                        <x-lucide-door-closed class="w-3 h-3 mr-1" /> {{ $asset->room->name }}
                                    </span>
                                @else
                                    <span class="text-red-500 text-xs font-semibold bg-red-50 px-2.5 py-1 rounded-md border border-red-100">Belum Dialokasikan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm text-gray-500">{{ $asset->created_at->format('d M Y') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-lucide-layout-dashboard class="w-12 h-12 text-gray-300 mb-3" />
                                    <p>Belum ada data distribusi aset.</p>
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
