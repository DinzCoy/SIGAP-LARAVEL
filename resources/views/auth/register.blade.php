<x-layouts.auth title="Daftar Akun" subtitle="Bergabunglah dengan Kami">

    @php // =============================================
     // FORM REGISTRASI AKUN BARU
     // ============================================= @endphp

    <div class="mb-8">
        <h2 class="text-2xl md:text-3xl font-black text-[#004a8d] mb-1.5 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-slate-400 font-medium text-sm md:text-base">Silakan lengkapi data untuk mendaftar.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        @php // Input Nama Lengkap @endphp
        <div class="space-y-1">
            <label for="name" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                Nama Lengkap
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="auth-input" placeholder="Masukkan nama lengkap">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        @php // Input Username @endphp
        <div class="space-y-1">
            <label for="username" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                Username
            </label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required
                class="auth-input" placeholder="Pilih username unik">
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>

        @php // Input Email @endphp
        <div class="space-y-1">
            <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                Alamat Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="auth-input" placeholder="nama@bps.go.id">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        @php // Input Kata Sandi & Konfirmasi @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                    Kata Sandi
                </label>
                <input id="password" type="password" name="password" required
                    class="auth-input" placeholder="Min. 8 karakter">
            </div>
            <div class="space-y-1">
                <label for="password_confirmation" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                    Konfirmasi
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="auth-input" placeholder="Ulangi sandi">
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-1" />

        @php // Tombol Daftar @endphp
        <button type="submit" class="btn-auth mt-2">
            DAFTAR SEKARANG
        </button>

        <div class="text-center pt-2">
            <p class="text-sm text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-[#004a8d] hover:text-[#00d2d2] transition-colors ml-1">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>

</x-layouts.auth>