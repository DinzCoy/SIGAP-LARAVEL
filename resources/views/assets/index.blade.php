<x-app-layout>
    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                
                {{-- Header Section --}}
                <div class="bg-gradient-to-r from-bps-blue to-blue-700 px-6 py-6 sm:px-8 text-white">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold leading-tight flex items-center gap-2">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                {{ $currentDeviceNameId ? 'Daftar Aset (Difilter)' : 'Daftar Seluruh Aset BMN' }}
                            </h2>
                            <p class="text-blue-100 text-sm mt-1">
                                @if(in_array(session('active_role_id'), [2, 4]))
                                    Kelola data inventaris PC, laptop, dan perangkat jaringan (Mode Edit)
                                @else
                                    Lihat data inventaris PC, laptop, dan perangkat jaringan (Mode Lihat)
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('device-names.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-bps-blue font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Kembali ke Master Perangkat
                            </a>
                        </div>
                    </div>
                </div>



                {{-- Table Section --}}
                <div class="p-6">
                    {{-- Filter Component --}}
                    <x-assets.filter />

                    {{-- Table Component --}}
                    <x-assets.table :assets="$assets" />
                </div>
                </div>
        </div>
    </div>

    @include('assets.partials.link-modal')
    @include('assets.partials.edit-modal')

</x-app-layout>
