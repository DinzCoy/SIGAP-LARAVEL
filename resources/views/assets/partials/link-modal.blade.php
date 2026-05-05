{{-- Modal Link Device --}}
<div id="linkModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('linkModal').classList.add('hidden')"></div>
        
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-100 animate-slide-up">
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
                        <button type="submit" class="modal-btn-warning">
                            Tautkan Sekarang
                        </button>
                    @endif
                    <button type="button" onclick="document.getElementById('linkModal').classList.add('hidden')" class="modal-btn-secondary">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
