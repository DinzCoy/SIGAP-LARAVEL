<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ketua Tim') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6">

        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-violet-700 to-purple-800 rounded-2xl shadow-xl mb-8 p-8 flex flex-col md:flex-row items-center justify-between text-white">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-2">Pusat Komando Tim</h1>
                <p class="text-violet-100 max-w-2xl text-sm md:text-base leading-relaxed">
                    Kelola dan distribusikan tiket perbaikan ke anggota teknisi tim Anda. Pantau progress dan pastikan setiap laporan ditangani dengan tepat.
                </p>
            </div>
            <div class="mt-6 md:mt-0 flex shrink-0">
                <a href="{{ route('tickets.index') }}"
                    class="bg-white/10 hover:bg-white/20 border border-white/30 text-white font-semibold py-2.5 px-6 rounded-xl transition duration-200">
                    Beranda Tiket
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Pending Assignment -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menunggu Penugasan</p>
                            <p class="text-4xl font-extrabold text-amber-500">{{ $pendingAssignment }} <span class="text-lg font-medium text-gray-400">tiket</span></p>
                        </div>
                        <div class="bg-amber-50 text-amber-500 p-4 rounded-2xl">
                            <x-lucide-clock class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-orange-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>

            <!-- In Progress by Team -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sedang Dikerjakan Tim</p>
                            <p class="text-4xl font-extrabold text-blue-600">{{ $inProgressByTeam }} <span class="text-lg font-medium text-gray-400">tiket</span></p>
                        </div>
                        <div class="bg-blue-50 text-blue-500 p-4 rounded-2xl">
                            <x-lucide-wrench class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-600 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>

            <!-- Completed by Team -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="p-6 relative z-10 transition-transform duration-300 group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Diselesaikan Tim</p>
                            <p class="text-4xl font-extrabold text-emerald-500">{{ $completedByTeam }} <span class="text-lg font-medium text-gray-400">tiket</span></p>
                        </div>
                        <div class="bg-emerald-50 text-emerald-500 p-4 rounded-2xl">
                            <x-lucide-check-circle class="w-8 h-8" />
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-green-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            </div>
        </div>

        <!-- Recent Tickets Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <x-lucide-list-checks class="w-5 h-5 text-bps-orange" /> Tiket yang Perlu Ditugaskan & Dipantau
                </h3>
                <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">Semua Tiket &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="whitespace-nowrap">
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Judul Tiket</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Pelapor</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Teknisi</th>
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-right font-semibold whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($ticket->title, 40) }}</div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <x-lucide-calendar class="w-3 h-3" /> {{ $ticket->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">{{ $ticket->reporter?->name ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->technician)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <x-lucide-user class="w-3 h-3" /> {{ $ticket->technician?->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-200">
                                        <x-lucide-alert-circle class="w-3 h-3" /> Belum ditugaskan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'Menunggu Pengecekan Pengelola' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'Diteruskan ke Ketua Tim' => 'bg-violet-50 text-violet-700 border-violet-200',
                                        'Diteruskan ke Teknisi' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'In Progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Menunggu Persetujuan Biaya' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'Approved' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $badgeClass = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Quick Assign Button for unassigned tickets --}}
                                    @if($ticket->status === 'Diteruskan ke Ketua Tim' && !$ticket->technician_id)
                                        <form action="{{ route('tickets.updateStatus', $ticket->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Diteruskan ke Teknisi">
                                            <select name="technician_id" required
                                                class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-violet-500 focus:border-violet-500 bg-white">
                                                <option value="">Pilih Teknisi</option>
                                                @foreach($technicians as $tech)
                                                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                                <x-lucide-send class="w-3 h-3" /> Tugaskan
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <x-lucide-arrow-right class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center">
                                    <x-lucide-inbox class="w-12 h-12 text-gray-300 mb-3" />
                                    <p>Belum ada tiket yang perlu ditugaskan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
