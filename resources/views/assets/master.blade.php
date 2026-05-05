<x-app-layout>
    <div class="space-y-6">
            
            {{-- Header Section --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 mb-8">
                <div class="bg-gradient-to-r from-bps-blue to-blue-700 px-6 py-6 sm:px-8 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold leading-tight flex items-center gap-2">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Master Aset
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">Katalog aset BMN berdasarkan Brand/Jenis Device</p>
                        </div>
                        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-white text-bps-blue font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Aset BMN
                        </button>
                    </div>
                </div>
            </div>

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($brands as $b)
                    @php
                        // Check if an image exists for this brand, else fallback to default
                        $brandImage = strtolower($b->brand) . '.png';
                        $imagePath = public_path('images/devices/' . $brandImage);
                        if (!file_exists($imagePath)) {
                            $brandImage = 'default.png';
                        }
                    @endphp
                    
                    <a href="{{ route('assets.index', ['brand' => $b->brand]) }}" class="group block bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden hover:-translate-y-1">
                        {{-- Image Area --}}
                        <div class="h-48 bg-gray-50 flex items-center justify-center p-6 border-b border-gray-100 relative overflow-hidden group-hover:bg-blue-50/50 transition-colors">
                            <img src="{{ asset('images/devices/' . $brandImage) }}" alt="{{ $b->brand }} Logo" class="w-24 h-24 object-contain group-hover:scale-110 transition-transform duration-300">
                            
                            {{-- Quick total badge --}}
                            <div class="absolute top-4 right-4 bg-bps-blue text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                {{ $b->total }} Unit
                            </div>
                        </div>

                        {{-- Details Area --}}
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-bps-blue transition-colors">{{ $b->brand }}</h3>
                            
                            <div class="mt-4 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-green-500"></div> Baik</span>
                                    <span class="font-semibold text-gray-700">{{ $b->baik_count }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-yellow-500"></div> Rusak Ringan</span>
                                    <span class="font-semibold text-gray-700">{{ $b->rusak_ringan_count }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-red-500"></div> Rusak Berat</span>
                                    <span class="font-semibold text-gray-700">{{ $b->rusak_berat_count }}</span>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center text-bps-orange text-sm font-semibold group-hover:underline">
                                Lihat Detail Kumpulan Aset
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($brands->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada data Brand Aset</h3>
                    <p class="mt-1 text-gray-500">Silakan tambahkan aset baru melalui menu Daftar Aset untuk mulai mengelola.</p>
                </div>
            @endif
        </div>

        {{-- Modal Tambah Aset --}}
    <div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('asset.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Registrasi Aset Baru</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="bmn_number" class="block text-sm font-medium text-gray-700">Nomor BMN <span class="text-red-500">*</span></label>
                                        <input type="text" name="bmn_number" id="bmn_number" required class="mt-1 focus:ring-bps-blue focus:border-bps-blue block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                                        <input type="text" name="serial_number" id="serial_number" class="mt-1 focus:ring-bps-blue focus:border-bps-blue block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand / Jenis Device <span class="text-red-500">*</span></label>
                                        <input type="text" list="brands_list" name="brand" id="brand" required placeholder="Pilih dari daftar atau ketik baru..." autocomplete="off" class="mt-1 focus:ring-bps-blue focus:border-bps-blue block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <p class="mt-1 text-xs text-gray-400">Pilih dari dropdown atau ketik nama brand untuk menambah baru.</p>
                                    </div>
                                    <div>
                                        <label for="room_id" class="block text-sm font-medium text-gray-700">Ruangan</label>
                                        <select name="room_id" id="room_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                            <option value="">-- Pilih Ruangan --</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="status_kondisi" class="block text-sm font-medium text-gray-700">Status Kondisi Fisik <span class="text-red-500">*</span></label>
                                        <select name="status_kondisi" id="status_kondisi" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="modal-btn-primary">
                            Simpan Aset
                        </button>
                        <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="modal-btn-secondary">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Datalist untuk dropdown Brand --}}
    <datalist id="brands_list">
        <option value="Acer">
        <option value="Apple">
        <option value="Asus">
        <option value="Axioo">
        <option value="Dell">
        <option value="HP">
        <option value="Lenovo">
        <option value="Samsung">
        <option value="Think Vision">
        <option value="MikroTik">
    </datalist>

</x-app-layout>
