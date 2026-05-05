<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-bps-blue hover:text-blue-800 transition-colors">
                    &larr; Kembali
                </a>
                <span class="text-gray-400">|</span>
                Detail PC: <span class="text-bps-orange">{{ $report->hostname }}</span>
            </h2>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    @if(!$isOffline)
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-sm font-semibold text-green-600">ONLINE</span>
                    @else
                    <span class="relative flex h-3 w-3">
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <span class="text-sm font-semibold text-red-600">OFFLINE</span>
                    @endif
                </div>

                <div class="h-6 w-px bg-gray-300 hidden sm:block"></div>

                <div x-data="{ showDeleteModal: false, confirmText: '' }">
                    <button @click="showDeleteModal = true" type="button" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white shadow-sm rounded-lg text-sm font-bold transition-all border border-red-100 hover:border-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span class="hidden sm:block">Hapus Device</span>
                    </button>

                    <!-- Professional Modal Confirmation -->
                    <template x-teleport="body">
                        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">
                             
                             <div @click.away="showDeleteModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all"
                                  x-show="showDeleteModal"
                                  x-transition:enter="transition ease-out duration-300"
                                  x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                                  x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                  x-transition:leave="transition ease-in duration-200"
                                  x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                  x-transition:leave-end="opacity-0 translate-y-8 scale-95">
                                  
                                  <div class="flex items-center gap-4 mb-5 border-b border-gray-100 pb-4">
                                      <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                      </div>
                                      <div>
                                          <h3 class="text-xl font-bold text-gray-900">Perhatian: Tindakan Destruktif</h3>
                                          <p class="text-sm text-gray-500 mt-1">Sistem akan menghapus perangkat secara permanen.</p>
                                      </div>
                                  </div>
                    
                                  <div class="bg-red-50 p-4 rounded-xl border border-red-100 mb-6 relative overflow-hidden">
                                      <div class="absolute top-0 right-0 w-16 h-16 bg-red-100 rounded-full -mr-8 -mt-8 opacity-50"></div>
                                      <p class="text-sm text-red-800 leading-relaxed relative z-10">
                                          Tindakan ini tidak dapat dibatalkan. Menghapus <strong>{{ $report->hostname }}</strong> akan ikut melenyapkan seluruh data spesifikasi, status pemakaian RAM/Disk, riwayat <i>online</i>, dan daftar aplikasi yang pernah disinkronisasikan.
                                      </p>
                                  </div>

                                  <div class="mb-6">
                                      <label for="confirm_delete" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-2">
                                          Ketik <span class="px-1.5 py-0.5 bg-gray-100 text-gray-800 border border-gray-200 rounded font-mono select-all">{{ $report->hostname }}</span> untuk mengonfirmasi
                                      </label>
                                      <input type="text" id="confirm_delete" x-model="confirmText" autocomplete="off"
                                             class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white placeholder-gray-300 outline-none transition-all" 
                                             placeholder="Masukkan nama PC persis seperti di atas...">
                                  </div>
                    
                                  <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-6">
                                      <button @click="showDeleteModal = false; confirmText = ''" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-colors text-center">
                                          Batalkan
                                      </button>
                                      <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="inline m-0 p-0 w-full sm:w-auto">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" 
                                                  :disabled="confirmText !== '{{ $report->hostname }}'"
                                                  :class="confirmText === '{{ $report->hostname }}' ? 'bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200 hover:shadow-red-300 cursor-pointer text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'"
                                                  class="w-full sm:w-auto px-5 py-2.5 font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                              Ya, Hapus Permanen
                                          </button>
                                      </form>
                                  </div>
                             </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($report->is_trouble)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <p class="font-bold text-lg">Indikasi Anomali Terdeteksi!</p>
                    <p class="text-sm mt-1">{{ $report->trouble_note }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column (Specs) -->
            <div class="col-span-1 md:col-span-1 space-y-6">
                
                <!-- ID Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-6 bg-bps-blue text-white rounded-t-xl border-b-4 border-bps-orange">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold tracking-wider">{{ $report->hostname }}</h3>
                                <p class="text-blue-100 text-sm mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $report->room_name ?: 'Ruangan Tidak Diketahui' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">IP Address</p>
                            <p class="font-medium text-gray-900 font-mono text-lg">{{ $report->ip_address }}</p>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">MAC Address</p>
                            <p class="font-medium text-gray-900 font-mono text-lg">{{ $report->mac_address }}</p>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Terakhir Aktif</p>
                            <p class="font-medium text-gray-900">{{ $report->last_seen ? $report->last_seen->diffForHumans() : 'Belum pernah' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $report->last_seen ? $report->last_seen->format('d M Y, H:i:s') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Aset BMN Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Data Aset BMN</h3>
                        @if($report->asset)
                            @if($report->asset->status_kondisi == 'Baik')
                                <span class="inline-flex items-center px-2 py-0.5 rounded textxs font-medium bg-green-100 text-green-800">Baik</span>
                            @elseif($report->asset->status_kondisi == 'Rusak Ringan')
                                <span class="inline-flex items-center px-2 py-0.5 rounded textxs font-medium bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded textxs font-medium bg-red-100 text-red-800">Rusak Berat</span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded textxs font-medium bg-gray-100 text-gray-500">Belum Link</span>
                        @endif
                    </div>
                    <div class="p-6">
                        @if($report->asset)
                            <div class="mb-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Nomor BMN</p>
                                <p class="font-bold text-gray-900 text-lg">{{ $report->asset->bmn_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Serial Number (Aset)</p>
                                <p class="font-medium text-gray-800">{{ $report->asset->serial_number ?? '-' }}</p>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <p class="text-sm text-gray-500">Device ini belum ditautkan ke data Aset BMN manapun.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column (Resources & Logs) -->
            <div class="col-span-1 md:col-span-2 space-y-6">
                
                <!-- Hardware Resources -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-bps-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            Hardware & OS Resources
                        </h3>
                        <div class="text-xs bg-blue-50 text-bps-blue px-3 py-1 rounded-full font-medium border border-blue-200 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            {{ str_replace('Microsoft Windows', 'Win', $report->os_name) }} (Build: {{ $report->os_build }})
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-8">
                        
                        <!-- RAM Monitor -->
                        @php
                            $totalRamGb = round(($report->total_ram_kb ?? 0) / 1024 / 1024, 2);
                            $freeRamGb = round(($report->ram_free_kb ?? 0) / 1024 / 1024, 2);
                            $usedRamGb = $totalRamGb > 0 ? $totalRamGb - $freeRamGb : 0;
                            $ramPercent = $totalRamGb > 0 ? round(($usedRamGb / $totalRamGb) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold text-gray-700">Memory (RAM)</span>
                                <span class="text-sm font-bold {{ $ramPercent > 85 ? 'text-red-600' : 'text-bps-blue' }}">{{ $ramPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                                 @if($ramPercent > 85)
                                    <div class="bg-red-500 h-3 rounded-full transition-all duration-500" style="width: {{ $ramPercent }}%"></div>
                                 @elseif($ramPercent > 70)
                                    <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500" style="width: {{ $ramPercent }}%"></div>
                                 @else
                                    <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $ramPercent }}%"></div>
                                 @endif
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 font-medium">
                                <span>Digunakan: {{ $usedRamGb }} GB</span>
                                <span>Total: {{ $totalRamGb }} GB</span>
                            </div>
                        </div>

                        <!-- Storage Monitor -->
                        @php
                            $totalDiskGb = round(($report->total_disk_b ?? 0) / 1024 / 1024 / 1024, 2);
                            $freeDiskGb = round(($report->disk_free_b ?? 0) / 1024 / 1024 / 1024, 2);
                            $usedDiskGb = $totalDiskGb > 0 ? $totalDiskGb - $freeDiskGb : 0;
                            $diskPercent = $totalDiskGb > 0 ? round(($usedDiskGb / $totalDiskGb) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold text-gray-700">Storage (C:)</span>
                                <span class="text-sm font-bold {{ $diskPercent > 90 ? 'text-red-600' : 'text-bps-blue' }}">{{ $diskPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                                 @if($diskPercent > 90)
                                    <div class="bg-red-500 h-3 rounded-full transition-all duration-500" style="width: {{ $diskPercent }}%"></div>
                                 @elseif($diskPercent > 80)
                                    <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500" style="width: {{ $diskPercent }}%"></div>
                                 @else
                                    <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $diskPercent }}%"></div>
                                 @endif
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 font-medium">
                                <span>Sisa: {{ $freeDiskGb }} GB</span>
                                <span>Total: {{ $totalDiskGb }} GB</span>
                            </div>
                        </div>

                    </div>
                </div>

                @if($report->is_trouble)
                <!-- Anomaly Action Box -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border-2 border-red-200">
                    <div class="p-4 border-b border-red-100 bg-red-50 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">Tindakan Lanjutan (Troubleshooting)</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 text-sm mb-4">Sistem mendeteksi adanya kejanggalan pada perangkat ini. Berikut langkah yang disarankan:</p>
                        <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2 mb-6">
                            <li>Periksa status koneksi internet dari ruangan {{ $report->room_name }}.</li>
                            <li>Lakukan remote desktop (RDP) atau hubungi pegawai yang bersangkutan.</li>
                            <li>Apabila ini kesalahan deteksi (False Positive), Anda dapat me-reset status anomali pada komputer target via Agent.</li>
                        </ul>
                        
                    </div>
                </div>
                @endif
                <!-- Software Inventory List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Aplikasi Terinstall</h3>
                        <span class="text-xs bg-bps-blue text-white px-2 py-1 rounded font-bold">{{ $report->installedSoftware->count() }} Aplikasi</span>
                    </div>
                    <div class="p-0">
                        @if($report->installedSoftware->count() > 0)
                        <div class="w-full">
                            <table class="w-full text-left border-collapse">
                                <thead class="whitespace-nowrap">
                                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider sticky top-0 shadow-sm">
                                        <th class="p-3 border-b whitespace-nowrap">Nama Aplikasi</th>
                                        <th class="p-3 border-b whitespace-nowrap">Versi</th>
                                        <th class="p-3 border-b hidden sm:table-cell whitespace-nowrap">Publisher</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    @foreach($report->installedSoftware->sortBy('software_name') as $software)
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="p-3 font-medium text-gray-800">{{ $software->software_name }}</td>
                                        <td class="p-3 text-gray-600 whitespace-nowrap">{{ $software->software_version ?? '-' }}</td>
                                        <td class="p-3 text-gray-500 hidden sm:table-cell whitespace-nowrap">{{ $software->software_publisher ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="p-8 text-center bg-gray-50">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="text-gray-500 font-medium">Belum ada data aplikasi.</p>
                            <p class="text-xs text-gray-400 mt-1">Sistem menunggu sinkronisasi Agent berikutnya.</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
