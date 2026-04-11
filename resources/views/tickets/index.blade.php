<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Tiket Layanan Perbaikan') }}
        </h2>
    </x-slot>

    <div class="py-8 space-y-6">
        <!-- Header Banner -->
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
                @if(in_array(session('active_role_id'), [2, 5, 3, 4, 6]))
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

        <!-- Main Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex-1 group/table">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Tiket</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Prioritas</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Pelapor</th>
                            <th scope="col"
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tickets as $ticket)
                            <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'"
                                class="hover:bg-blue-50/50 transition duration-200 group/row cursor-pointer relative">
                                <td class="px-6 py-5">
                                    <div class="flex items-start gap-4 z-10 relative">
                                        <div
                                            class="p-2.5 rounded-xl bg-gray-50 text-gray-400 group-hover/row:bg-blue-600 group-hover/row:text-white group-hover/row:shadow-lg transition-all duration-300 transform group-hover/row:scale-110 group-hover/row:rotate-3 shrink-0">
                                            <x-lucide-ticket class="w-5 h-5" />
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-gray-900 group-hover/row:text-blue-700 transition-colors text-base">
                                                {{ \Illuminate\Support\Str::limit($ticket->title, 50) }}
                                            </div>
                                            <div class="text-xs font-medium text-gray-400 mt-1.5 flex items-center gap-3">
                                                <span class="flex items-center gap-1"><x-lucide-hash class="w-3.5 h-3.5" />
                                                    {{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                @if($ticket->type == 'Asset')
                                                    <span
                                                        class="flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md border border-blue-100 font-bold"><x-lucide-monitor
                                                            class="w-3 h-3" /> Aset:
                                                        {{ $ticket->asset->bmn_number ?? 'N/A' }}</span>
                                                @else
                                                    <span
                                                        class="flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md border border-purple-100 font-bold"><x-lucide-wrench
                                                            class="w-3 h-3" /> Umum</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 relative z-10">
                                    @php
                                        $statusColors = [
                                            'Menunggu Pengecekan Pengelola' => 'bg-orange-50 text-orange-700 border-orange-100',
                                            'Diteruskan ke Teknisi' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                                            'Open' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            'In Progress' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'Menunggu Persetujuan Biaya' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                            'Approved' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'Dibatalkan' => 'bg-red-50 text-red-700 border-red-100',
                                        ];
                                        $badgeClass = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold border shadow-sm {{ $badgeClass }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 relative z-10">
                                    @php
                                        $prioColors = [
                                            'Rendah' => 'bg-emerald-50 text-emerald-700',
                                            'Sedang' => 'bg-amber-50 text-amber-700',
                                            'Tinggi' => 'bg-red-50 text-red-700',
                                        ];
                                        $pBadge = $prioColors[$ticket->priority] ?? 'bg-gray-50 text-gray-700';
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $pBadge }}">
                                        <x-lucide-zap class="w-3.5 h-3.5" />
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 relative z-10">
                                    <div class="max-w-[150px] truncate text-sm font-semibold text-gray-700">
                                        {{ $ticket->reporter->name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right relative z-10">
                                    <x-lucide-chevron-right
                                        class="w-5 h-5 text-gray-300 ml-auto group-hover/row:text-blue-500 group-hover/row:translate-x-1 transition-all" />
                                </td>
                                <!-- Highlight Overlay -->
                                <div
                                    class="absolute inset-0 bg-blue-50/0 group-hover/row:bg-blue-50/50 pointer-events-none transition-colors duration-200">
                                </div>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
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

    <!-- Modal Create Ticket -->
    @if(in_array(session('active_role_id'), [2, 5, 3, 4, 6]))
        <x-modal name="createTicketModal" focusable>
            <form method="post" action="{{ route('tickets.store') }}" class="p-6" x-data="{ type: 'Asset' }">
                @csrf
                <h2 class="text-lg font-medium text-bps-blue mb-4">
                    {{ __('Buat Tiket Layanan Perbaikan Baru') }}
                </h2>

                <div class="space-y-4">

                    <!-- Pilihan Tipe -->
                    <div>
                        <x-input-label value="{{ __('Jenis Layanan') }}" />
                        <div class="mt-2 space-y-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" class="form-radio text-bps-blue" name="type" value="Asset"
                                    x-model="type">
                                <span class="ml-2">Perbaikan Perangkat BMN / Aset Sendiri</span>
                            </label>
                            <br>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" class="form-radio text-purple-600" name="type" value="General"
                                    x-model="type">
                                <span class="ml-2">Bantuan Troubleshooting Umum (Non-Aset, misal AC, Jaringan)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Asset Selection -->
                    <div x-show="type === 'Asset'">
                        <x-input-label for="asset_id" value="{{ __('Pilih Aset (PC)') }}" />
                        <select id="asset_id" name="asset_id"
                            class="border-gray-300 focus:border-bps-blue focus:ring-bps-blue rounded-md shadow-sm w-full mt-1"
                            x-bind:required="type === 'Asset'">
                            <option value="">-- Pilih Aset --</option>
                            @foreach(\App\Models\Asset::all() as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->bmn_number }}
                                    {{ $asset->room_id ? '(' . $asset->room_id . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('asset_id')" />
                        <p class="text-xs text-gray-500 mt-1">Laporan perangkat ini akan melalui Pengelola Aset sebelum
                            diteruskan ke Teknisi.</p>
                    </div>

                    <!-- Title -->
                    <div>
                        <x-input-label for="title" value="{{ __('Judul Masalah') }}" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required
                            placeholder="Contoh: Layar berkedip atau PC tidak mau menyala" />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <!-- Priority -->
                    <div>
                        <x-input-label for="priority" value="{{ __('Prioritas Laporan') }}" />
                        <select id="priority" name="priority"
                            class="border-gray-300 focus:border-bps-blue focus:ring-bps-blue rounded-md shadow-sm w-full mt-1"
                            required>
                            <option value="Rendah">Rendah (Kerusakan minor, masih bisa dipakai)</option>
                            <option value="Sedang" selected>Sedang (Mengganggu pekerjaan)</option>
                            <option value="Tinggi">Tinggi (Mati total, data hilang, urgent)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" value="{{ __('Keterangan Detail') }}" />
                        <textarea id="description" name="description" rows="4"
                            class="border-gray-300 focus:border-bps-blue focus:ring-bps-blue rounded-md shadow-sm w-full mt-1"
                            required placeholder="Jelaskan secara rinci kendala yang dialami..."></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-primary-button
                        class="ms-3 bg-bps-orange hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700">
                        {{ __('Kirim Laporan') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-app-layout>