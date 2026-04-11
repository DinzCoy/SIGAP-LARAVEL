<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 mb-8">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-5">
                        <div class="flex items-center gap-4">
                            @if($deviceName->image)
                                <img src="{{ Storage::url($deviceName->image) }}" alt="{{ $deviceName->brand }}" class="h-16 w-16 object-cover rounded-lg border border-gray-200 shadow-sm bg-white p-1">
                            @endif
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                    <a href="{{ route('device-names.index') }}" class="text-gray-400 hover:text-bps-blue transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    </a>
                                    Rincian Merek: {{ $deviceName->brand }} {{ $deviceName->name }}
                                </h2>
                                <div class="mt-1 flex items-center gap-2 flex-wrap">
                                    <span class="bg-blue-100 text-bps-blue px-3 py-1 rounded-full text-sm font-bold border border-blue-200">
                                        {{ $deviceName->type ?: 'Tipe Umum' }}
                                    </span>
                                    @if($deviceName->procurement_date)
                                        <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-sm font-semibold border border-amber-200 inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Pengadaan: {{ $deviceName->procurement_date->translatedFormat('d F Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                {{-- Statistik Inventaris --}}
                <div class="lg:col-span-1 border border-gray-100 bg-white rounded-2xl shadow-sm p-6 overflow-hidden relative">
                    <div class="absolute -right-6 -top-6 text-gray-50 opacity-50">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-gray-500 font-bold mb-4 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg> Status Gudang & Persebaran</h3>
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-end border-b border-gray-100 pb-2">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Pengadaan</p>
                                <p class="text-2xl font-black text-gray-800">{{ $deviceName->quantity }} <span class="text-sm font-normal text-gray-500">Unit</span></p>
                            </div>
                            <div class="p-2 bg-blue-50 text-blue-500 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg></div>
                        </div>
                        <div class="flex justify-between items-end border-b border-gray-100 pb-2">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Terdistribusi (Masuk Aset)</p>
                                <p class="text-2xl font-black text-green-600">{{ $deviceName->registered_count }} <span class="text-sm font-normal text-gray-500">Unit</span></p>
                            </div>
                            <div class="p-2 bg-green-50 text-green-500 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                        </div>
                        @php $sisaBarang = $deviceName->quantity - $deviceName->registered_count; @endphp
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Sisa Gudang (Belum Terdaftar)</p>
                                <p class="text-2xl font-black {{ $sisaBarang < 0 ? 'text-red-500' : 'text-bps-orange' }}">{{ $sisaBarang }} <span class="text-sm font-normal text-gray-500">Unit</span></p>
                            </div>
                            <div class="p-2 bg-orange-50 text-bps-orange rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg></div>
                        </div>
                        @if($sisaBarang < 0)
                            <div class="text-xs text-red-500 mt-2 p-2 bg-red-50 rounded italic">Peringatan: Jumlah aset terdaftar melebihi kuota master ({{ $deviceName->quantity }} unit). Harap update kuota master jika ada penambahan!</div>
                        @endif
                    </div>
                </div>

                {{-- Statistik Kondisi Fisik --}}
                <div class="lg:col-span-2 border border-gray-100 bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-gray-500 font-bold mb-6 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Kondisi Kesehatan Perangkat</h3>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center border-b-4 border-green-500 shadow-sm">
                            <div class="w-12 h-12 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500 uppercase">Kondisi Baik</p>
                            <p class="text-3xl font-black text-gray-800 mt-1">{{ $deviceName->baik_count }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 text-center border-b-4 border-yellow-400 shadow-sm">
                            <div class="w-12 h-12 mx-auto bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500 uppercase">Rusak Ringan</p>
                            <p class="text-3xl font-black text-gray-800 mt-1">{{ $deviceName->rusak_ringan_count }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 text-center border-b-4 border-red-500 shadow-sm">
                            <div class="w-12 h-12 mx-auto bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500 uppercase">Rusak Berat</p>
                            <p class="text-3xl font-black text-gray-800 mt-1">{{ $deviceName->rusak_berat_count }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Asset Linked --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-bps-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
                        Daftar Individu Aset "{{ $deviceName->name }}"
                    </h3>
                    <a href="{{ route('assets.index') }}" class="text-sm text-bps-blue hover:underline font-semibold">Tuju Master Aset Fisik &rarr;</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. BMN</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">MAC Address</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ruangan / Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $asset->bmn_number ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">{{ $asset->serial_number ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono text-xs">{{ $asset->mac_address ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asset->room)
                                            <div class="text-sm text-gray-900 font-medium">{{ $asset->room->name }}</div>
                                            <div class="text-xs text-gray-500 border-t border-gray-100 mt-1 pt-1">{{ $asset->room->pic ? $asset->room->pic->name : 'Tanpa PIC' }}</div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Belum Dialokasikan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asset->user)
                                            <div class="text-sm font-medium text-gray-900">{{ $asset->user->name }}</div>
                                            @if($asset->allocated_at)
                                                <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    {{ $asset->allocated_at->translatedFormat('d M Y') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400 italic">Belum dialokasikan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($asset->status_kondisi == 'Baik')
                                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold leading-5 text-green-800 border border-green-200">Baik</span>
                                        @elseif($asset->status_kondisi == 'Rusak Ringan')
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold leading-5 text-yellow-800 border border-yellow-200">Rusak Ringan</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold leading-5 text-red-800 border border-red-200">Rusak Berat</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        Belum ada aset fisik yang diregistrasikan/di-link dengan merek ini.
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
