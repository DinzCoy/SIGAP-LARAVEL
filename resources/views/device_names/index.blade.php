<x-app-layout>
    <div class="space-y-6">
            
            {{-- Header Section --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 mb-8">
                <div class="bg-gradient-to-r from-bps-blue to-blue-700 px-6 py-6 sm:px-8 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold leading-tight flex items-center gap-2">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                Master Nama Perangkat
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">Kelola data merek dan tipe nama perangkat</p>
                        </div>
                        <a href="{{ route('device-names.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-bps-blue font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Perangkat
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($device_names as $device)
                    @php
                        $initials = substr(strtoupper($device->brand), 0, 1) . substr(strtoupper($device->name), 0, 1);
                        if(strlen($initials) == 0) $initials = "PC";
                        $hash = array_sum(array_map('ord', str_split($initials)));
                        $colors = [
                            ['bg-blue-400', 'text-white'],
                            ['bg-purple-500', 'text-white'],
                            ['bg-indigo-500', 'text-white'],
                            ['bg-teal-500', 'text-white'],
                            ['bg-blue-600', 'text-white'],
                            ['bg-cyan-500', 'text-white'],
                        ];
                        $color = $colors[$hash % count($colors)];
                    @endphp
                    
                    <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col overflow-hidden relative hover:-translate-y-1">
                        {{-- Image / Initials Area --}}
                        <div class="h-48 bg-white flex items-center justify-center p-4 border-b border-gray-100 relative overflow-hidden group-hover:bg-gray-50 transition-colors">
                            @if($device->image)
                                <img src="{{ Storage::url($device->image) }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-500" alt="{{ $device->brand }}">
                            @else
                                <div class="w-20 h-20 flex items-center justify-center rounded-2xl {{ $color[0] }} {{ $color[1] }} shadow group-hover:scale-110 transition-transform duration-300">
                                    <span class="text-3xl font-bold">{{ $initials }}</span>
                                </div>
                            @endif

                            {{-- Quantity Badge --}}
                            <div class="absolute top-4 right-4 bg-bps-blue text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm z-10 backdrop-blur-sm bg-opacity-90">
                                {{ $device->quantity }} Unit
                            </div>
                        </div>

                        {{-- Details Area --}}
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-bps-blue transition-colors line-clamp-1 truncate" title="{{ $device->brand }} - {{ $device->name }}">{{ $device->brand }} {{ $device->name }}</h3>
                            
                            <div class="mt-2 text-sm text-gray-600 border-b border-gray-50 pb-2">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold">{{ $device->type ?: 'Tipe Tidak Disebutkan' }}</span>
                            </div>

                            @if($device->procurement_date)
                                <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Pengadaan: <span class="font-semibold text-gray-700">{{ $device->procurement_date->translatedFormat('d F Y') }}</span></span>
                                </div>
                            @endif

                            <div class="mt-3 text-sm text-gray-500 flex-1 line-clamp-2">
                                {{ $device->description ?: 'Tidak ada deskripsi' }}
                            </div>

                            {{-- Actions (CRUD + Link) --}}
                            <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4 relative z-20">
                                <a href="{{ route('device-names.show', $device->id) }}" class="text-bps-orange text-sm font-semibold hover:underline flex items-center">
                                    Lihat Rincian Analitik
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('device-names.edit', $device->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border-transparent" title="Edit Perangkat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('device-names.destroy', $device->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus master nama perangkat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Master">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($device_names->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center mt-6">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada data Master Nama Perangkat</h3>
                    <p class="mt-1 text-gray-500">Silakan tambahkan merk/tipe untuk menginventarisir hardware Anda.</p>
                </div>
            @endif
    </div>
</x-app-layout>
