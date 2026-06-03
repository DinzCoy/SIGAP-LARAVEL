<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                <div class="bg-gradient-to-r from-bps-blue to-blue-800 px-6 py-6 sm:px-8 text-white relative">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('rooms.index') }}" class="group flex items-center justify-center w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all duration-200">
                            <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform"></i>
                        </a>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Tambah Ruangan Baru</h2>
                            <p class="text-blue-100/85 text-xs sm:text-sm mt-0.5">Daftarkan data nama ruangan, penanggung jawab, dan rincian lokasinya.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <form action="{{ route('rooms.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                <span>Nama Ruangan</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="home" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm"
                                    placeholder="Contoh: Ruang IT, Ruang Rapat, Aula...">
                            </div>
                            @error('name')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pic_id" class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                <span>Penanggung Jawab (PIC)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </div>
                                <select name="pic_id" id="pic_id"
                                    class="w-full pl-9 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm appearance-none bg-white">
                                    <option value="">-- Tidak ada PIC --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('pic_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                            @error('pic_id')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                <span>Deskripsi / Keterangan</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 text-gray-400 pointer-events-none">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </div>
                                <textarea name="description" id="description" rows="4"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm resize-none"
                                    placeholder="Keterangan singkat mengenai ruangan (opsional)...">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <a href="{{ route('rooms.index') }}"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all duration-200 flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-bps-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Simpan Ruangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
