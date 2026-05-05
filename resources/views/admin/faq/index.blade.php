<x-app-layout>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-book-text class="w-6 h-6 text-bps-orange" />
                    Manajemen Artikel FAQ
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Kelola artikel panduan dan jawaban dari pertanyaan yang sering ditanyakan.
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.faq.articles.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-bps-orange hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-orange transition-colors">
                    <x-lucide-plus class="w-4 h-4 mr-1.5" />
                    Tulis Artikel Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-lucide-check-circle class="h-5 w-5 text-green-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Table Data -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 whitespace-nowrap">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pertanyaan / Judul</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statistik / Interaksi</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($faqs as $index => $faq)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 max-w-md truncate">{{ $faq->question }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $faq->category?->name ?? 'Uncategorized' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($faq->is_published)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">Published</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1 tooltip-trigger" title="Dilihat">
                                            <x-lucide-eye class="w-4 h-4 text-gray-400" />
                                            <span class="font-medium text-gray-700">{{ number_format($faq->views) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 tooltip-trigger" title="Sangat Membantu">
                                            <x-lucide-thumbs-up class="w-4 h-4 text-green-500" />
                                            <span class="font-medium text-green-700">{{ number_format($faq->helpful_count) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 tooltip-trigger" title="Tidak Membantu">
                                            <x-lucide-thumbs-down class="w-4 h-4 text-red-500" />
                                            <span class="font-medium text-red-700">{{ number_format($faq->unhelpful_count) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <!-- View Detail in frontend (blank target) -->
                                        <a href="{{ route('faq.show', $faq->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 p-1.5 rounded-md transition-colors tooltip-trigger" title="Preview Artikel">
                                            <x-lucide-external-link class="w-5 h-5" />
                                        </a>

                                        <a href="{{ route('admin.faq.articles.edit', $faq->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors tooltip-trigger" title="Edit Data">
                                            <x-lucide-edit class="w-5 h-5" />
                                        </a>

                                        <form id="delete-form-{{ $faq->id }}" action="{{ route('admin.faq.articles.destroy', $faq->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $faq->id }}', 'Anda yakin ingin menghapus artikel FAQ ini?')" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors tooltip-trigger" title="Hapus Artikel">
                                                <x-lucide-trash-2 class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center whitespace-nowrap">
                                    <x-lucide-book-text class="mx-auto h-12 w-12 text-gray-300" />
                                    <p class="mt-4 text-sm text-gray-500">Belum ada artikel panduan/FAQ yang ditulis.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
