
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('editModal').classList.add('hidden')"></div>

        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-100 animate-slide-up">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-gradient-to-r from-bps-blue to-blue-800 px-6 py-4 text-white flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold flex items-center gap-2" id="modal-title">
                        <i data-lucide="cpu" class="w-5 h-5"></i>
                        Edit Aset BMN
                    </h3>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-blue-100 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">

                    <div>
                        <label for="edit_bmn_number" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                            <span>Nomor BMN</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="hash" class="w-4 h-4"></i>
                            </div>
                            <input type="text" name="bmn_number" id="edit_bmn_number" required
                                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm"
                                placeholder="Masukkan nomor BMN...">
                        </div>
                    </div>

                    <div>
                        <label for="edit_serial_number" class="block text-sm font-semibold text-gray-700 mb-1">Serial Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="barcode" class="w-4 h-4"></i>
                            </div>
                            <input type="text" name="serial_number" id="edit_serial_number"
                                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm"
                                placeholder="Masukkan serial number (S/N)...">
                        </div>
                    </div>

                    <div>
                        <label for="edit_device_name_id" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                            <span>Master Perangkat (Brand/Model)</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="device_name_id" id="hidden_edit_device_name_id">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="monitor" class="w-4 h-4"></i>
                            </div>
                            <select id="edit_device_name_id" disabled
                                class="w-full pl-9 pr-10 py-2 border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed rounded-lg text-sm appearance-none">
                                <option value="">-- Pilih Master Perangkat --</option>
                                @foreach($allDeviceNames as $dn)
                                    <option value="{{ $dn->id }}">{{ $dn->brand }} - {{ $dn->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-300">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="edit_room_id" class="block text-sm font-semibold text-gray-700 mb-1">Ruangan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="home" class="w-4 h-4"></i>
                            </div>
                            <select name="room_id" id="edit_room_id"
                                class="w-full pl-9 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm appearance-none bg-white">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="edit_user_id" class="block text-sm font-semibold text-gray-700 mb-1">Dialokasikan ke Pengguna</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <select name="user_id" id="edit_user_id" onchange="handleUserAllocationChange(this)"
                                class="w-full pl-9 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm appearance-none bg-white">
                                <option value="">-- Belum Dialokasikan --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="edit_allocated_at" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Alokasi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </div>
                            <input type="date" name="allocated_at" id="edit_allocated_at"
                                onclick="this.showPicker()"
                                class="w-full pl-9 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm cursor-pointer bg-white">
                        </div>
                        <p class="mt-1 text-[10px] text-gray-400">Tanggal kapan perangkat dialokasikan ke pengguna.</p>
                    </div>

                    <div>
                        <label for="edit_status_kondisi" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                            <span>Status Kondisi Fisik</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="activity" class="w-4 h-4"></i>
                            </div>
                            <select name="status_kondisi" id="edit_status_kondisi"
                                class="w-full pl-9 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm appearance-none bg-white">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-bps-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all duration-200 flex items-center gap-2 bg-white">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
