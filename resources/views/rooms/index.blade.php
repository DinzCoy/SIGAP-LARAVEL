<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Fira+Sans:wght@300;400;500;600;700&display=swap');
        
        .font-fira { font-family: 'Fira Sans', sans-serif; }
        .font-mono-fira { font-family: 'Fira Code', monospace; }
        
        .premium-gradient {
            background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 50%, #111827 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>

    <div class="space-y-6 max-w-[1400px] mx-auto font-fira">
            
            {{-- Header Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-8">
                <div class="premium-gradient px-6 py-10 sm:px-10 text-white relative overflow-hidden">
                    {{-- Decorative background elements --}}
                    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-bps-orange/10 rounded-full -ml-32 -mb-32 blur-2xl"></div>
                    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M0 0h20L0 20z\' fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
                    
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 relative z-10">
                        <div class="flex items-center gap-5">
                            <div class="p-4 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-4xl font-black tracking-tight leading-none mb-2">
                                    Master Ruangan
                                </h2>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-2 w-2 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                    <p class="text-blue-100 text-sm font-medium tracking-wide">Infrastruktur BPulSe Operational</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4">
                            {{-- Quick Stats --}}
                            <div class="flex gap-4 mr-4 border-r border-white/10 pr-8 hidden sm:flex">
                                <div class="text-center">
                                    <div class="text-2xl font-black font-mono-fira leading-none">{{ $rooms->count() }}</div>
                                    <div class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mt-1">Ruangan</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-black font-mono-fira leading-none text-bps-orange">{{ $rooms->sum('assets_count') }}</div>
                                    <div class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mt-1">Total Unit</div>
                                </div>
                            </div>

                            @if(in_array(session('active_role_id'), [1, 2, 4]))
                            <a href="{{ route('rooms.create') }}" class="group flex items-center px-6 py-3.5 bg-white text-bps-blue font-black text-sm rounded-xl shadow-2xl hover:shadow-white/10 hover:-translate-y-1 transition-all duration-300 active:scale-95">
                                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Ruangan Baru
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alert Section --}}
            @if(session('success'))
            <div class="animate-in fade-in slide-in-from-top duration-500 mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center gap-3">
                <div class="p-1.5 bg-emerald-100 rounded-full">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Table Section --}}
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 transition-all duration-500 hover:shadow-blue-500/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/50 backdrop-blur-xl">
                                <th scope="col" class="px-8 py-6 text-left text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Identity</th>
                                <th scope="col" class="px-8 py-6 text-left text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Custodian / PIC</th>
                                <th scope="col" class="px-8 py-6 text-center text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Inventory</th>
                                <th scope="col" class="px-8 py-6 text-left text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Asset Integrity</th>
                                @if(in_array(session('active_role_id'), [1, 2, 4]))
                                <th scope="col" class="px-8 py-6 text-right text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Command</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50/50">
                            @forelse($rooms as $room)
                                <tr class="hover:bg-blue-50/30 transition-all duration-300 group cursor-pointer relative border-l-4 border-transparent hover:border-bps-blue" data-url="{{ route('assets.index', ['room' => $room->slug]) }}" onclick="if (!event.target.closest('button, a, form')) { window.location.href = this.dataset.url; }">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center gap-5">
                                            <div class="relative">
                                                <div class="p-3.5 bg-blue-50 text-bps-blue rounded-2xl group-hover:bg-bps-blue group-hover:text-white transition-all duration-500 shadow-sm group-hover:shadow-blue-500/20 group-hover:-rotate-3">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-lg font-black text-slate-800 group-hover:text-bps-blue transition-colors flex items-center gap-2">
                                                    {{ $room->name }}
                                                </div>
                                                <div class="text-[11px] font-mono-fira text-slate-400 mt-1 flex items-center gap-2 uppercase tracking-tight">
                                                    <span class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-500">{{ $room->slug }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if($room->pic)
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-bps-orange/20 to-orange-100 flex items-center justify-center border border-bps-orange/20 shadow-sm overflow-hidden">
                                                        <span class="text-bps-orange font-black text-base">{{ strtoupper(substr($room->pic?->name, 0, 1)) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-slate-700 tracking-tight">{{ $room->pic?->name }}</span>
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Primary PIC</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 text-slate-300">
                                                <div class="h-10 w-10 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                </div>
                                                <span class="text-[11px] font-bold uppercase italic tracking-widest">Unassigned</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-center">
                                        <div class="inline-flex flex-col items-center px-4 py-2 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                                            <span class="text-2xl font-black font-mono-fira text-bps-blue leading-none">{{ $room->assets_count }}</span>
                                            <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest mt-1.5">Assets</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="w-56">
                                            <div class="flex justify-between items-end mb-2.5">
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em]">Integrity Level</span>
                                                    <span class="text-xs font-black text-emerald-600 mt-0.5">
                                                        {{ $room->assets_count > 0 ? round(($room->baik_count / $room->assets_count) * 100) : 0 }}% Healthy
                                                    </span>
                                                </div>
                                                <div class="flex gap-1.5 pb-0.5">
                                                    <div class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[9px] font-black border border-emerald-100">{{ $room->baik_count }}</div>
                                                    <div class="px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded text-[9px] font-black border border-amber-100">{{ $room->rusak_ringan_count }}</div>
                                                    <div class="px-1.5 py-0.5 bg-rose-50 text-rose-600 rounded text-[9px] font-black border border-rose-100">{{ $room->rusak_berat_count }}</div>
                                                </div>
                                            </div>
                                            @php
                                                $baikWidth = $room->assets_count > 0 ? ($room->baik_count / $room->assets_count) * 100 : 0;
                                                $ringanWidth = $room->assets_count > 0 ? ($room->rusak_ringan_count / $room->assets_count) * 100 : 0;
                                                $beratWidth = $room->assets_count > 0 ? ($room->rusak_berat_count / $room->assets_count) * 100 : 0;
                                            @endphp
                                            <div class="flex h-3 w-full bg-slate-100 rounded-full overflow-hidden shadow-inner p-[2px] border border-slate-200/50">
                                                @if($room->assets_count > 0)
                                                    <div @style(['width' => $baikWidth . '%', 'height' => '100%', 'background-color' => '#10b981']) title="Baik: {{ $room->baik_count }}"></div>
                                                    <div @style(['width' => $ringanWidth . '%', 'height' => '100%', 'background-color' => '#f59e0b', 'margin-left' => '1px', 'margin-right' => '1px']) title="Rusak Ringan: {{ $room->rusak_ringan_count }}"></div>
                                                    <div @style(['width' => $beratWidth . '%', 'height' => '100%', 'background-color' => '#ef4444']) title="Rusak Berat: {{ $room->rusak_berat_count }}"></div>
                                                @else
                                                    <div class="w-full bg-slate-200 rounded-full"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @if(in_array(session('active_role_id'), [1, 2, 4]))
                                    <td class="px-8 py-6 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2.5">
                                            <a href="{{ route('rooms.edit', $room->id) }}" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-bps-blue hover:text-white rounded-xl transition-all duration-300 shadow-sm border border-slate-100" title="Modify Node">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form id="delete-form-{{ $room->id }}" action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('delete-form-{{ $room->id }}', 'Decommission room {{ $room->name }}?')" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-rose-600 hover:text-white rounded-xl transition-all duration-300 shadow-sm border border-slate-100" title="Delete Node">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-slate-50 rounded-3xl flex items-center justify-center mb-6 border border-slate-100 shadow-inner">
                                                <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <h3 class="text-2xl font-black text-slate-900">Zero Nodes Detected</h3>
                                            <p class="text-slate-400 text-sm mt-2 max-w-sm mx-auto font-medium">The infrastructure map is currently empty. Please initialize a new room node to begin asset tracking.</p>
                                            <a href="{{ route('rooms.create') }}" class="mt-8 inline-flex items-center px-8 py-4 bg-bps-blue text-white font-black text-sm rounded-2xl shadow-xl shadow-blue-500/20 hover:bg-blue-800 hover:-translate-y-1 transition-all active:scale-95">
                                                Initialize First Room
                                            </a>
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
