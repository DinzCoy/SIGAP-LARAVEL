@props(['assets'])

<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">
        Menampilkan 
        <span class="font-bold text-gray-700">{{ $assets?->firstItem() ?? 0 }}</span>
        –
        <span class="font-bold text-gray-700">{{ $assets?->lastItem() ?? 0 }}</span>
        dari
        <span class="font-bold text-bps-blue">{{ $assets?->total() ?? 0 }}</span> total aset
    </p>
</div>

<div class="border rounded-xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto w-full">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 whitespace-nowrap">
            <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">No. BMN</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Serial Number</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Ruangan</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Pengguna</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kondisi</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status Linking (PC)</th>
                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($assets as $asset)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $asset?->bmn_number ?? '-' }}</div>
                        <div class="text-xs text-blue-600 mt-1">{{ $asset?->deviceName ? ($asset->deviceName->brand ?? '') . ' ' . ($asset->deviceName->name ?? '') : ($asset?->brand ?? '-') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">{{ $asset->serial_number ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">{{ $asset->room?->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($asset->user)
                            <div class="text-sm font-medium text-gray-900">{{ $asset->user?->name ?? 'Unknown' }}</div>
                            @if($asset->allocated_at)
                                <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $asset?->allocated_at?->translatedFormat('d M Y') ?? '-' }}
                                </div>
                            @endif
                        @else
                            <span class="text-sm text-gray-400 italic">Belum dialokasikan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($asset->status_kondisi == 'Baik')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Baik</span>
                        @elseif($asset->status_kondisi == 'Rusak Ringan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rusak Berat</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($asset->pcReport)
                            <div class="inline-flex items-center text-sm font-medium text-blue-600">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                {{ $asset->pcReport?->hostname ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">{{ $asset->mac_address }}</div>
                        @else
                            @if(session('active_role_id') == \App\Models\User::ROLE_ADMIN)
                                <button onclick="openLinkModal({{ $asset?->id ?? 0 }}, '{{ $asset?->bmn_number ?? '-' }}')" class="inline-flex items-center px-2.5 py-1.5 border border-dashed border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    Link Device
                                </button>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum terhubung</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if(in_array(session('active_role_id'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_PENGELOLA_ASET]))
                                <a href="{{ route('assets.print', $asset->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-md transition-colors" title="Print QR Sticker">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <button type="button" data-asset="{{ json_encode($asset) }}" onclick="openEditModal(this)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form id="delete-form-{{ $asset?->id ?? '' }}" action="{{ $asset?->id ? route('asset.destroy', $asset->id) : '#' }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $asset?->id ?? '' }}', 'Apakah Anda yakin ingin menghapus aset BMN {{ $asset?->bmn_number ?? '-' }}?')" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Read Only</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center whitespace-nowrap">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="mt-4 text-sm text-gray-500">Belum ada data Aset BMN yang terdaftar.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>
</div>

{{-- Pagination & Per Page Control --}}
<div class="mt-6 mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-lg border border-gray-100">
        <label for="per_page_bottom" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tampilkan:</label>
        <select name="per_page" id="per_page_bottom" form="filterForm" onchange="this.form.submit()" class="block py-1.5 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm bg-white">
            <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 baris</option>
            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 baris</option>
            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
        </select>
    </div>

    @if($assets->hasPages())
        <div class="w-full md:w-auto">
            {{ $assets->appends(request()->query())->links('vendor.pagination.modern') }}
        </div>
    @endif
</div>
