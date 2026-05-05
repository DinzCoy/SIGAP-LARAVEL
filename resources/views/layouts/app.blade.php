<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script>
    // Prevent FOUC: apply dark class before any rendering
    if (localStorage.getItem('bps_dark_mode') === 'true') {
        document.documentElement.classList.add('dark');
    }
</script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/dark-mode.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased text-gray-900 overflow-hidden bg-gray-50">
    <!-- Main Layout Container -->
    <div class="flex h-screen overflow-hidden"
        x-data="{ sidebarOpen: window.innerWidth >= 1024, isMobile: window.innerWidth < 1024 }"
        @resize.window="isMobile = window.innerWidth < 1024">

        <!-- Mobile Backdrop (Fixed Overlay) — only shown on mobile when sidebar is open -->
        <div x-show="sidebarOpen && isMobile" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[60]" style="display: none;">
        </div>

        <!-- Sidebar (Desktop: Flex sibling, Mobile: Fixed) -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            @include('layouts.navigation')

            <div class="flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar bg-[#f8fafc]">
                <!-- Page Heading (Sticky) -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100 shadow-sm">
                        <div class="w-full mx-auto py-5 px-6 sm:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content (Dashboard/Tabel) -->
                <main class="p-6 sm:p-8 w-full mx-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    <style>
        /* Content area scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Sidebar smooth transition - Enhanced for Premium feel */
        .sidebar-transition {
            transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
            will-change: width, transform;
        }

        /* Custom cubic-bezier for smoother feel */
        .sidebar-ease {
            transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Sidebar nav scrollbar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Staggered menu item animation */
        @keyframes sidebarFadeIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar-menu-item {
            animation: sidebarFadeIn 400ms ease-out both;
        }

        /* Sembunyikan label "Powered by CKEditor" di versi gratis */
        .ck-powered-by {
            display: none !important;
        }
    </style>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // SweetAlert2 Global Handlers
        document.addEventListener('DOMContentLoaded', function () {
            // Flash Success Message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            @endif

            // Flash Error Message
            @if(session('error') || $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') ?? ($errors->first() ?? 'Terjadi kesalahan sistem.') }}",
                    confirmButtonColor: '#3b82f6',
                });
            @endif
            });

        // Global Confirmation Function
        window.confirmDelete = function (formId, message = 'Apakah Anda yakin ingin menghapus data ini?') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>

</html>