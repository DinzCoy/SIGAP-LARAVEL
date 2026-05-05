<x-app-layout>
    {{-- Satu x-data di root — semua state terpusat --}}
    <div class="max-w-xl mx-auto"
         x-data="{
             selectedAssetId: '{{ old('asset_id', '') }}',
             priority: '{{ old('priority', 'Sedang') }}',
             assets: {{ $roomAssets->map(fn($a) => [
                 'id'      => $a->id,
                 'bmn'     => $a->bmn_number ?? '-',
                 'serial'  => $a->serial_number ?? '-',
                 'kondisi' => $a->status_kondisi ?? 'Baik',
             ])->values()->toJson() }},
             get selectedInfo() {
                 if (!this.selectedAssetId) return null;
                 return this.assets.find(a => String(a.id) === String(this.selectedAssetId)) ?? null;
             },
             get kondisiClass() {
                 const k = this.selectedInfo?.kondisi ?? '';
                 if (k === 'Rusak Berat')  return 'text-red-600 font-semibold';
                 if (k === 'Rusak Ringan') return 'text-amber-600 font-semibold';
                 return 'text-emerald-600 font-semibold';
             }
         }">

        <!-- ── Header ── -->
        <div class="flex items-start gap-3 mb-6">
            <a href="{{ route('ruangan.dashboard') }}"
               class="mt-1 p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 shadow-sm transition-all shrink-0">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900 leading-tight">Laporkan Kerusakan Aset</h1>
                <p class="text-xs text-gray-500 mt-0.5">Pilih aset di ruangan Anda, lalu lengkapi detail laporannya.</p>
            </div>
        </div>

        <!-- ── Validasi Error ── -->
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex gap-2 items-start">
                <x-lucide-alert-circle class="w-4 h-4 shrink-0 mt-0.5 text-red-500" />
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($roomAssets->isEmpty())
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center">
                <x-lucide-package-x class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <h3 class="text-gray-700 font-semibold mb-1 text-sm">Tidak Ada Aset Terdaftar</h3>
                <p class="text-xs text-gray-400 mb-4">Belum ada aset di ruangan yang Anda kelola.</p>
                <a href="{{ route('ruangan.dashboard') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm transition-colors">
                    <x-lucide-arrow-left class="w-4 h-4" /> Kembali
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('ruangan.lapor.store') }}" class="space-y-4">
                @csrf
                {{-- Hidden input priority — dikontrol Alpine --}}
                <input type="hidden" name="priority" :value="priority">

                <!-- ── Card 1: Pilih Aset ── -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-gray-100 bg-gray-50/80 rounded-t-xl">
                        <span class="w-5 h-5 rounded-full bg-teal-500 text-white flex items-center justify-center text-[10px] font-bold shrink-0">1</span>
                        <h2 class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Pilih Aset yang Rusak</h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Aset di Ruangan Anda <span class="text-red-500">*</span>
                            </label>
                            <select id="asset_id" name="asset_id" required x-model="selectedAssetId"
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                                <option value="">-- Pilih Aset --</option>
                                @foreach($roomAssets->groupBy(fn($a) => $a->room?->nama_ruangan ?? $a->room?->name ?? 'Tanpa Ruangan') as $namaRuangan => $asets)
                                    <optgroup label="📍 {{ $namaRuangan }}">
                                        @foreach($asets as $asset)
                                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                                {{ $asset->deviceName?->name ?? 'Perangkat' }}
                                                {{ $asset->deviceName?->brand ? '(' . $asset->deviceName->brand . ')' : '' }}
                                                — BMN: {{ $asset->bmn_number ?? '-' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info aset terpilih -->
                        <div x-show="selectedInfo !== null"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             style="display:none"
                             class="rounded-lg border border-teal-100 bg-teal-50 p-3">
                            <p class="text-[10px] font-bold text-teal-600 uppercase tracking-widest mb-2">ℹ Info Aset</p>
                            <div class="grid grid-cols-3 gap-3 text-sm">
                                <div>
                                    <p class="text-[10px] text-gray-400 mb-0.5">No BMN</p>
                                    <p class="text-xs font-semibold text-gray-800" x-text="selectedInfo?.bmn ?? '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 mb-0.5">Serial</p>
                                    <p class="text-[10px] font-mono text-gray-700 break-all" x-text="selectedInfo?.serial ?? '-'"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 mb-0.5">Kondisi</p>
                                    <p class="text-xs" :class="kondisiClass" x-text="selectedInfo?.kondisi ?? '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Card 2: Detail Laporan ── -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-gray-100 bg-gray-50/80 rounded-t-xl">
                        <span class="w-5 h-5 rounded-full bg-teal-500 text-white flex items-center justify-center text-[10px] font-bold shrink-0">2</span>
                        <h2 class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Detail Laporan Kerusakan</h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">

                        <!-- Kategori -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category" required
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                                <option value="Service" {{ old('category', 'Service') == 'Service' ? 'selected' : '' }}>🔧 Service — Perbaikan Fisik / Hardware</option>
                                <option value="Troubleshooting" {{ old('category') == 'Troubleshooting' ? 'selected' : '' }}>💻 Troubleshooting — Software / Jaringan</option>
                            </select>
                        </div>

                        <!-- Judul -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Judul Masalah <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" required
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm"
                                placeholder="Contoh: Monitor tidak menyala, keyboard macet..."
                                value="{{ old('title') }}">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prioritas — Alpine :class binding, TIDAK ada vanilla JS -->
                        <div>
                            <p class="block text-sm font-medium text-gray-700 mb-2">
                                Tingkat Prioritas <span class="text-red-500">*</span>
                            </p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach([
                                    ['value' => 'Rendah', 'emoji' => '🟢', 'label' => 'Rendah',  'sub' => 'Masih bisa dipakai'],
                                    ['value' => 'Sedang', 'emoji' => '🟡', 'label' => 'Sedang',  'sub' => 'Ganggu aktivitas'],
                                    ['value' => 'Tinggi', 'emoji' => '🔴', 'label' => 'Tinggi',  'sub' => 'Mati total / Urgent'],
                                ] as $p)
                                    <label
                                        @click="priority = '{{ $p['value'] }}'"
                                        :class="priority === '{{ $p['value'] }}'
                                            ? 'border-teal-500 bg-teal-50'
                                            : 'border-gray-200 bg-white hover:border-gray-300'"
                                        class="flex flex-col items-center gap-1 py-3 px-2 rounded-lg border-2 cursor-pointer transition-all duration-150 select-none text-center">
                                        <span class="text-xl leading-none">{{ $p['emoji'] }}</span>
                                        <span class="text-xs font-bold text-gray-800 mt-1">{{ $p['label'] }}</span>
                                        <span class="text-[10px] text-gray-400 leading-tight">{{ $p['sub'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Keterangan Detail <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" rows="3" required
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm resize-none"
                                placeholder="Jelaskan kondisi kerusakan dan tindakan yang sudah dicoba...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- ── Tombol Aksi — grid 2 kolom, tidak bisa terpotong ── -->
                <div class="grid grid-cols-2 gap-3 py-2">
                    <a href="{{ route('ruangan.dashboard') }}"
                       class="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">
                        <x-lucide-x class="w-3.5 h-3.5" /> Batal
                    </a>
                    <button type="submit"
                        class="flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition-all">
                        <x-lucide-send class="w-3.5 h-3.5" /> Kirim Laporan
                    </button>
                </div>

            </form>
        @endif
    </div>
</x-app-layout>