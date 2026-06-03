<x-app-layout>
    <div class="relative py-8 min-h-[calc(100vh-80px)]">

        <div class="absolute top-0 left-1/4 w-[30rem] h-[30rem] bg-blue-400/20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[30rem] h-[30rem] bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-2xl rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-white/60 overflow-hidden">

                <div class="px-8 sm:px-12 pt-10 pb-8 border-b border-gray-100/60 bg-white/40">
                    <div class="flex items-start sm:items-center gap-6">
                        <a href="{{ route('device-names.index') }}" class="group flex shrink-0 items-center justify-center w-14 h-14 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md hover:border-bps-blue/30 text-gray-400 hover:text-bps-blue transition-all duration-300">
                            <i data-lucide="arrow-left" class="w-6 h-6 group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="px-3 py-1 rounded-lg bg-blue-50 text-bps-blue text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">Master Data</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                <span class="text-xs font-bold text-gray-400">Inventory Setup</span>
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-black text-gray-800 tracking-tight">Tambah Perangkat</h2>
                            <p class="text-gray-500 text-sm mt-1.5 font-medium leading-relaxed">Registrasi identitas, kategori, dan spesifikasi perangkat baru ke dalam katalog sistem.</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 sm:p-12">
                    <form action="{{ route('device-names.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-12 gap-12">
                        @csrf

                        <div class="xl:col-span-7 space-y-8">

                            <div class="space-y-2.5 group">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                    Merek (Brand) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-bps-blue transition-colors duration-300">
                                        <i data-lucide="tag" class="w-5 h-5"></i>
                                    </div>
                                    <input type="text" name="brand" value="{{ old('brand') }}" required
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50/50 hover:bg-gray-100/50 focus:bg-white border-2 border-gray-100 focus:border-bps-blue rounded-2xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-bps-blue/10 transition-all duration-300 outline-none shadow-sm"
                                        placeholder="Contoh: AXIOO, Lenovo, HP...">
                                </div>
                                @error('brand')
                                    <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2.5 group">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                    Nama / Model Perangkat <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-bps-blue transition-colors duration-300">
                                        <i data-lucide="monitor" class="w-5 h-5"></i>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50/50 hover:bg-gray-100/50 focus:bg-white border-2 border-gray-100 focus:border-bps-blue rounded-2xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-bps-blue/10 transition-all duration-300 outline-none shadow-sm"
                                        placeholder="Contoh: Pongo 725, ThinkPad X1...">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2.5 group">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                    Tipe (Kategori)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-bps-blue transition-colors duration-300">
                                        <i data-lucide="layers" class="w-5 h-5"></i>
                                    </div>
                                    <select name="type"
                                        class="w-full pl-12 pr-12 py-4 bg-gray-50/50 hover:bg-gray-100/50 focus:bg-white border-2 border-gray-100 focus:border-bps-blue rounded-2xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-bps-blue/10 transition-all duration-300 outline-none shadow-sm appearance-none cursor-pointer">
                                        <option value="">-- Pilih Tipe Perangkat --</option>
                                        <option value="Laptop" {{ old('type') == 'Laptop' ? 'selected' : '' }}>Laptop / Notebook</option>
                                        <option value="PC Desktop" {{ old('type') == 'PC Desktop' ? 'selected' : '' }}>PC Desktop / Komputer Rakitan</option>
                                        <option value="AIO" {{ old('type') == 'AIO' ? 'selected' : '' }}>All-in-One (AIO)</option>
                                        <option value="Server" {{ old('type') == 'Server' ? 'selected' : '' }}>Server</option>
                                        <option value="Mini PC" {{ old('type') == 'Mini PC' ? 'selected' : '' }}>Mini PC</option>
                                        <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                        <i data-lucide="chevron-down" class="w-5 h-5"></i>
                                    </div>
                                </div>
                                @error('type')
                                    <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">

                                <div class="space-y-2.5 group">
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                        Jumlah Unit <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-bps-blue transition-colors duration-300">
                                            <i data-lucide="database" class="w-5 h-5"></i>
                                        </div>
                                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}" min="0" required
                                            class="w-full pl-12 pr-4 py-4 bg-gray-50/50 hover:bg-gray-100/50 focus:bg-white border-2 border-gray-100 focus:border-bps-blue rounded-2xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-bps-blue/10 transition-all duration-300 outline-none shadow-sm"
                                            placeholder="Contoh: 50">
                                    </div>
                                    @error('quantity')
                                        <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2.5 group">
                                    <label class="flex items-center justify-between text-sm font-bold text-gray-700">
                                        <span>Tgl Pengadaan</span>
                                        <span class="text-[9px] font-black uppercase text-gray-400 tracking-wider bg-gray-100 px-2 py-0.5 rounded-md">Opsional</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-bps-blue transition-colors duration-300">
                                            <i data-lucide="calendar" class="w-5 h-5"></i>
                                        </div>
                                        <input type="date" name="procurement_date" value="{{ old('procurement_date') }}"
                                            onclick="this.showPicker()"
                                            class="w-full pl-12 pr-4 py-4 bg-gray-50/50 hover:bg-gray-100/50 focus:bg-white border-2 border-gray-100 focus:border-bps-blue rounded-2xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-bps-blue/10 transition-all duration-300 outline-none shadow-sm cursor-pointer">
                                    </div>
                                    @error('procurement_date')
                                        <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-2.5 group">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                    Deskripsi Tambahan <span class="text-[9px] font-black uppercase text-gray-400 tracking-wider bg-gray-100 px-2 py-0.5 rounded-md">Opsional</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute top-4 left-0 pl-4 pointer-events-none text-gray-400 group-focus-within:text-bps-blue transition-colors duration-300">
                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                    </div>
                                    <textarea name="description" rows="4"
                                        class="w-full pl-12 pr-4 py-4 bg-gray-50/50 hover:bg-gray-100/50 focus:bg-white border-2 border-gray-100 focus:border-bps-blue rounded-2xl text-sm font-medium text-gray-800 focus:ring-4 focus:ring-bps-blue/10 transition-all duration-300 outline-none shadow-sm resize-none"
                                        placeholder="Spesifikasi singkat, catatan, atau detail tambahan mengenai perangkat...">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="xl:col-span-5 relative">
                            <div class="sticky top-28 space-y-2.5">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                    Foto Perangkat <span class="text-[9px] font-black uppercase text-gray-400 tracking-wider bg-gray-100 px-2 py-0.5 rounded-md">Opsional</span>
                                </label>

                                <div class="relative group/upload mt-2">

                                    <input type="file" name="image" id="image-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">

                                    <div class="border-2 border-dashed border-gray-200 group-hover/upload:border-bps-blue/50 bg-gray-50/30 group-hover/upload:bg-blue-50/30 rounded-[2rem] transition-all duration-500 p-8 flex flex-col items-center justify-center min-h-[380px] text-center shadow-inner group-hover/upload:shadow-md relative overflow-hidden">

                                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100/50 to-transparent rounded-bl-[100px] pointer-events-none transition-opacity opacity-0 group-hover/upload:opacity-100"></div>
                                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-indigo-100/50 to-transparent rounded-tr-[80px] pointer-events-none transition-opacity opacity-0 group-hover/upload:opacity-100"></div>

                                        <div id="upload-placeholder" class="flex flex-col items-center space-y-5 pointer-events-none transition-all duration-500 transform group-hover/upload:-translate-y-2">
                                            <div class="relative">
                                                <div class="absolute inset-0 bg-blue-100 rounded-full blur-md opacity-0 group-hover/upload:opacity-100 transition-opacity duration-500"></div>
                                                <div class="w-24 h-24 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-300 group-hover/upload:text-bps-blue group-hover/upload:scale-105 transition-all duration-500 relative z-10">
                                                    <i data-lucide="image-plus" class="w-10 h-10"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black text-gray-800 tracking-tight">Tarik & Lepas Foto</h4>
                                                <p class="text-sm font-semibold text-gray-400 mt-1">atau klik untuk menelusuri komputer</p>
                                            </div>
                                            <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 pt-3">
                                                <span class="px-2.5 py-1 bg-white rounded-md border border-gray-100 shadow-sm">PNG</span>
                                                <span class="px-2.5 py-1 bg-white rounded-md border border-gray-100 shadow-sm">JPG</span>
                                                <span class="px-2.5 py-1 bg-white rounded-md border border-gray-100 shadow-sm">WEBP</span>
                                            </div>
                                            <p class="text-[10px] font-medium text-gray-400 mt-2">Maksimal ukuran file: 2MB</p>
                                        </div>

                                        <div id="image-preview-container" class="hidden absolute inset-3 bg-white rounded-[1.5rem] shadow-xl flex flex-col items-center p-3 z-30 border border-gray-100">
                                            <div class="relative w-full flex-1 flex items-center justify-center bg-gray-50/50 rounded-[1rem] overflow-hidden mb-3 border border-gray-100/50 group/img">
                                                <img id="image-preview" src="#" alt="Preview" class="max-w-full max-h-[250px] object-contain drop-shadow-md transition-transform duration-700 group-hover/img:scale-105">
                                            </div>
                                            <div class="flex items-center justify-between w-full px-3 py-1">
                                                <div class="flex items-center gap-2.5 overflow-hidden">
                                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                                                        <i data-lucide="image" class="w-4 h-4 text-bps-blue"></i>
                                                    </div>
                                                    <span id="image-filename" class="text-xs font-bold text-gray-700 truncate max-w-[150px]"></span>
                                                </div>
                                                <button type="button" id="remove-image-btn" class="flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 shadow-sm" title="Hapus Gambar">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="xl:col-span-12 mt-4 pt-8 border-t border-gray-100/60 flex flex-col-reverse sm:flex-row items-center justify-end gap-4">
                            <a href="{{ route('device-names.index') }}"
                                class="w-full sm:w-auto px-8 py-3.5 border-2 border-gray-200 text-gray-600 font-bold text-sm rounded-2xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-100 transition-all duration-300 flex items-center justify-center gap-2">
                                <i data-lucide="x" class="w-5 h-5"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-bps-blue to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold text-sm rounded-2xl shadow-xl shadow-blue-900/20 hover:shadow-blue-900/40 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                Simpan Perangkat
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

            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            previewImg.src = event.target.result;
                            filenameSpan.textContent = file.name;
                            previewContainer.classList.remove('hidden');

                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });

                const dropZone = imageInput.closest('.group\\/upload');
                if (dropZone) {
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropZone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropZone.classList.add('border-bps-blue', 'bg-blue-50/40');
                        }, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropZone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropZone.classList.remove('border-bps-blue', 'bg-blue-50/40');
                        }, false);
                    });
                }
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    imageInput.value = '';
                    previewContainer.classList.add('hidden');
                    previewImg.src = '#';
                    filenameSpan.textContent = '';
                });
            }
        });
    </script>
</x-app-layout>
