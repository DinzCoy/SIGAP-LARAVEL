<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-5">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <a href="{{ route('device-names.index') }}" class="text-gray-400 hover:text-bps-blue transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                        Edit Master Nama Perangkat
                    </h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('device-names.update', $deviceName->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Merek (Brand) <span class="text-red-500">*</span></label>
                            <input type="text" name="brand" value="{{ old('brand', $deviceName->brand) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama / Model Perangkat <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $deviceName->name) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe (Kategori)</label>
                            <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Laptop" {{ old('type', $deviceName->type) == 'Laptop' ? 'selected' : '' }}>Laptop / Notebook</option>
                                <option value="PC Desktop" {{ old('type', $deviceName->type) == 'PC Desktop' ? 'selected' : '' }}>PC Desktop / Komputer Rakitan</option>
                                <option value="AIO" {{ old('type', $deviceName->type) == 'AIO' ? 'selected' : '' }}>All-in-One (AIO)</option>
                                <option value="Server" {{ old('type', $deviceName->type) == 'Server' ? 'selected' : '' }}>Server</option>
                                <option value="Mini PC" {{ old('type', $deviceName->type) == 'Mini PC' ? 'selected' : '' }}>Mini PC</option>
                                <option value="Lainnya" {{ old('type', $deviceName->type) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Pengadaan (Unit/Stok) <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', $deviceName->quantity) }}" min="0" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pengadaan / Alokasi</label>
                            <input type="date" name="procurement_date" value="{{ old('procurement_date', $deviceName->procurement_date ? $deviceName->procurement_date->format('Y-m-d') : '') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Tanggal kapan perangkat ini diadakan/dialokasikan.</p>
                            @error('procurement_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Tambahan (Opsional)</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm">{{ old('description', $deviceName->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar Perangkat</label>
                            
                            @if($deviceName->image)
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Gambar Saat Ini:</p>
                                    <img src="{{ Storage::url($deviceName->image) }}" alt="{{ $deviceName->brand }}" class="h-32 object-contain rounded-lg border border-gray-200 shadow-sm p-2 bg-gray-50">
                                </div>
                            @endif

                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-bps-blue hover:file:bg-blue-100 border border-gray-300 rounded-lg shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="px-5 py-2.5 bg-bps-blue text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
