<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SIGAP | Monitor & Protect</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
            .glass {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .hero-gradient {
                background: linear-gradient(135deg, rgba(0, 90, 140, 0.9) 0%, rgba(10, 10, 15, 0.95) 100%);
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-20px); }
            }
        </style>
    </head>
    <body class="antialiased bg-black text-white selection:bg-bps-orange selection:text-white">
        <div class="relative min-h-screen overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/hero.png') }}" alt="Hero Background" class="w-full h-full object-cover opacity-40">
                <div class="absolute inset-0 hero-gradient"></div>
            </div>

            <!-- Navigation -->
            <nav class="relative z-10 p-6 flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-bps-orange rounded-xl flex items-center justify-center shadow-lg shadow-bps-orange/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">BPS-PC <span class="text-bps-orange">Guardian</span></span>
                </div>

                @if (Route::has('login'))
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-ghost text-white hover:bg-white/10">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold px-6 py-2 rounded-full border border-white/30 hover:bg-white/10 hover:border-bps-orange hover:text-bps-orange transition-all duration-200">Masuk</a>
                    @endauth
                </div>
                @endif
            </nav>

            <!-- Hero Section -->
            <main class="relative z-10 flex flex-col items-center justify-center min-h-[80vh] px-6 text-center">
                <div class="glass p-2 rounded-2xl mb-8 animate-float">
                    <div class="bg-white/5 p-4 rounded-xl border border-white/10">
                        <span class="text-bps-orange font-semibold text-sm uppercase tracking-widest">System Operational</span>
                    </div>
                </div>

                <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight">
                    Monitor with <span class="text-bps-orange">Confidence.</span><br>
                    Protect with Precision.
                </h1>

                <p class="max-w-2xl text-lg md:text-xl text-gray-400 mb-10 leading-relaxed">
                    SIGAP provides real-time analytics, performance insights, and security auditing for your infrastructure. Built for speed, designed for clarity.
                </p>

                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('login') }}" class="btn bg-bps-blue hover:bg-bps-blue/80 border-none text-white px-10 py-4 h-auto text-lg rounded-full shadow-xl shadow-bps-blue/30">
                        Masuk ke Sistem
                    </a>
                    <a href="{{ route('faq.index') }}" class="btn btn-ghost border-white/20 hover:border-white/40 text-white px-10 py-4 h-auto text-lg rounded-full">
                        Buku Panduan
                    </a>
                </div>

                <!-- Stats/Social Proof -->
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-16">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">99.9%</div>
                        <div class="text-sm text-gray-500 uppercase tracking-widest">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">< 10ms</div>
                        <div class="text-sm text-gray-500 uppercase tracking-widest">Latency</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">256-bit</div>
                        <div class="text-sm text-gray-500 uppercase tracking-widest">Protection</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white mb-1">24/7</div>
                        <div class="text-sm text-gray-500 uppercase tracking-widest">Monitoring</div>
                    </div>
                </div>
            </main>

            <!-- Grid Decorative -->
            <div class="absolute inset-0 z-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 pointer-events-none"></div>
        </div>

        <footer class="relative z-10 p-10 bg-black/50 backdrop-blur-sm border-t border-white/5">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} SIGAP. All rights reserved.</p>
                <div class="flex space-x-6 text-sm text-gray-400">
                    <a href="#" class="hover:text-bps-orange transition">Privacy Policy</a>
                    <a href="#" class="hover:text-bps-orange transition">Terms of Service</a>
                    <a href="#" class="hover:text-bps-orange transition">Contact Support</a>
                </div>
            </div>
        </footer>
    </body>
</html>
