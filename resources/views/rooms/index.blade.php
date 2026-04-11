<x-app-layout>
    <div class="space-y-6">
            
            {{-- Header Section --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 mb-8">
                <div class="bg-gradient-to-r from-bps-blue to-blue-700 px-6 py-6 sm:px-8 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold leading-tight flex items-center gap-2">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Master Ruangan
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">Kelola data ruangan untuk penempatan aset BMN</p>
                        </div>
                        <a href="{{ route('rooms.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-bps-blue font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Ruangan
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            @endif

            {{-- Table Section --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruangan</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PIC / Penanggung Jawab</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Unit</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kondisi Aset</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($rooms as $room)
                                <tr class="hover:bg-blue-50/30 transition-colors group cursor-pointer" onclick="if(!event.target.closest('button') && !event.target.closest('a') && !event.target.closest('form')) window.location.href='{{ route('assets.index', ['room' => $room->slug]) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-blue-100 text-bps-blue rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 group-hover:text-bps-blue transition-colors">{{ $room->name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($room->description, 40) ?: 'Tidak ada deskripsi' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($room->pic)
                                            <div class="flex items-center gap-2">
                                                <div class="bg-bps-orange/10 p-1.5 rounded-full">
                                                    <svg class="w-4 h-4 text-bps-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700">{{ $room->pic->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">PIC Belum Diatur</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-bps-blue border border-blue-100">
                                            {{ $room->assets_count }} Unit
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5" title="Baik">
                                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                    <span class="text-xs font-bold text-gray-700">{{ $room->baik_count }}</span>
                                                    <span class="text-[10px] text-gray-400 uppercase tracking-tight">Baik</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5" title="Rusak Ringan">
                                                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                                    <span class="text-xs font-bold text-gray-700">{{ $room->rusak_ringan_count }}</span>
                                                    <span class="text-[10px] text-gray-400 uppercase tracking-tight">R. Ringan</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5" title="Rusak Berat">
                                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                    <span class="text-xs font-bold text-gray-700">{{ $room->rusak_berat_count }}</span>
                                                    <span class="text-[10px] text-gray-400 uppercase tracking-tight">R. Berat</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('rooms.edit', $room->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors" title="Edit Ruangan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form id="delete-form-{{ $room->id }}" action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('delete-form-{{ $room->id }}', 'Hapus ruangan {{ $room->name }}? Pastikan ruangan sudah kosong.')" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Hapus Ruangan">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <h3 class="text-lg font-medium text-gray-900">Belum ada data Ruangan</h3>
                                            <p class="text-gray-500 text-sm">Silakan tambahkan ruangan baru melalui tombol di atas.</p>
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
