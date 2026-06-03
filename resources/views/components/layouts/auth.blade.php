@props([
    'title'    => 'SIGAP',
    'subtitle' => 'Sistem Guardian Aset dan Pelayanan IT',
])
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIGAP' }} | SIGAP - BPS Sulsel</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>

        :root {
            --bps-navy: #004a8d;
            --bps-teal: #00d2d2;
            --bps-bg:   #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: white;
            overflow-x: hidden;
        }

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

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes glowPulse {
            0%, 100% {
                filter: drop-shadow(0 0 12px rgba(255, 255, 255, 0.2)) drop-shadow(0 0 22px rgba(0, 74, 141, 0.4));
            }
            50% {
                filter: drop-shadow(0 0 28px rgba(255, 255, 255, 0.45)) drop-shadow(0 0 48px rgba(0, 210, 210, 0.65));
            }
        }

        .animate-shield {
            animation: float 4s ease-in-out infinite, glowPulse 4s ease-in-out infinite;
        }

        .shimmer-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 100%;
            -webkit-mask-image: url('{{ asset('images/logo_sigap.svg') }}');
            mask-image: url('{{ asset('images/logo_sigap.svg') }}');
            -webkit-mask-size: contain;
            mask-size: contain;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-position: center;
        }

        .sheen-sweep {
            position: absolute;
            top: -50%;
            left: -150%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                105deg,
                rgba(255, 255, 255, 0) 30%,
                rgba(255, 255, 255, 0.05) 42%,
                rgba(255, 255, 255, 0.6) 50%,
                rgba(255, 255, 255, 0.05) 58%,
                rgba(255, 255, 255, 0) 70%
            );
            pointer-events: none;
            animation: sheen 5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes sheen {
            0% {
                left: -150%;
            }
            35%, 100% {
                left: 150%;
            }
        }

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

        <div class="left-panel">

            <div class="diagonal-overlay" style="opacity:0.3; pointer-events:none; z-index:0;"></div>

            <div style="height:2rem; width:100%;"></div>

            <div class="left-panel-inner">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 320px; position: relative;">
                    <!-- Shield Icon (Floating & Glowing with Shimmer) -->
                    <div class="animate-shield" style="width: 370px; height: 370px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;">
                        <div class="shimmer-wrapper">
                            <img src="{{ asset('images/logo_sigap.svg') }}" style="width: 100%; height: 100%; object-fit: contain;" alt="Logo SIGAP">
                            <div class="sheen-sweep"></div>
                        </div>
                    </div>
                    <!-- Name Logo (Static) -->
                    <img src="{{ asset('images/nama_logo.svg') }}" style="width: 320px; height: 320px; object-fit: contain; clip-path: inset(40% 0 41.5% 0); margin-top: -118px; margin-bottom: -125px; z-index: 5;" alt="Nama SIGAP">
                </div>

                <div style="max-width: 28rem; padding: 0 1rem; text-align: center;">
                    <div style="height: 2px; width: 4rem; background: rgba(255,255,255,0.2); border-radius: 9999px; margin: 0 auto 1.5rem;"></div>
                    <p style="font-size: 1.125rem; color: white; font-weight: 500; letter-spacing: -0.025em; line-height: 1.5;">{{ $subtitle }}</p>
                    <p style="color: rgba(255,255,255,0.5); font-size: 10px; margin-top: 0.75rem; text-transform: uppercase; letter-spacing: 0.25em; font-weight: 700;">BPS Provinsi Sulawesi Selatan</p>
                </div>
            </div>

            <div style="z-index:20; text-align:center; color:rgba(255,255,255,0.3); font-size:9px; font-weight:500; padding:1rem 0; letter-spacing:0.2em; text-transform:uppercase;">
                © {{ date('Y') }} Badan Pusat Statistik Provinsi Sulawesi Selatan
            </div>
        </div>

        <div class="right-panel">

            <div class="mobile-logo" style="display: flex; align-items: center; gap: 0.5rem; position: absolute; top: 1.5rem; left: 1.5rem;">
                <img src="{{ asset('images/logo_sigap.svg') }}" class="h-8 w-auto object-contain" alt="Logo SIGAP">
            </div>

            <div style="width:100%; max-width:22rem;">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>
</html>
