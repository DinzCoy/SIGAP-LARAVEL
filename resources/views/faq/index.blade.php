<x-app-layout>
    <!-- Background Header -->
    <div class="bg-gradient-to-r from-bps-blue to-blue-800 rounded-2xl p-8 mb-8 text-white shadow-lg relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
        <div class="absolute bottom-0 right-32 -mb-8 w-40 h-40 rounded-full bg-white opacity-10 blur-3xl"></div>
        
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl font-bold mb-3">Hi, ada yang bisa kami bantu?</h1>
            <p class="text-blue-100 text-lg mb-6">Temukan jawaban untuk pertanyaan yang sering diajukan dan panduan penggunaan sistem di sini.</p>
            
            <!-- Search field placeholder -->
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none" style="padding-left: 1rem;">
                    <x-lucide-search class="w-5 h-5 text-gray-400" />
                </div>
                <!-- Inline padding-left guarantees layout priority regardless of JIT plugin conflicts -->
                <input type="text" id="faq-search" placeholder="Ketik kata kunci untuk mencari panduan..." style="padding-left: 2.75rem;" class="block w-full pr-4 py-3 rounded-xl text-gray-900 border-0 ring-1 ring-inset ring-gray-100 shadow-sm bg-gray-50 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-bps-blue sm:text-sm sm:leading-6">
            </div>
        </div>
    </div>

    <!-- FAQ Content -->
    <div class="space-y-8">
        @forelse($categories as $category)
            @if($category->faqs->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden faq-category-container">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <x-lucide-folder class="w-5 h-5 text-bps-orange" />
                            {{ $category->name }}
                        </h2>
                        <span class="bg-blue-100 text-bps-blue text-xs font-bold px-2 py-1 rounded-full">
                            {{ $category->faqs->count() }} Artikel
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($category->faqs as $faq)
                            <a href="{{ route('faq.show', $faq->id) }}" class="block p-6 hover:bg-gray-50 transition-colors group faq-article-link">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-bps-blue transition-colors mb-1">{{ $faq->question }}</h3>
                                        <!-- Plain text excerpt of the answer -->
                                        <p class="text-sm text-gray-500 line-clamp-2">
                                            {{ Str::limit(strip_tags($faq->answer), 150) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 text-gray-400 group-hover:text-bps-orange transition-colors">
                                        <x-lucide-chevron-right class="w-5 h-5" />
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-4 text-xs text-gray-400">
                                    <div class="flex items-center gap-1">
                                        <x-lucide-eye class="w-3.5 h-3.5" />
                                        {{ number_format($faq->views) }}x dilihat
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <x-lucide-calendar class="w-3.5 h-3.5" />
                                        Diperbarui {{ $faq->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-lucide-book-open class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Panduan</h3>
                <p class="text-gray-500">Saat ini belum ada artikel FAQ yang dipublikasikan.</p>
            </div>
        @endforelse
    </div>

    <!-- Client-side Search Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faq-search');
            
            if(searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    const categories = document.querySelectorAll('.faq-category-container');
                    
                    categories.forEach(category => {
                        let hasVisibleArticles = false;
                        const articles = category.querySelectorAll('a.faq-article-link');
                        
                        articles.forEach(article => {
                            const question = article.querySelector('h3').textContent.toLowerCase();
                            const answer = article.querySelector('p').textContent.toLowerCase();
                            
                            if (question.includes(query) || answer.includes(query)) {
                                article.style.display = 'block';
                                hasVisibleArticles = true;
                            } else {
                                article.style.display = 'none';
                            }
                        });
                        
                        // Hide the entire category if no articles match
                        if (hasVisibleArticles) {
                            category.style.display = 'block';
                        } else {
                            category.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
