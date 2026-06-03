<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Tiket Layanan Perbaikan') }}
        </h2>
    </x-slot>
    <div class="py-8 space-y-6">

        <div
            class="relative overflow-hidden bg-gradient-to-r from-blue-700 to-indigo-800 rounded-3xl shadow-xl p-8 border border-white/10 group">
            <div
                class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white/10 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-110">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight mb-2">Pusat Layanan IT</h2>
                    <p class="text-blue-100/90 text-sm md:text-base font-medium max-w-2xl">
                        Kelola seluruh permintaan perbaikan hardware jaringan dan approval biaya dalam satu dasbor
                        cerdas.
                    </p>
                </div>
                @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_PIC_RUANGAN, \App\Models\User::ROLE_USER]))
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'createTicketModal')"
                        class="inline-flex items-center justify-center bg-white text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 focus:outline-none shrink-0">
                        <x-lucide-plus class="w-5 h-5 mr-2" /> Buat Tiket Baru
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50/80 backdrop-blur-sm border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3 animate-fade-in-down"
                role="alert">
                <div class="bg-emerald-100 p-2 rounded-xl"><x-lucide-check-circle class="w-5 h-5 text-emerald-600" /></div>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex-1 group/table">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class=" bg-gray-50/80 border-b border-gray-100 whitespace-nowrap">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Tiket</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Kategori</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Teknisi</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Pelapor</th>
                            <th scope="col"
                                class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Tgl. Selesai</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tickets as $ticket)
                            @php $ticketId = $ticket?->id ?? 0; @endphp
                            <tr data-href="{{ route('tickets.show', $ticketId) }}" onclick="window.location.href=this.dataset.href;"
                                class="hover:bg-blue-50/50 transition duration-200 group/row cursor-pointer relative">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-start gap-4 z-10 relative">
                                        <div
                                            class="p-2.5 rounded-xl bg-gray-50 text-gray-400 group-hover/row:bg-blue-600 group-hover/row:text-white group-hover/row:shadow-lg transition-all duration-300 transform group-hover/row:scale-110 group-hover/row:rotate-3 shrink-0">
                                            <x-lucide-ticket class="w-5 h-5" />
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-gray-900 group-hover/row:text-blue-700 transition-colors text-base">
                                                {{ \Illuminate\Support\Str::limit($ticket?->title ?? '-', 50) }}
                                            </div>
                                            <div class="text-xs font-medium text-gray-400 mt-1.5 flex items-center gap-3">
                                                <span class="flex items-center gap-1"><x-lucide-hash class="w-3.5 h-3.5" />
                                                    {{ str_pad($ticket?->id ?? 0, 5, '0', STR_PAD_LEFT) }}</span>
                                                @if(($ticket?->type ?? '') == 'Asset')
                                                    <span
                                                        class="flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md border border-blue-100 font-bold"><x-lucide-monitor
                                                            class="w-3 h-3" /> Aset:
                                                        {{ $ticket?->asset?->bmn_number ?? 'N/A' }}</span>
                                                @else
                                                    <span
                                                        class="flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md border border-purple-100 font-bold"><x-lucide-wrench
                                                            class="w-3 h-3" /> Umum</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 relative z-10 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold border shadow-sm {{ ($ticket?->category ?? '') == 'Service' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : (($ticket?->category ?? '') == 'Troubleshooting' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-gray-50 text-gray-700 border-gray-100') }}">
                                        {{ $ticket?->category ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 relative z-10 whitespace-nowrap">
                                    <div class="flex flex-col items-start gap-1.5">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold border shadow-sm {{ $ticket->status_badge_class }}">
                                            {{ $ticket?->status ?? 'Open' }}
                                        </span>
                                        {!! $ticket->sla_status_badge !!}
                                    </div>
                                </td>
                                <td class="px-6 py-5 relative z-10 whitespace-nowrap">
                                    @if($ticket?->technician)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-7 w-7 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-700 border border-blue-200">
                                                {{ strtoupper(substr($ticket?->technician?->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="text-xs font-bold text-gray-700">
                                                {{ $ticket?->technician?->name ?? '-' }}</div>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-gray-400">Belum ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 relative z-10 whitespace-nowrap">
                                    <div class="max-w-[150px] truncate text-sm font-semibold text-gray-700">
                                        {{ $ticket?->reporter?->name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-5 relative z-10 whitespace-nowrap text-center text-sm font-bold text-gray-600">
                                    @if(($ticket?->status ?? '') == 'Selesai')
                                        {{ $ticket?->updated_at?->format('d/m/Y') ?? '-' }}
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right relative z-10 whitespace-nowrap">
                                    <x-lucide-chevron-right
                                        class="w-5 h-5 text-gray-300 ml-auto group-hover/row:text-blue-500 group-hover/row:translate-x-1 transition-all" />
                                </td>

                                <div
                                    class="absolute inset-0 bg-blue-50/0 group-hover/row:bg-blue-50/50 pointer-events-none transition-colors duration-200">
                                </div>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center max-w-[280px] mx-auto">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                            <x-lucide-inbox class="w-8 h-8 text-gray-300" />
                                        </div>
                                        <h4 class="text-gray-900 font-bold mb-1">Belum Ada Tiket Perbaikan</h4>
                                        <p class="text-sm text-gray-500 leading-relaxed">Saat ini belum ada request layanan
                                            atau riwayat perbaikan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_PIC_RUANGAN, \App\Models\User::ROLE_USER]))
        <x-modal name="createTicketModal" focusable>
            <form method="post" action="{{ route('tickets.store') }}"
              enctype="multipart/form-data"
              x-data="{ priority: 'Sedang', photoPreview: null, photoName: null,
                        setPhoto(e) {
                            const file = e.target.files[0];
                            if (!file) return;
                            this.photoName = file.name;
                            const reader = new FileReader();
                            reader.onload = (ev) => { this.photoPreview = ev.target.result; };
                            reader.readAsDataURL(file);
                        },
                        clearPhoto() {
                            this.photoPreview = null;
                            this.photoName = null;
                            this.$refs.photoInput.value = '';
                        }
                      }">
                @csrf

                <input type="hidden" name="priority" :value="priority">

                <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 rounded-xl">
                            <x-lucide-ticket class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Buat Tiket Layanan</h2>
                            <p class="text-[11px] text-gray-400 mt-0.5">Isi detail laporan kerusakan atau kendala IT</p>
                        </div>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')"
                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <div class="px-6 py-4 space-y-4 max-h-[65vh] overflow-y-auto">

                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center gap-2.5 px-4 py-3 bg-gray-50/80 border-b border-gray-100">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">1</span>
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Identifikasi Sumber Masalah</h3>
                        </div>
                        <div class="p-4 space-y-3">

                            <div>
                                <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Aset BMN Terkait <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                                </label>
                                <select id="asset_id" name="asset_id"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">— Tidak Terkait Aset / Masalah Umum —</option>
                                    @foreach($userAssets as $asset)
                                        <option value="{{ $asset->id }}">
                                            {{ $asset->deviceName?->name ?? $asset->bmn_number }}
                                            {{ $asset->room?->name ? '(' . $asset->room->name . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1 italic">Kosongkan jika melapor masalah fasilitas atau bantuan IT umum.</p>
                            </div>

                            <div>
                                <label for="room_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Lokasi / Ruangan <span class="text-red-500">*</span>
                                </label>
                                <select id="room_id" name="room_id" required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">— Pilih Ruangan Tempat Kendala —</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1" :messages="$errors->get('room_id')" />
                            </div>

                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center gap-2.5 px-4 py-3 bg-gray-50/80 border-b border-gray-100">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">2</span>
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Detail Laporan</h3>
                        </div>
                        <div class="p-4 space-y-3">

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select id="category" name="category" required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">— Pilih Kategori —</option>
                                    <option value="Service">🔧 Service — Perbaikan Fisik / Hardware</option>
                                    <option value="Troubleshooting">💻 Troubleshooting — Software / Jaringan</option>
                                </select>
                                <x-input-error class="mt-1" :messages="$errors->get('category')" />
                            </div>

                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Judul Masalah <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="title" name="title" required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Contoh: Layar berkedip, PC tidak menyala, keyboard macet..." />
                                <x-input-error class="mt-1" :messages="$errors->get('title')" />
                            </div>

                            <div>
                                <p class="block text-sm font-medium text-gray-700 mb-2">
                                    Tingkat Prioritas <span class="text-red-500">*</span>
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach([
                                        ['value' => 'Rendah', 'emoji' => '🟢', 'label' => 'Rendah', 'sub' => 'Masih bisa dipakai'],
                                        ['value' => 'Sedang', 'emoji' => '🟡', 'label' => 'Sedang', 'sub' => 'Ganggu aktivitas'],
                                        ['value' => 'Tinggi', 'emoji' => '🔴', 'label' => 'Tinggi', 'sub' => 'Mati total / Urgent'],
                                    ] as $p)
                                        <label
                                            @click="priority = '{{ $p['value'] }}'"
                                            :class="priority === '{{ $p['value'] }}'
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 bg-white hover:border-gray-300'"
                                            class="flex flex-col items-center gap-1 py-3 px-2 rounded-lg border-2 cursor-pointer transition-all duration-150 select-none text-center">
                                            <span class="text-xl leading-none">{{ $p['emoji'] }}</span>
                                            <span class="text-xs font-bold text-gray-800 mt-1">{{ $p['label'] }}</span>
                                            <span class="text-[10px] text-gray-400 leading-tight">{{ $p['sub'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Keterangan Detail <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" name="description" rows="3" required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm resize-none"
                                    placeholder="Jelaskan secara rinci kendala yang dialami..."></textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('description')" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Foto Kerusakan <span class="text-gray-400 font-normal text-xs">(opsional, maks. 5MB)</span>
                                </label>

                                <div
                                    x-show="!photoPreview"
                                    class="relative flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 rounded-xl py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all"
                                    @click="$refs.photoInput.click()">
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-600">Klik untuk pilih foto</p>
                                        <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, WEBP — maks. 5MB</p>
                                    </div>
                                </div>

                                <div x-show="photoPreview" class="relative rounded-xl overflow-hidden border border-gray-200">
                                    <img :src="photoPreview" class="w-full max-h-48 object-cover" alt="Preview foto">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-2 flex items-center justify-between">
                                        <span x-text="photoName" class="text-white text-xs font-medium truncate max-w-[70%]"></span>
                                        <button type="button" @click.stop="clearPhoto()"
                                            class="flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-2.5 py-1 rounded-lg transition-colors">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>

                                <input type="file" name="photo" accept="image/*" x-ref="photoInput" class="hidden" @change="setPhoto($event)">
                                <x-input-error class="mt-1" :messages="$errors->get('photo')" />
                            </div>

                        </div>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">
                            <x-lucide-x class="w-3.5 h-3.5" /> Batal
                        </button>
                        <button type="submit"
                            class="flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all">
                            <x-lucide-send class="w-3.5 h-3.5" /> Kirim Laporan
                        </button>
                    </div>
                </div>

            </form>
        </x-modal>
    @endif
</x-app-layout>