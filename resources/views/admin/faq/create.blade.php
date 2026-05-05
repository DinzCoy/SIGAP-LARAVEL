<x-app-layout>
    <div class="space-y-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.faq.articles.index') }}" class="inline-flex items-center text-sm font-medium text-bps-blue hover:text-blue-800 transition-colors mb-2">
                    <x-lucide-arrow-left class="w-4 h-4 mr-1" />
                    Kembali ke Daftar Artikel
                </a>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-file-plus class="w-6 h-6 text-bps-orange" />
                    Tulis Artikel Baru
                </h1>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form action="{{ route('admin.faq.articles.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="question" class="block text-sm font-medium text-gray-700">Pertanyaan / Topik <span class="text-red-500">*</span></label>
                        <input type="text" name="question" id="question" value="{{ old('question') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bps-blue focus:border-bps-blue sm:text-sm" placeholder="Cara memperbaiki masalah koneksi jaringan">
                        @error('question') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="faq_category_id" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                            <select name="faq_category_id" id="faq_category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('faq_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('faq_category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="is_published" class="block text-sm font-medium text-gray-700">Status Publikasi</label>
                            <select name="is_published" id="is_published" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bps-blue focus:border-bps-blue sm:text-sm">
                                <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>Publikasikan (Live)</option>
                                <option value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                            </select>
                            @error('is_published') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">Jawaban / Isi Artikel <span class="text-red-500">*</span></label>
                        <textarea name="answer" id="answer" rows="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-bps-blue focus:border-bps-blue sm:text-sm">{!! old('answer') !!}</textarea>
                        @error('answer') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        <style>
                            .ck-editor__editable_inline {
                                min-height: 300px;
                            }
                        </style>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.faq.articles.index') }}" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue sm:text-sm transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-bps-orange text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-orange sm:text-sm transition-colors">
                        <x-lucide-save class="w-4 h-4 mr-1.5" />
                        Simpan Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#answer' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
</x-app-layout>
