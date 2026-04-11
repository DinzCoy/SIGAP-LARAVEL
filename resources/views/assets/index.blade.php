<x-app-layout>
    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                
                {{-- Header Section --}}
                <div class="bg-gradient-to-r from-bps-blue to-blue-700 px-6 py-6 sm:px-8 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold leading-tight flex items-center gap-2">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                {{ $currentDeviceNameId ? 'Daftar Aset (Difilter)' : 'Daftar Seluruh Aset BMN' }}
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">Kelola data inventaris PC, laptop, dan perangkat jaringan (Role: Pengelola Barang)</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('device-names.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-bps-blue font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Kembali ke Master Perangkat
                            </a>
                        </div>
                    </div>
                </div>



                {{-- Table Section --}}
                <div class="p-6">
                    {{-- Filter & Pagination Control --}}
                    <form id="filterForm" method="GET" action="{{ route('assets.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-100">
                        @if(request('device_name_id'))
                            <input type="hidden" name="device_name_id" value="{{ request('device_name_id') }}">
                        @endif
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Pencarian</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari BMN, SN, atau Merek..." class="block w-full py-2 px-3 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status PC Guardian</label>
                            <select name="filter_linked" class="block w-full py-2 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                                <option value="all" {{ request('filter_linked') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="yes" {{ request('filter_linked') == 'yes' ? 'selected' : '' }}>Sudah Terhubung</option>
                                <option value="no" {{ request('filter_linked') == 'no' ? 'selected' : '' }}>Belum Terhubung</option>
                            </select>
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status BMN</label>
                            <select name="filter_bmn" class="block w-full py-2 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                                <option value="all" {{ request('filter_bmn') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="yes" {{ request('filter_bmn') == 'yes' ? 'selected' : '' }}>Memiliki BMN</option>
                                <option value="no" {{ request('filter_bmn') == 'no' ? 'selected' : '' }}>Tanpa BMN</option>
                            </select>
                        </div>

                        <div class="w-full md:w-auto">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-bps-blue text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-blue-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Terapkan Filter
                            </button>
                        </div>
                    </form>

                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-gray-500">
                            Menampilkan 
                            <span class="font-bold text-gray-700">{{ $assets->firstItem() ?? 0 }}</span>
                            –
                            <span class="font-bold text-gray-700">{{ $assets->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-bold text-bps-blue">{{ $assets->total() }}</span> total aset
                        </p>
                    </div>

                    <div class="border rounded-xl overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. BMN</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Serial Number</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruangan</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kondisi</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Linking (PC)</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($assets as $asset)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $asset->bmn_number }}</div>
                                            <div class="text-xs text-blue-600 mt-1">{{ $asset->deviceName ? $asset->deviceName->brand . ' ' . $asset->deviceName->name : $asset->brand }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ $asset->serial_number ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ $asset->room->name ?? '-' }}</div>
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
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Baik</span>
                                            @elseif($asset->status_kondisi == 'Rusak Ringan')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rusak Berat</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($asset->pcReport)
                                                <div class="inline-flex items-center text-sm font-medium text-blue-600">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    {{ $asset->pcReport->hostname }}
                                                </div>
                                                <div class="text-xs text-gray-400 mt-1">{{ $asset->mac_address }}</div>
                                            @else
                                                <button onclick="openLinkModal({{ $asset->id }}, '{{ $asset->bmn_number }}')" class="inline-flex items-center px-2.5 py-1.5 border border-dashed border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                                    Link Device
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="openEditModal({{ $asset->toJson() }})" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <form id="delete-form-{{ $asset->id }}" action="{{ route('asset.destroy', $asset->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-form-{{ $asset->id }}', 'Apakah Anda yakin ingin menghapus aset BMN {{ $asset->bmn_number }}?')" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p class="mt-4 text-sm text-gray-500">Belum ada data Aset BMN yang terdaftar.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination & Per Page Control --}}
                    <div class="mt-6 mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-lg border border-gray-100">
                            <label for="per_page_bottom" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tampilkan:</label>
                            <select name="per_page" id="per_page_bottom" form="filterForm" onchange="this.form.submit()" class="block py-1.5 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm bg-white">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 baris</option>
                                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 baris</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
                                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                            </select>
                        </div>

                        @if($assets->hasPages())
                            <div class="w-full md:w-auto">
                                {{ $assets->appends(request()->query())->links('vendor.pagination.modern') }}
                            </div>
                        @endif
                    </div>
                </div>
                </div>
    </div>


    {{-- Modal Link Device --}}
    <div id="linkModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('linkModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="linkForm" action="#" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-bps-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    Tautkan Device ke BMN
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Pilih data Agent PC yang terdeteksi untuk ditautkan ke BMN <span id="linkBmnCode" class="font-bold text-bps-blue"></span></p>
                                
                                <div class="mt-5">
                                    <label for="mac_address" class="block text-sm font-medium text-gray-700 mb-1">Pilih Device Active (Hostname / MAC) <span class="text-red-500">*</span></label>
                                    @if($unlinkedPcs->count() > 0)
                                        <select name="mac_address" id="mac_address" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                            <option value="">-- Pilih Device Unlinked --</option>
                                            @foreach($unlinkedPcs as $pc)
                                                <option value="{{ $pc->mac_address }}">
                                                    {{ $pc->hostname }} (IP: {{ $pc->ip_address }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="p-3 bg-yellow-50 text-yellow-800 text-sm rounded border border-yellow-200">
                                            Semua device yang terdeteksi telah memiliki BMN atau tidak ada agent PC yang berhasil terhubung ke server.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        @if($unlinkedPcs->count() > 0)
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-bps-orange text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-orange sm:ml-3 sm:w-auto sm:text-sm">
                                Tautkan Sekarang
                            </button>
                        @endif
                        <button type="button" onclick="document.getElementById('linkModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLinkModal(assetId, bmnNumber) {
            document.getElementById('linkBmnCode').innerText = bmnNumber;
            // Set action route dynamically
            const form = document.getElementById('linkForm');
            form.action = `/asset-manager/${assetId}/link`;
            document.getElementById('linkModal').classList.remove('hidden');
        }

        function openEditModal(asset) {
            document.getElementById('edit_bmn_number').value = asset.bmn_number;
            document.getElementById('edit_device_name_id').value = asset.device_name_id;
            document.getElementById('hidden_edit_device_name_id').value = asset.device_name_id;
            document.getElementById('edit_serial_number').value = asset.serial_number || '';
            document.getElementById('edit_room_id').value = asset.room_id || '';
            document.getElementById('edit_user_id').value = asset.user_id || '';
            document.getElementById('edit_allocated_at').value = asset.allocated_at ? asset.allocated_at.substring(0, 10) : '';
            document.getElementById('edit_status_kondisi').value = asset.status_kondisi;
            
            const form = document.getElementById('editForm');
            form.action = `/asset-manager/${asset.id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>

    {{-- Modal Edit Aset --}}
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Aset BMN</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="edit_bmn_number" class="block text-sm font-medium text-gray-700">Nomor BMN <span class="text-red-500">*</span></label>
                                        <input type="text" name="bmn_number" id="edit_bmn_number" required class="mt-1 focus:ring-bps-blue focus:border-bps-blue block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label for="edit_serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                                        <input type="text" name="serial_number" id="edit_serial_number" class="mt-1 focus:ring-bps-blue focus:border-bps-blue block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label for="edit_device_name_id" class="block text-sm font-medium text-gray-700">Master Perangkat (Brand/Model) <span class="text-red-500">*</span></label>
                                        <input type="hidden" name="device_name_id" id="hidden_edit_device_name_id">
                                        <select id="edit_device_name_id" disabled class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed rounded-md shadow-sm sm:text-sm">
                                            <option value="">-- Pilih Master Perangkat --</option>
                                            @foreach($allDeviceNames as $dn)
                                                <option value="{{ $dn->id }}">{{ $dn->brand }} - {{ $dn->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="edit_room_id" class="block text-sm font-medium text-gray-700">Ruangan</label>
                                        <select name="room_id" id="edit_room_id" class="mt-1 block w-full py-2 pl-3 pr-10 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                            <option value="">-- Pilih Ruangan --</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="edit_user_id" class="block text-sm font-medium text-gray-700">Dialokasikan ke Pengguna</label>
                                        <select name="user_id" id="edit_user_id" class="mt-1 block w-full py-2 pl-3 pr-10 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                            <option value="">-- Belum Dialokasikan --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="edit_allocated_at" class="block text-sm font-medium text-gray-700">Tanggal Alokasi</label>
                                        <input type="date" name="allocated_at" id="edit_allocated_at" class="mt-1 focus:ring-bps-blue focus:border-bps-blue block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <p class="mt-1 text-xs text-gray-500">Tanggal kapan perangkat dialokasikan ke pengguna.</p>
                                    </div>
                                    <div>
                                        <label for="edit_status_kondisi" class="block text-sm font-medium text-gray-700">Status Kondisi Fisik <span class="text-red-500">*</span></label>
                                        <select name="status_kondisi" id="edit_status_kondisi" class="mt-1 block w-full py-2 pl-3 pr-10 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
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
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-bps-blue text-base font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
