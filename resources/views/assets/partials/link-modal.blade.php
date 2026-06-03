
<div id="linkModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('linkModal').classList.add('hidden')"></div>

        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-100 animate-slide-up">
            <form id="linkForm" action="#" method="POST">
                @csrf

                <div class="bg-gradient-to-r from-bps-blue to-blue-800 px-6 py-4 text-white flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-bold flex items-center gap-2" id="modal-title">
                        <i data-lucide="link" class="w-5 h-5"></i>
                        Tautkan Device ke BMN
                    </h3>
                    <button type="button" onclick="document.getElementById('linkModal').classList.add('hidden')" class="text-blue-100 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-gray-600">
                        Pilih data Agent PC yang terdeteksi untuk ditautkan ke nomor BMN <span id="linkBmnCode" class="font-bold text-bps-blue"></span>.
                    </p>

                    <div>
                        <label for="mac_address" class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                            <span>Pilih Device Active (Hostname / MAC)</span>
                            <span class="text-red-500">*</span>
                        </label>

                        @if($unlinkedPcs->count() > 0)
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="monitor" class="w-4 h-4"></i>
                                </div>
                                <select name="mac_address" id="mac_address" required
                                    class="w-full pl-9 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm appearance-none bg-white">
                                    <option value="">-- Pilih Device Unlinked --</option>
                                    @foreach($unlinkedPcs as $pc)
                                        <option value="{{ $pc->mac_address }}">
                                            {{ $pc->hostname }} (IP: {{ $pc->ip_address }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-amber-50 text-amber-800 text-sm rounded-lg border border-amber-200 flex items-start gap-2.5">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                                <span>Semua device yang terdeteksi telah memiliki BMN atau tidak ada agent PC yang berhasil terhubung ke server.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    @if($unlinkedPcs->count() > 0)
                        <button type="submit"
                            class="px-5 py-2 bg-gradient-to-r from-bps-orange to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-bps-orange focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                            <i data-lucide="link-2" class="w-4 h-4"></i>
                            Tautkan Sekarang
                        </button>
                    @endif
                    <button type="button" onclick="document.getElementById('linkModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all duration-200 flex items-center gap-2 bg-white">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
