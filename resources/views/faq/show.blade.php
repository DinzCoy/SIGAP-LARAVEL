<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('faq.index') }}" class="inline-flex items-center hover:text-bps-blue transition-colors">
                        <x-lucide-book-text class="w-4 h-4 mr-2" />
                        Panduan & FAQ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-lucide-chevron-right class="w-4 h-4 text-gray-400 mx-1" />
                        <span class="hover:text-gray-900">{{ $faq->category?->name ?? 'Uncategorized' }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
            <!-- decorative line -->
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-bps-blue to-bps-orange"></div>

            <!-- Article Header -->
            <div class="p-8 pb-6 border-b border-gray-100 bg-gray-50/30 mt-1">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-bps-blue border border-blue-100">
                        {{ $faq->category?->name ?? 'Uncategorized' }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-4 tracking-tight">
                    {{ $faq->question }}
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                        Diperbarui pada {{ $faq->updated_at->translatedFormat('d F Y, H:i') }}
                    </div>
                    <div class="hidden sm:block text-gray-300">•</div>
                    <div class="flex items-center gap-1.5 bg-gray-100 px-2 py-0.5 rounded-md">
                        <x-lucide-eye class="w-4 h-4 text-gray-400" />
                        <span class="font-medium text-gray-700">{{ number_format($faq->views) }}</span> dibaca
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="p-8 prose prose-blue prose-lg max-w-none text-gray-700 leading-relaxed 
                        prose-headings:text-gray-900 prose-headings:font-bold 
                        prose-a:text-bps-blue hover:prose-a:text-bps-orange prose-a:transition-colors
                        prose-ol:pl-4 prose-ul:pl-4 prose-li:marker:text-gray-400
                        prose-strong:text-gray-900 prose-strong:font-bold
                        prose-img:rounded-xl prose-img:shadow-sm">
                {!! $faq->answer !!}
            </div>
            
            <!-- Feedback Section -->
            <div id="feedback-section" class="px-8 py-6 bg-gray-50 border-t border-gray-100 text-center relative overflow-hidden transition-all duration-300">
                <p class="text-sm font-medium text-gray-600 mb-3" id="feedback-question">Apakah artikel ini membantu Anda?</p>
                <div class="flex justify-center gap-3" id="feedback-buttons">
                    <button onclick="submitFeedback('helpful')" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-200 transition-all shadow-sm">
                        <x-lucide-thumbs-up class="w-4 h-4 mr-2" />
                        Sangat Membantu
                    </button>
                    <button onclick="submitFeedback('unhelpful')" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-all shadow-sm">
                        <x-lucide-thumbs-down class="w-4 h-4 mr-2" />
                        Tidak Membantu
                    </button>
                </div>
                <!-- Success Message -->
                <div id="feedback-success" class="hidden flex items-center justify-center text-green-600 font-medium scale-95 opacity-0 transition-all duration-500">
                    <x-lucide-check-circle class="w-5 h-5 mr-2" />
                    Terima kasih atas masukannya!
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
            <div class="flex justify-end">
                <a href="{{ route('admin.faq.articles.edit', $faq->id) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-bps-blue transition-colors">
                    <x-lucide-edit class="w-4 h-4 mr-1.5" />
                    Edit Artikel ini (Admin)
                </a>
            </div>
        @endif
    </div>

    <!-- Styling untuk konten artikel FAQ -->
    <style>
        /* ── Heading Hierarchy ── */
        .prose h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #111827;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .prose h2:first-child { margin-top: 0; }

        .prose h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* ── Lists ── */
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin: 0.75rem 0; }
        .prose ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0.75rem 0; }
        .prose li { margin-bottom: 0.35rem; line-height: 1.7; }
        .prose li > ul, .prose li > ol { margin-top: 0.35rem; margin-bottom: 0.25rem; }

        /* ── Table ── */
        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .prose thead { background-color: #f3f4f6; }
        .prose th {
            padding: 0.65rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .prose td {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            vertical-align: top;
        }
        .prose tbody tr:last-child td { border-bottom: none; }
        .prose tbody tr:hover { background-color: #f9fafb; }

        /* ── Inline Code ── */
        .prose code {
            background-color: #f1f5f9;
            color: #0f4c81;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.85em;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            font-family: 'Fira Code', 'Cascadia Code', 'Consolas', monospace;
        }

        /* ── Blockquote ── */
        .prose blockquote {
            border-left: 4px solid #fca311;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            padding: 1rem 1.25rem;
            border-radius: 0 0.5rem 0.5rem 0;
            font-style: normal;
            margin: 1.25rem 0;
            color: #78350f;
        }
        .prose blockquote strong { color: #92400e; }
        .prose blockquote p { margin: 0; }

        /* ── Paragraphs ── */
        .prose p { margin-bottom: 0.85rem; line-height: 1.8; }

        /* ── Strong / Bold ── */
        .prose strong { color: #111827; font-weight: 600; }

        /* ── Emphasis ── */
        .prose em { color: #4b5563; }

        /* ── Horizontal Rule ── */
        .prose hr { border-color: #e5e7eb; margin: 1.5rem 0; }
    </style>

    <script>
        function submitFeedback(type) {
            const faqId = {{ $faq->id }};
            const csrfToken = '{{ csrf_token() }}';
            
            // Hide buttons, show success
            document.getElementById('feedback-buttons').classList.add('hidden');
            document.getElementById('feedback-question').classList.add('hidden');
            const successDiv = document.getElementById('feedback-success');
            successDiv.classList.remove('hidden');
            setTimeout(() => {
                successDiv.classList.remove('scale-95', 'opacity-0');
            }, 50);

            // Send fetch request
            fetch(`/faq/${faqId}/feedback`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Feedback submitted:', data);
            })
            .catch(error => {
                console.error('Error submitting feedback:', error);
            });
        }
    </script>
</x-app-layout>
