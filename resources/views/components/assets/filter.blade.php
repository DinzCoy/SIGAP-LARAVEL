<form id="filterForm" method="GET" action="{{ route('assets.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-100">
    @if(request('device_name_id'))
        <input type="hidden" name="device_name_id" value="{{ request('device_name_id') }}">
    @endif
    <div class="flex-1">
        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Pencarian</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari BMN, SN, atau Merek..." class="block w-full py-2 px-3 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
    </div>
    <div class="w-full md:w-auto">
        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status PC Guardian</label>
        <select name="filter_linked" class="block w-full py-2 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
            <option value="all" {{ request('filter_linked') == 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="yes" {{ request('filter_linked') == 'yes' ? 'selected' : '' }}>Sudah Terhubung</option>
            <option value="no" {{ request('filter_linked') == 'no' ? 'selected' : '' }}>Belum Terhubung</option>
        </select>
    </div>
    <div class="w-full md:w-auto">
        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status BMN</label>
        <select name="filter_bmn" class="block w-full py-2 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
            <option value="all" {{ request('filter_bmn') == 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="yes" {{ request('filter_bmn') == 'yes' ? 'selected' : '' }}>Memiliki BMN</option>
            <option value="no" {{ request('filter_bmn') == 'no' ? 'selected' : '' }}>Tanpa BMN</option>
        </select>
    </div>

    <div class="w-full md:w-auto">
        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-bps-blue text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-blue-800 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Terapkan Filter
        </button>
    </div>
</form>
