<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                <div class="bg-gradient-to-r from-bps-blue to-blue-800 px-6 py-6 sm:px-8 text-white relative">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('device-names.index') }}" class="group flex items-center justify-center w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all duration-200">
                            <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform"></i>
                        </a>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Edit Master Nama Perangkat</h2>
                            <p class="text-blue-100/85 text-xs sm:text-sm mt-0.5">Perbarui data merek, kategori, dan informasi dasar perangkat Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <form action="{{ route('device-names.update', $deviceName->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                            <div class="lg:col-span-3 space-y-5">

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                        <span>Merek (Brand)</span>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="tag" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="brand" value="{{ old('brand', $deviceName->brand) }}" required
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm"
                                            placeholder="Contoh: AXIOO, Lenovo, HP...">
                                    </div>
                                    @error('brand')
                                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                        <span>Nama / Model Perangkat</span>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="monitor" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="name" value="{{ old('name', $deviceName->name) }}" required
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm"
                                            placeholder="Contoh: Pongo 725, ThinkPad X1...">
                                    </div>
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                        <span>Tipe (Kategori)</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="layers" class="w-4 h-4"></i>
                                        </div>
                                        <select name="type"
                                            class="w-full pl-9 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm appearance-none bg-white">
                                            <option value="">-- Pilih Tipe --</option>
                                            <option value="Laptop" {{ old('type', $deviceName->type) == 'Laptop' ? 'selected' : '' }}>Laptop / Notebook</option>
                                            <option value="PC Desktop" {{ old('type', $deviceName->type) == 'PC Desktop' ? 'selected' : '' }}>PC Desktop / Komputer Rakitan</option>
                                            <option value="AIO" {{ old('type', $deviceName->type) == 'AIO' ? 'selected' : '' }}>All-in-One (AIO)</option>
                                            <option value="Server" {{ old('type', $deviceName->type) == 'Server' ? 'selected' : '' }}>Server</option>
                                            <option value="Mini PC" {{ old('type', $deviceName->type) == 'Mini PC' ? 'selected' : '' }}>Mini PC</option>
                                            <option value="Lainnya" {{ old('type', $deviceName->type) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                    @error('type')
                                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                            <span>Jumlah Pengadaan (Unit)</span>
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <i data-lucide="database" class="w-4 h-4"></i>
                                            </div>
                                            <input type="number" name="quantity" value="{{ old('quantity', $deviceName->quantity) }}" min="0" required
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm"
                                                placeholder="Contoh: 50">
                                        </div>
                                        @error('quantity')
                                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                            <span>Tanggal Pengadaan</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                            </div>
                                            <input type="date" name="procurement_date" value="{{ old('procurement_date', $deviceName->procurement_date ? $deviceName->procurement_date->format('Y-m-d') : '') }}"
                                                onclick="this.showPicker()"
                                                class="w-full pl-9 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm cursor-pointer">
                                        </div>
                                        <p class="mt-1 text-[11px] text-gray-500">Tanggal kapan perangkat diadakan.</p>
                                        @error('procurement_date')
                                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-2 space-y-5">

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                        <span>Gambar Perangkat</span>
                                        <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                                    </label>

                                    <div class="relative group border-2 border-dashed border-gray-300 hover:border-bps-blue hover:bg-blue-50/10 rounded-xl transition-all duration-300 cursor-pointer overflow-hidden min-h-[190px] flex flex-col justify-center items-center p-5">
                                        <input type="file" name="image" id="image-input" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-20">

                                        <div id="upload-placeholder" class="flex flex-col items-center justify-center text-center space-y-2 pointer-events-none {{ $deviceName->image ? 'hidden' : '' }}">
                                            <div class="w-12 h-12 rounded-full bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center text-bps-blue transition-colors duration-300">
                                                <i data-lucide="image" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-semibold text-gray-700 block">Pilih File Gambar</span>
                                                <span class="text-xs text-gray-500">atau tarik & lepas di sini</span>
                                            </div>
                                            <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-500">JPG, PNG, WEBP (Maks: 2MB)</span>
                                        </div>

                                        <div id="image-preview-container" class="absolute inset-0 bg-white rounded-xl flex flex-col items-center justify-center p-3 z-10 {{ $deviceName->image ? '' : 'hidden' }}">
                                            <img id="image-preview" src="{{ $deviceName->image ? Storage::url($deviceName->image) : '#' }}" alt="Preview" class="max-w-full max-h-[130px] object-contain rounded-lg shadow-sm">
                                            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500 w-full justify-center">
                                                <span id="image-filename" class="truncate max-w-[150px] font-medium text-gray-600">
                                                    {{ $deviceName->image ? 'Gambar_Saat_Ini.' . pathinfo($deviceName->image, PATHINFO_EXTENSION) : 'file_name.png' }}
                                                </span>
                                                <button type="button" id="remove-image-btn" class="p-1 hover:bg-red-50 text-red-500 rounded-full transition-colors z-30" title="Batal Pilih Gambar">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-[11px] text-gray-500">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                                    @error('image')
                                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                        <span>Deskripsi Tambahan</span>
                                        <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute top-3 left-3 text-gray-400 pointer-events-none">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                        </div>
                                        <textarea name="description" rows="4"
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm resize-none"
                                            placeholder="Spesifikasi singkat / catatan mengenai perangkat...">{{ old('description', $deviceName->description) }}</textarea>
                                    </div>
                                    @error('description')
                                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                            <a href="{{ route('device-names.index') }}"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all duration-200 flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-bps-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image-input');
            const uploadPlaceholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('image-preview-container');
            const previewImg = document.getElementById('image-preview');
            const filenameSpan = document.getElementById('image-filename');
            const removeBtn = document.getElementById('remove-image-btn');

            const originalSrc = "{{ $deviceName->image ? Storage::url($deviceName->image) : '' }}";
            const originalFilename = "{{ $deviceName->image ? 'Gambar_Saat_Ini.' . pathinfo($deviceName->image, PATHINFO_EXTENSION) : '' }}";

            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            previewImg.src = event.target.result;
                            filenameSpan.textContent = file.name;

                            uploadPlaceholder.classList.add('hidden');
                            previewContainer.classList.remove('hidden');

                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });

                const dropZone = imageInput.closest('.relative.group');
                if (dropZone) {
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropZone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropZone.classList.add('border-bps-blue', 'bg-blue-50/20');
                        }, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropZone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropZone.classList.remove('border-bps-blue', 'bg-blue-50/20');
                        }, false);
                    });
                }
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();

                    imageInput.value = '';

                    if (originalSrc) {

                        previewImg.src = originalSrc;
                        filenameSpan.textContent = originalFilename;
                        previewContainer.classList.remove('hidden');
                        uploadPlaceholder.classList.add('hidden');
                    } else {

                        previewContainer.classList.add('hidden');
                        uploadPlaceholder.classList.remove('hidden');
                        previewImg.src = '#';
                        filenameSpan.textContent = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>
