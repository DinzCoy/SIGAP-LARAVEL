{{-- Modal Edit Aset --}}
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
        
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-100 animate-slide-up">
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
                                    <select name="user_id" id="edit_user_id" onchange="handleUserAllocationChange(this)" class="mt-1 block w-full py-2 pl-3 pr-10 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                        <option value="">-- Belum Dialokasikan --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_allocated_at" class="block text-sm font-medium text-gray-700">Tanggal Alokasi</label>
                                    <div class="relative mt-1">
                                        <input type="date" name="allocated_at" id="edit_allocated_at" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm rounded-md shadow-sm bg-white" onclick="this.showPicker()">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
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
                    <button type="submit" class="modal-btn-primary">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="modal-btn-secondary">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
