<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - BPS-PC Guardian</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white flex flex-col lg:flex-row min-h-screen">

    <!-- Top/Left Side: Branding / Background -->
    <div class="flex w-full lg:w-1/2 bg-bps-blue relative flex-col justify-center items-center p-8 lg:p-12 overflow-hidden shrink-0">
        <!-- Abstract Decoration -->
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <polygon fill="currentColor" points="0,100 100,0 100,100" />
            </svg>
        </div>
        
        <div class="relative z-10 text-center text-white">
            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-white rounded-full flex items-center justify-center font-bold text-bps-blue text-2xl lg:text-3xl shadow-xl border-[3px] lg:border-4 border-bps-orange mx-auto mb-4 lg:mb-8">
                BPS
            </div>
            <h1 class="text-2xl lg:text-4xl font-extrabold tracking-tight mb-2 lg:mb-4">BPS-PC Guardian</h1>
            <p class="text-xs lg:text-lg text-blue-100 max-w-sm mx-auto leading-relaxed px-4">
                Sistem Monitoring Terpadu Aset Komputer Badan Pusat Statistik (BPS) Provinsi Sulawesi Selatan.
            </p>
        </div>
        
        <div class="absolute bottom-4 lg:bottom-8 text-blue-200 text-xs lg:text-sm">
            &copy; {{ date('Y') }} BPS Sulsel
        </div>
    </div>

    <!-- Bottom/Right Side: Login Form -->
    <div class="flex-1 w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white z-10">
        <div class="w-full max-w-md space-y-6 lg:space-y-8">
            <div class="text-center lg:text-left">
                <h2 class="text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">Selamat Datang</h2>
                <p class="mt-2 text-sm text-gray-500">Silakan masuk menggunakan akun Anda untuk mengakses dashboard.</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 lg:mt-8 space-y-5 lg:space-y-6">
                @csrf
                <div class="space-y-4">
                    <!-- Email / Username -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email atau Username</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="text" autocomplete="username" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm" placeholder="Masukkan email atau username" value="{{ old('email') }}">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Masuk Sebagai</label>
                        <div class="mt-1">
                            <select id="role_id" name="role_id" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-gray-700 focus:outline-none focus:ring-bps-blue focus:border-bps-blue sm:text-sm bg-white">
                                <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>-- Pilih Role Aktif --</option>
                                <option value="1" {{ old('role_id') == '1' ? 'selected' : '' }}>Pimpinan</option>
                                <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Admin</option>
                                <option value="3" {{ old('role_id') == '3' ? 'selected' : '' }}>Teknisi</option>
                                <option value="4" {{ old('role_id') == '4' ? 'selected' : '' }}>Pengelola Barang</option>
                                <option value="5" {{ old('role_id') == '5' ? 'selected' : '' }}>Pengelola Ruangan</option>
                                <option value="6" {{ old('role_id') == '6' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2 text-sm text-red-600" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-bps-blue focus:ring-bps-blue border-gray-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                            Ingat Saya
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-bps-blue hover:text-blue-800 transition-colors">
                            Lupa password?
                        </a>
                    </div>
                    @endif
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-bps-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue transition-all transform hover:-translate-y-0.5">
                        Masuk ke Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>