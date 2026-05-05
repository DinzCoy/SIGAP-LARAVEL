<x-app-layout>
    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">

            {{-- Header Section (PROVEN PATTERN from index.blade.php) --}}
            <div class="bg-gradient-to-r from-bps-blue to-blue-700 px-6 py-8 sm:px-8 text-white relative overflow-hidden">
                {{-- Ornamen --}}
                <div class="absolute top-0 right-0 w-40 h-full opacity-10" style="background: linear-gradient(135deg, white 0%, transparent 50%);"></div>

                <div class="relative z-10">
                    {{-- Breadcrumb & Back Button --}}
                    <div class="flex flex-wrap items-center gap-4 justify-between mb-4">
                        <div class="flex items-center gap-2 text-blue-200 text-xs font-semibold uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"></path></svg>
                            <span>Hasil Scan QR</span>
                            <span class="mx-1">/</span>
                            <span class="text-white">Detail Aset</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-blue-800 bg-white hover:bg-gray-100 border border-transparent rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            KEMBALI KE DASHBOARD
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        {{-- Icon Perangkat --}}
                        <div class="p-4 rounded-2xl border border-white/20" style="background: rgba(255,255,255,0.1);">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        {{-- Detail Aset --}}
                        <div class="flex-1">
                            <h2 class="text-2xl sm:text-3xl font-bold leading-tight">
                                {{ $asset->deviceName?->brand ?? 'Aset' }}
                                <span class="text-blue-200">{{ $asset->deviceName?->name ?? 'BMN' }}</span>
                            </h2>
                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-mono font-bold border border-white/20" style="background: rgba(255,255,255,0.15);">
                                    BMN: {{ $asset?->bmn_number ?? '-' }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border border-green-300/30" style="background: rgba(34,197,94,0.15); color: #bbf7d0;">
                                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                    Tercatat Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Konten Utama --}}
            <div class="p-6 sm:p-8">

                {{-- Grid Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    {{-- Penanggung Jawab --}}
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-bps-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penanggung Jawab</span>
                        </div>
                        <p class="text-gray-900 font-bold text-base">{{ $asset?->user?->name ?? 'Belum Dialokasikan' }}</p>
                    </div>

                    {{-- Lokasi --}}
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lokasi Ruangan</span>
                        </div>
                        <p class="text-gray-900 font-bold text-base">{{ $asset?->room?->name ?? 'N/A' }}</p>
                    </div>

                    {{-- Update Terakhir --}}
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Update Terakhir</span>
                        </div>
                        <p class="text-gray-900 font-bold text-base">{{ $asset?->updated_at?->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>

                {{-- Kondisi Fisik --}}
                <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl p-5 mb-8">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="text-sm font-bold text-gray-700">Kondisi Fisik Perangkat</span>
                    </div>
                    @if($asset->status_kondisi === 'Baik')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                            ✓ Baik
                        </span>
                    @elseif($asset->status_kondisi === 'Rusak Ringan')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                            ⚠ Rusak Ringan
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold text-white border border-red-600" style="background-color: #dc2626;">
                            ✕ Rusak Berat
                        </span>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 my-6"></div>

                {{-- AREA AKSI --}}
                <div class="space-y-4">
                    @php
                        $isAdminOrManager = in_array(session('active_role_id'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_PENGELOLA_ASET]);
                        $canManageLoan = (auth()->id() === ($asset?->user_id ?? null)) || (!($asset?->user_id ?? null) && $isAdminOrManager);
                    @endphp

                    @if($canManageLoan && $pendingLoan)
                        {{-- Pemilik atau Admin/Manager (jika unallocated) bisa menyetujui peminjaman --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-4 shadow-sm text-left">
                            <div class="flex items-start gap-4">
                                <div class="p-2.5 rounded-xl bg-yellow-100 mt-1 shrink-0">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-base font-bold text-yellow-900">Permintaan Pinjaman</h4>
                                    <p class="text-sm text-yellow-800 mt-1 mb-3"><span class="font-bold">{{ $pendingLoan->borrower?->name ?? 'Seseorang' }}</span> ingin meminjam perangkat ini.</p>
                                    
                                    @if($pendingLoan->loan_reason)
                                        <div class="bg-white/60 border border-yellow-200 rounded-lg p-3 mb-4 text-xs text-yellow-900 italic relative">
                                            "{{ $pendingLoan->loan_reason }}"
                                        </div>
                                    @endif

                                    <div class="flex gap-3">
                                        <form action="{{ route('assets.loan.approve', $pendingLoan->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition active:scale-[0.98] shadow-sm flex items-center justify-center gap-2 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('assets.loan.reject', $pendingLoan->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-red-50 text-red-700 border border-red-200 font-bold py-2.5 rounded-lg hover:bg-red-100 transition active:scale-[0.98] flex items-center justify-center gap-2 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($activeLoan && ($activeLoan?->borrower_id ?? 0) === auth()->id())
                        {{-- User sedang meminjam aset ini --}}
                        <form action="{{ route('assets.return', $asset->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-3 px-6 py-5 text-white font-bold text-lg rounded-xl shadow-lg transition-all hover:opacity-90 active:scale-[0.98]" style="background-color: #dc2626;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                <span class="uppercase tracking-wider">Kembalikan Aset Ini</span>
                            </button>
                        </form>

                    @elseif($pendingLoan && ($pendingLoan?->borrower_id ?? 0) === auth()->id())
                        {{-- User sedang menunggu persetujuan peminjaman --}}
                        <div class="flex items-center gap-4 bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-left">
                            <svg class="w-8 h-8 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <h4 class="text-base font-bold text-yellow-900">Menunggu Persetujuan</h4>
                                <p class="text-sm text-yellow-700 mt-0.5">Permintaan pinjaman Anda sedang menunggu persetujuan dari <span class="font-bold underline">{{ $asset?->user?->name ?? 'Admin / Pengelola Aset' }}</span>.</p>
                            </div>
                        </div>

                    @elseif($activeLoan)
                        {{-- Aset sedang dipinjam orang lain --}}
                        <div class="flex items-center gap-4 bg-red-50 border border-red-200 rounded-xl p-6 text-left">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <div>
                                <h4 class="text-base font-bold text-red-900">Aset Tidak Tersedia</h4>
                                <p class="text-sm text-red-700 mt-0.5">Sedang dipinjam oleh <span class="font-black underline">{{ $activeLoan?->borrower?->name ?? 'Seseorang' }}</span>.</p>
                            </div>
                        </div>

                    @elseif($pendingLoan && !$canManageLoan)
                        {{-- Aset sedang diproses peminjaman oleh orang lain --}}
                        <div class="flex items-center gap-4 bg-orange-50 border border-orange-200 rounded-xl p-6 text-left">
                            <svg class="w-8 h-8 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <div>
                                <h4 class="text-base font-bold text-orange-900">Sedang Diproses</h4>
                                <p class="text-sm text-orange-700 mt-0.5">Sedang dalam proses peminjaman oleh <span class="font-black underline">{{ $pendingLoan?->borrower?->name ?? 'Seseorang' }}</span>.</p>
                            </div>
                        </div>

                    @else
                        {{-- Aset Tersedia atau Milik Sendiri (jika tidak ada pending loan) --}}
                        
                        @if(auth()->id() === ($asset?->user_id ?? null))
                            <div class="flex items-center gap-4 bg-green-50 border border-green-200 rounded-xl p-6 text-left">
                                <div class="p-2.5 rounded-xl bg-green-600">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-green-900">Aset Milik Anda</h4>
                                    <p class="text-sm text-green-700 mt-0.5">Anda adalah penanggung jawab perangkat ini.</p>
                                </div>
                            </div>
                        @else
                            @if(!($asset?->user_id ?? null))
                                {{-- Info Aset Unallocated --}}
                                <div class="flex items-center gap-4 bg-blue-50 border border-blue-200 rounded-xl p-6 mb-2 text-left">
                                    <svg class="w-8 h-8 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <h4 class="text-base font-bold text-blue-900">Aset Belum Dialokasikan</h4>
                                        <p class="text-sm text-blue-700 mt-0.5">Aset ini belum memiliki penanggung jawab tetap dan tersedia untuk dipinjam.</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Aksi Utama: PINJAM --}}
                            @if(!isset($mode) || $mode === 'loan')
                                <form action="{{ route('assets.loan', $asset->id) }}" method="POST" class="text-left">
                                    @csrf
                                    <div class="mb-5">
                                        <label for="loan_reason" class="block text-sm font-black text-bps-blue mb-2 uppercase tracking-wide">Alasan Meminjam <span class="text-red-500">*</span></label>
                                        <textarea 
                                            name="loan_reason" 
                                            id="loan_reason" 
                                            rows="3" 
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-bps-blue focus:ring focus:ring-bps-blue/20 transition text-sm py-3 px-4" 
                                            placeholder="Contoh: Dipinjam untuk keperluan rapat di Hotel..."
                                            required
                                        >{{ old('loan_reason') }}</textarea>
                                        @error('loan_reason')
                                            <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="w-full flex items-center justify-center gap-4 px-6 py-5 text-white font-bold text-lg rounded-xl shadow-lg transition-all hover:opacity-90 active:scale-[0.98]" style="background-color: #005A8C;">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                                        <div class="text-left">
                                            <span class="block text-xl font-black uppercase tracking-wide">Pinjam Sekarang</span>
                                            <span class="block text-[11px] text-blue-200 font-medium">Kirim permintaan ke @if($asset?->user_id) pemilik @else Admin @endif</span>
                                        </div>
                                    </button>
                                </form>
                            @endif

                            {{-- Separator --}}
                            @if(!isset($mode) || $mode == '')
                                <div class="flex items-center gap-4 py-2">
                                    <div class="flex-1 border-t border-gray-200"></div>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">atau</span>
                                    <div class="flex-1 border-t border-gray-200"></div>
                                </div>
                            @endif

                            {{-- Aksi Sekunder: AMBIL ALIH --}}
                            @if(!isset($mode) || $mode === 'transfer')
                                <form action="{{ route('assets.takeover', $asset->id) }}" method="POST" onsubmit="promptConfirm(event, 'ambil')">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white border-2 border-blue-900 text-blue-900 font-bold rounded-xl transition-all hover:bg-blue-900 hover:text-white active:scale-[0.98]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        <div class="text-left font-black uppercase text-sm">Ambil Alih Permanen</div>
                                    </button>
                                </form>
                            @endif
                    @endif
                @endif
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="bg-gray-50 px-6 sm:px-8 py-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span>Terakhir diperbarui: {{ $asset?->updated_at?->format('d M Y, H:i') ?? '-' }} WIB</span>
                <span class="font-semibold">Guardian BPS &copy; 2026</span>
            </div>

        </div>
    </div>
</x-app-layout>
