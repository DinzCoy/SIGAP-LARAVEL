<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center p-2 rounded-full hover:bg-gray-200 text-gray-600 transition-colors tooltip relative group">
                    <x-lucide-arrow-left class="w-5 h-5" />
                    <span class="absolute top-10 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Kembali</span>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <x-lucide-tag class="w-5 h-5 text-gray-500" /> {{ __('Detail Tiket Layanan') }} <span class="text-bps-blue font-mono font-bold">#{{ str_pad($ticket?->id ?? 0, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="p-4 lg:p-8">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 mb-6 rounded-2xl flex items-center gap-3">
                <x-lucide-check-circle class="w-5 h-5 text-emerald-500" />
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-2xl flex items-start gap-3">
                <x-lucide-alert-circle class="w-5 h-5 text-red-500 mt-0.5" />
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Left Column: Info & Status -->
            <div class="lg:col-span-1 flex flex-col space-y-6">
                <!-- Ticket Info Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <x-lucide-ticket class="w-24 h-24" />
                    </div>
                    <div class="p-6 border-b border-gray-50 flex items-center gap-3 relative z-10">
                        <div class="p-2.5 bg-blue-50 text-bps-blue rounded-xl">
                            <x-lucide-info class="w-5 h-5" />
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Informasi Tiket</h3>
                    </div>
                    <div class="p-6 space-y-5 text-sm relative z-10">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Judul Masalah</p>
                            <p class="font-bold text-gray-900 leading-snug">{{ $ticket?->title ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Jenis Layanan</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl {{ $ticket->type_badge_class }}">
                                <x-dynamic-component :component="'lucide-' . $ticket->type_icon" class="w-3.5 h-3.5" />
                                {{ $ticket->type_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kategori Masalah</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl {{ $ticket->category_badge_class }}">
                                <x-dynamic-component :component="'lucide-' . $ticket->category_icon" class="w-3.5 h-3.5" />
                                {{ $ticket?->category ?? 'Belum Ditentukan' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pelapor</p>
                            <p class="font-medium text-gray-800 flex items-center gap-2">
                                <x-lucide-user class="w-3.5 h-3.5 text-gray-400" />
                                {{ $ticket?->reporter?->name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Lokasi Ruangan</p>
                            <p class="font-medium text-gray-800 flex items-center gap-2">
                                <x-lucide-map-pin class="w-3.5 h-3.5 text-gray-400" />
                                {{ $ticket?->room?->name ?? 'Tidak Ditentukan' }}
                            </p>
                        </div>
                        @if(($ticket?->type ?? '') == 'Asset')
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Aset Terkait</p>
                            <p class="font-medium text-gray-800 flex items-center gap-2">
                                <x-lucide-monitor class="w-3.5 h-3.5 text-gray-400" />
                                {{ $ticket?->asset?->bmn_number ?? 'N/A' }}
                            </p>
                            @if($ticket?->asset?->mac_address)
                              <p class="text-xs font-mono text-gray-500 mt-1 ml-5">{{ $ticket?->asset?->mac_address }}</p>
                            @endif
                        </div>
                        @endif
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 text-center lg:text-left">Ketua Tim</p>
                            <p class="font-medium text-gray-800 flex items-center justify-center lg:justify-start gap-2">
                                <x-lucide-shield-check class="w-3.5 h-3.5 text-gray-400" />
                                {{ $ticket?->teamLeader?->name ?? 'Belum Ditentukan' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1 text-center lg:text-left">Teknisi Bertugas</p>
                            <p class="font-medium text-gray-800 flex items-center justify-center lg:justify-start gap-2">
                                <x-lucide-wrench class="w-3.5 h-3.5 text-gray-400" />
                                {{ $ticket?->technician?->name ?? 'Belum Ditugaskan' }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Prioritas</p>
                            <span class="px-3 py-1 text-xs font-bold rounded-xl {{ $ticket->priority_badge_class }}">
                                {{ $ticket?->priority ?? '-' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-2 pt-2 border-t border-gray-50">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</p>
                            <span class="inline-flex w-max items-center px-4 py-2 rounded-xl text-sm font-bold border {{ $ticket->status_badge_class }}">
                                {{ $ticket?->status ?? 'Open' }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-gray-50">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Estimasi Biaya</p>
                            <p class="font-bold text-bps-orange text-xl">Rp {{ number_format($ticket?->estimated_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="pt-2 border-t border-gray-50 flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status SLA</p>
                                {!! $ticket->sla_status_badge !!}
                            </div>
                            @if($ticket->responded_at)
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu Respons</p>
                                    <p class="text-[11px] font-bold text-gray-800">{{ $ticket->response_time_hours }} Jam</p>
                                </div>
                            @endif
                            @if($ticket->resolved_at)
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Penyelesaian</p>
                                    <p class="text-[11px] font-bold text-gray-800">{{ $ticket->resolution_time_hours }} Jam</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Workflow Actions Card (Role Based) -->
                @php
                    $canAct = true;
                    // Teknisi tidak bisa memproses jika tiket aset masih menunggu Pengelola
                    if (session('active_role_id') == \App\Models\User::ROLE_TEKNISI && ($ticket?->type ?? '') == 'Asset' && ($ticket?->status ?? '') == 'Menunggu Pengecekan Pengelola') {
                        $canAct = false;
                    }
                @endphp
                @can('updateStatus', $ticket)
                    @if($canAct)
                    <div class="bg-white rounded-3xl shadow-sm border border-blue-100 overflow-hidden relative">
                        <div class="p-6 border-b border-blue-50 flex items-center gap-3 bg-blue-50/30">
                            <div class="p-2.5 bg-blue-100 text-bps-blue rounded-xl shadow-inner">
                                <x-lucide-settings class="w-5 h-5" />
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Aksi Workflow</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('tickets.updateStatus', $ticket->id) }}" method="POST" class="space-y-5">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label for="status" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ubah Status</label>
                                    <select id="status" name="status" class="w-full bg-gray-50 border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-bps-blue/20 focus:border-bps-blue text-sm font-medium rounded-xl py-3 px-4 transition-all hover:bg-white cursor-pointer shadow-inner">
                                    @if(session('active_role_id') == \App\Models\User::ROLE_ADMIN)
                                        <option value="Open" {{ ($ticket?->status ?? '') == 'Open' ? 'selected' : '' }}>📝 Open (Buka)</option>
                                        <option value="Menunggu Pengecekan Pengelola" {{ ($ticket?->status ?? '') == 'Menunggu Pengecekan Pengelola' ? 'selected' : '' }}>🔍 Menunggu Pengecekan Pengelola</option>
                                        <option value="Diteruskan ke Ketua Tim" {{ ($ticket?->status ?? '') == 'Diteruskan ke Ketua Tim' ? 'selected' : '' }}>⏭️ Teruskan ke Ketua Tim</option>
                                        <option value="Approved" {{ ($ticket?->status ?? '') == 'Approved' ? 'selected' : '' }}>👍 Disetujui (Approved)</option>
                                    @endif
                                    
                                    @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_TEKNISI]))
                                        <option value="In Progress" {{ ($ticket?->status ?? '') == 'In Progress' ? 'selected' : '' }}>🔧 In Progress (Diambil Teknisi)</option>
                                        <option value="Menunggu Persetujuan Biaya" {{ ($ticket?->status ?? '') == 'Menunggu Persetujuan Biaya' ? 'selected' : '' }}>⚠️ Ajukan Persetujuan Biaya</option>
                                        <option value="Selesai" {{ ($ticket?->status ?? '') == 'Selesai' ? 'selected' : '' }}>✅ Selesai (Ditutup)</option>
                                        <option value="Dibatalkan" {{ ($ticket?->status ?? '') == 'Dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                    @endif
                                    
                                    @if(session('active_role_id') == \App\Models\User::ROLE_PENGELOLA_ASET)
                                         <option value="Diteruskan ke Ketua Tim" {{ ($ticket?->status ?? '') == 'Diteruskan ke Ketua Tim' ? 'selected' : '' }}>⏭️ Teruskan ke Ketua Tim</option>
                                         <option value="Approved" {{ ($ticket?->status ?? '') == 'Approved' ? 'selected' : '' }}>✅ Setujui Biaya (Approved)</option>
                                         <option value="Dibatalkan" {{ ($ticket?->status ?? '') == 'Dibatalkan' ? 'selected' : '' }}>❌ Tolak / Batalkan</option>
                                    @endif

                                    @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_KETUA_TIM]))
                                         <option value="Diteruskan ke Teknisi" {{ ($ticket?->status ?? '') == 'Diteruskan ke Teknisi' ? 'selected' : '' }}>👤 Tugaskan ke Teknisi</option>
                                         @if(session('active_role_id') == \App\Models\User::ROLE_KETUA_TIM)
                                             <option value="Selesai" {{ ($ticket?->status ?? '') == 'Selesai' ? 'selected' : '' }}>✅ Selesai (Ditutup)</option>
                                             <option value="Dibatalkan" {{ ($ticket?->status ?? '') == 'Dibatalkan' ? 'selected' : '' }}>❌ Batalkan</option>
                                         @endif
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label for="category" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tentukan Kategori</label>
                                <select id="category" name="category" class="w-full bg-gray-50 border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-bps-blue/20 focus:border-bps-blue text-sm font-medium rounded-xl py-3 px-4 transition-all hover:bg-white cursor-pointer shadow-inner">
                                    <option value="" disabled {{ is_null($ticket?->category) ? 'selected' : '' }}>-- Pilih Kategori --</option>
                                    <option value="Service" {{ ($ticket?->category ?? '') == 'Service' ? 'selected' : '' }}>🛠️ Service (Perbaikan Fisik)</option>
                                    <option value="Troubleshooting" {{ ($ticket?->category ?? '') == 'Troubleshooting' ? 'selected' : '' }}>🔍 Troubleshooting (Sistem/Jaringan)</option>
                                </select>
                            </div>

                            @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_KETUA_TIM]) && count($technicians) > 0)
                            <div class="mt-4">
                                <label for="technician_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Teknisi</label>
                                <select id="technician_id" name="technician_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-bps-blue/20 focus:border-bps-blue text-sm font-medium rounded-xl py-3 px-4 transition-all hover:bg-white cursor-pointer shadow-inner">
                                    <option value="" disabled {{ is_null($ticket?->technician_id) ? 'selected' : '' }}>-- Pilih Personel Teknisi --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" {{ ($ticket?->technician_id ?? 0) == $tech->id ? 'selected' : '' }}>👨‍🔧 {{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_PENGELOLA_ASET]))
                            <div>
                                <label for="estimated_cost" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Estimasi Biaya (Rp)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="estimated_cost" id="estimated_cost" value="{{ $ticket?->estimated_cost ?? 0 }}" class="w-full bg-gray-50 border border-gray-200 pl-10 pr-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-bps-blue/20 focus:border-bps-blue text-sm font-bold rounded-xl py-3 transition-all hover:bg-white shadow-inner input-no-spin" placeholder="Contoh: 150000">
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1.5">*Hanya Pengelola Aset yang dapat menentukan estimasi biaya final.</p>
                            </div>
                            @endif

                            <button type="submit" class="w-full bg-bps-blue hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition-all hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2 mt-4">
                                <x-lucide-save class="w-4 h-4 mt-0.5" /> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
                    @else
                    <div class="bg-orange-50 rounded-3xl border border-orange-100 p-6 text-center">
                        <div class="w-12 h-12 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                            <x-lucide-lock class="w-6 h-6" />
                        </div>
                        <h3 class="font-bold text-orange-800 mb-1">Menunggu Validasi</h3>
                        <p class="text-xs text-orange-600">Anda baru dapat menindaklanjuti tiket aset ini setelah dikonfirmasi oleh Pengelola Aset.</p>
                    </div>
                    @endif
                @endcan
            </div>

            <!-- Right Column: Description & Thread -->
            <div class="lg:col-span-3 flex flex-col space-y-6 flex-1">
                <!-- Description -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden shrink-0">
                    <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                        <div class="p-2.5 bg-blue-50 text-bps-blue rounded-xl">
                            <x-lucide-file-text class="w-5 h-5" />
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Deskripsi Keluhan</h3>
                    </div>
                    <div class="p-6 text-gray-700 leading-relaxed bg-blue-50/20 text-[15px]">
                        {{ $ticket?->description ?? '-' }}
                    </div>
                </div>

                <!-- WhatsApp-style Chat Log -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[700px] relative">
                    <!-- Chat Header -->
                    <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center z-10 shadow-sm relative">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-bps-blue font-bold shadow-inner">
                                {{ substr($ticket?->technician?->name ?? 'T', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Diskusi & Log Tindakan</h3>
                                <p class="text-xs text-emerald-500 font-bold flex items-center gap-1.5 mt-0.5 tracking-wide">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> SYSTEM LOG / CHAT
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-500 px-3 py-1.5 bg-gray-100 rounded-xl flex items-center gap-1.5 border border-gray-200">
                            <x-lucide-message-circle class="w-4 h-4" /> {{ $ticket?->replies?->count() ?? 0 }}
                        </span>
                    </div>

                    <!-- Chat Box (Background Pattern) -->
                    <div id="chatBoxContainer" class="flex-1 p-6 overflow-y-auto space-y-6 relative" style="background-color: #f7f9fa; background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23a0aec0\' fill-opacity=\'0.1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E'); scroll-behavior: smooth;">
                        
                        <!-- Auto-Generated System Message -->
                        <div class="flex justify-center mb-6">
                            <div class="bg-yellow-100/90 backdrop-blur-sm border border-yellow-200 text-yellow-800 text-[11px] font-bold px-4 py-2 rounded-full shadow-sm text-center max-w-md flex flex-col gap-1">
                                <span>Tiket #{{ str_pad($ticket?->id ?? 0, 5, '0', STR_PAD_LEFT) }} dibuat pada {{ $ticket?->created_at?->format('d M Y, H:i') ?? '-' }}.</span>
                                <span class="font-medium opacity-80">Semua diskusi & log tindakan terekam dalam sistem E-Monev.</span>
                            </div>
                        </div>

                        @forelse($ticket->replies as $reply)
                            @php
                                $isMine = $reply->user_id == auth()->id();
                                $isTechnician = $reply->user_id == $ticket->technician_id;
                                $isReporter = $reply->user_id == $ticket->reported_by;
                            @endphp
                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} group fade-in">
                                <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }} max-w-[75%] lg:max-w-[60%]">
                                    @if(!$isMine)
                                        <span class="text-[11px] font-bold text-gray-500 mb-1.5 ml-1 flex items-center gap-1.5 bg-white/50 px-2 py-0.5 rounded-full w-max backdrop-blur-sm">
                                            @if($isTechnician)
                                                <x-lucide-wrench class="w-3 h-3 text-bps-blue" />
                                            @elseif($isReporter)
                                                <x-lucide-user class="w-3 h-3 text-bps-orange" />
                                            @else
                                                <x-lucide-shield class="w-3 h-3 text-emerald-600" />
                                            @endif
                                            {{ $reply?->user?->name ?? 'User' }}
                                        </span>
                                    @endif
                                    
                                    <div class="relative px-5 py-3.5 rounded-2xl shadow-sm flex flex-col justify-between 
                                        {{ $isMine 
                                            ? 'bg-gradient-to-br from-bps-blue to-blue-700 text-white rounded-tr-sm shadow-blue-900/10' 
                                            : 'bg-white text-gray-800 border-none shadow-gray-200/50 rounded-tl-sm' 
                                        }}">
                                        
                                        <div class="text-[14.5px] leading-relaxed whitespace-pre-line break-words w-full">
                                            {{ $reply?->message ?? '-' }}
                                        </div>
                                        
                                        <div class="text-[10px] mt-2.5 flex items-center {{ $isMine ? 'justify-end text-blue-200' : 'justify-end text-gray-400' }} gap-1 font-medium w-full">
                                            {{ $reply?->created_at?->format('H:i') ?? '-' }}
                                            @if($isMine)
                                                <x-lucide-check-check class="w-3.5 h-3.5 text-blue-300" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-48 opacity-40 mt-10">
                                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                     <x-lucide-messages-square class="w-10 h-10 text-gray-400" />
                                </div>
                                <p class="text-sm font-bold text-gray-500">Belum ada diskusi atau tindakan.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Chat Input area -->
                    @can('reply', $ticket)
                    <div class="p-4 bg-white border-t border-gray-100 z-10 w-full shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.02)]">
                         <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" class="relative">
                            @csrf
                            <div class="flex items-end gap-3 bg-gray-50 p-2 rounded-3xl border border-gray-200 focus-within:border-bps-blue focus-within:ring-2 focus-within:ring-bps-blue/10 focus-within:bg-white transition-all shadow-inner">
                                
                                <button type="button" class="p-3.5 text-gray-400 hover:text-bps-blue rounded-full hover:bg-blue-50 transition-colors shrink-0 outline-none">
                                    <x-lucide-paperclip class="w-5 h-5" />
                                </button>

                                <textarea name="message" id="chatInput" rows="1" class="flex-1 max-h-32 bg-transparent border-0 focus:ring-0 resize-none py-3.5 px-2 text-[15px] text-gray-700 leading-snug" placeholder="Ketik balasan Anda di sini... (Shift+Enter untuk baris baru)" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                                
                                <button type="submit" class="w-12 h-12 bg-bps-blue hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-md transition-transform hover:scale-105 active:scale-95 shrink-0 outline-none">
                                    <x-lucide-send class="w-5 h-5 mt-0.5 ml-0.5" />
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="p-5 bg-gray-50 text-center flex items-center justify-center gap-2 border-t border-gray-200 z-10">
                        <x-lucide-lock class="w-4 h-4 text-gray-500" />
                        <span class="text-sm font-bold text-gray-500">Tiket ini telah ditutup. Sesi diskusi dikunci secara sistem.</span>
                    </div>
                    @endcan
                </div>
            </div>

        </div>
    </div>
    </div>
</x-app-layout>
