@props([
    'title'    => 'SIGAP',
    'subtitle' => 'Sistem Monitoring Terpadu Aset Komputer',
])
<!DOCTYPE html>
<html lang="id">
@php // Layout Induk SIGAP — Digunakan oleh semua halaman auth @endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIGAP' }} | SIGAP - BPS Sulsel</title>

    @php // Google Fonts @endphp
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php // Alpine JS @endphp
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php // Gaya Terpusat Auth — Hanya didefinisikan di satu tempat @endphp
    <style>
        /* =========================================
         * TOKEN WARNA BPULSE
         * ========================================= */
        :root {
            --bps-navy: #004a8d;
            --bps-teal: #00d2d2;
            --bps-bg:   #f8fafc;
        }

        /* =========================================
         * DASAR
         * ========================================= */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: white;
            overflow-x: hidden;
        }

        /* =========================================
         * ANIMASI EKG DETAK JANTUNG
         * ========================================= */
        @keyframes heartPulse {
            0%   { stroke-dashoffset: 1000; opacity: 0; }
            10%  { opacity: 1; }
            40%  { stroke-dashoffset: 0; }
            60%  { stroke-dashoffset: 0; opacity: 1; }
            100% { stroke-dashoffset: -1000; opacity: 0; }
        }

        .pulse-line {
            stroke: var(--bps-teal);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            stroke-dasharray: 1000;
            animation: heartPulse 3s ease-in-out infinite;
        }

        .pulse-glow {
            filter: drop-shadow(0 0 8px rgba(0, 210, 210, 0.6));
        }

        /* =========================================
         * ANIMASI MELAYANG LOGO
         * ========================================= */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        /* =========================================
         * STRUKTUR HALAMAN AUTH
         * ========================================= */
        .auth-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @media (min-width: 768px) {
            .auth-wrapper {
                flex-direction: row;
            }
        }

        /* =========================================
         * PANEL KIRI
         * ========================================= */
        .left-panel {
            display: none;
            background-color: var(--bps-navy);
            position: relative;
            overflow: hidden;
            width: 50%;
            flex-shrink: 0;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 3rem;
            color: white;
        }

        @media (min-width: 768px) {
            .left-panel {
                display: flex;
            }
        }

        .diagonal-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 50%);
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .sigap-title span {
            display: inline-block;
        }

        /* =========================================
         * PANEL KANAN
         * ========================================= */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-color: white;
            position: relative;
            z-index: 30;
        }

        @media (min-width: 640px) { .right-panel { padding: 3rem; } }
        @media (min-width: 768px) { .right-panel { padding: 4rem; } }

        .left-panel-inner {
            z-index: 10;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            width: 100%;
        }

        /* =========================================
         * LOGO MOBILE
         * ========================================= */
        .mobile-logo {
            display: flex;
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            align-items: center;
            gap: 0.5rem;
        }

        @media (min-width: 768px) {
            .mobile-logo {
                display: none;
            }
        }

        /* =========================================
         * INPUT FORM
         * ========================================= */
        .auth-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--bps-bg);
            border: 1px solid #e2e8f0;
            border-radius: 0.6rem;
            transition: all 0.2s;
            outline: none;
            font-size: 0.9rem;
            font-weight: 500;
            color: #334155;
        }

        .auth-input:focus {
            background-color: white;
            border-color: var(--bps-navy);
            box-shadow: 0 0 0 3px rgba(0, 74, 141, 0.08);
        }

        .auth-input[readonly] {
            background-color: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        /* =========================================
         * TOMBOL UTAMA
         * ========================================= */
        .btn-auth {
            background-color: var(--bps-navy) !important;
            color: white !important;
            font-weight: 700;
            padding: 0.8rem;
            border-radius: 0.6rem;
            width: 100%;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 74, 141, 0.2);
            font-size: 0.95rem;
            cursor: pointer;
            border: none;
        }

        .btn-auth:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
            box-shadow: 0 6px 10px -1px rgba(0, 74, 141, 0.3);
        }

        .btn-auth:active {
            transform: translateY(0);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased text-gray-900 bg-white">

    <div class="auth-wrapper">

        @php // =============================================
         // PANEL KIRI: BRANDING & ANIMASI (BERSAMA)
         // ============================================= @endphp
        <div class="left-panel">

            @php // Overlay dekorasi diagonal @endphp
            <div class="diagonal-overlay" style="opacity:0.3; pointer-events:none; z-index:0;"></div>

            @php // Spacer atas @endphp
            <div style="height:2rem; width:100%;"></div>

            @php // Konten tengah: Logo & Judul @endphp
            <div class="left-panel-inner">

                @php // Logo bulat dengan animasi EKG @endphp
                <div class="animate-float" style="background:white; box-shadow:0 0 60px rgba(255,255,255,0.25); margin-bottom:2rem; position:relative; width:140px; height:140px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg viewBox="0 0 100 100" class="pulse-glow" style="position:absolute; top:0; left:0; width:100%; height:100%; transform:scale(1.1); z-index:20;">
                        <path class="pulse-line" d="M0,50 L30,50 L35,30 L45,70 L50,50 L100,50" />
                    </svg>
                    <span style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:900; font-size:2.5rem; line-height:1; color:#004a8d; position:relative; z-index:50; user-select:none; letter-spacing:-0.05em;">BPS</span>
                </div>

                @php // Judul SIGAP @endphp
                <div class="sigap-title" style="display:flex; align-items:flex-end; justify-content:center; font-weight:800; letter-spacing:-0.05em; margin-bottom:1rem; user-select:none; line-height:1; z-index:20; width:100%;">
                    <span style="color:white; font-size:clamp(3rem,5vw,4.5rem);">BP</span>
                    <span style="color:#00d2d2; font-size:clamp(2rem,3.5vw,3rem); font-weight:500; margin:0 2px 4px;">ul</span>
                    <span style="color:white; font-size:clamp(3rem,5vw,4.5rem);">S</span>
                    <span style="color:#00d2d2; font-size:clamp(2rem,3.5vw,3rem); font-weight:500; margin:0 2px 4px;">e</span>
                </div>

                @php // Garis aksen & slogan @endphp
                <div style="height:4px; width:4rem; background:#00d2d2; border-radius:9999px; margin-bottom:1.5rem; box-shadow:0 0 20px rgba(0,210,210,0.6);"></div>
                <h2 style="font-weight:700; letter-spacing:0.3em; text-transform:uppercase; opacity:0.9; margin-bottom:2rem; color:white; font-size:0.875rem;">THE HEARTBEAT OF IT</h2>

                @php // Subjudul per halaman @endphp
                <div style="max-width:28rem; padding:0 1rem; text-align:center;">
                    <p style="font-size:1.125rem; color:white; font-weight:500; letter-spacing:-0.025em;">{{ $subtitle }}</p>
                    <p style="color:rgba(255,255,255,0.4); font-size:9px; margin-top:1rem; text-transform:uppercase; letter-spacing:0.3em; font-weight:700;">BPS Provinsi Sulawesi Selatan</p>
                </div>
            </div>

            @php // Hak cipta @endphp
            <div style="z-index:20; text-align:center; color:rgba(255,255,255,0.3); font-size:9px; font-weight:500; padding:1rem 0; letter-spacing:0.2em; text-transform:uppercase;">
                © {{ date('Y') }} Badan Pusat Statistik Provinsi Sulawesi Selatan
            </div>
        </div>

        @php // =============================================
         // PANEL KANAN: KONTEN FORM (BERUBAH PER HALAMAN)
         // ============================================= @endphp
        <div class="right-panel">

            @php // Logo mobile (tampil hanya di layar kecil) @endphp
            <div class="mobile-logo">
                <div style="width:2rem; height:2rem; background:#004a8d; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:9px;">BPS</div>
                <span style="font-weight:700; font-size:1rem; letter-spacing:-0.05em;">BP<span style="color:#00d2d2;">ul</span>S<span style="color:#00d2d2;">e</span></span>
            </div>

            @php // Slot konten form dari masing-masing halaman @endphp
            <div style="width:100%; max-width:22rem;">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>
</html>
