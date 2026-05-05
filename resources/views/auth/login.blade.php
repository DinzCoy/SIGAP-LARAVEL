<x-layouts.auth title="Masuk" subtitle="Sistem Monitoring Terpadu Aset Komputer">

    @php // =============================================
     // FORM LOGIN
     // ============================================= @endphp

    <div class="mb-8">
        <h2 class="text-2xl md:text-3xl font-black text-[#004a8d] mb-1.5 tracking-tight">Selamat Datang</h2>
        <p class="text-slate-400 font-medium text-sm md:text-base">Silakan masuk menggunakan akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        @php // Input Login @endphp
        <div class="space-y-1">
            <label for="login" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                Email atau Username
            </label>
            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                class="auth-input" placeholder="Masukkan email atau username">
            <x-input-error :messages="$errors->get('login')" class="mt-1" />
        </div>

        @php // Input Kata Sandi @endphp
        <div class="space-y-1" x-data="{ tampil: false }">
            <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                Kata Sandi
            </label>
            <div class="relative">
                <input :type="tampil ? 'text' : 'password'" id="password" name="password" required
                    class="auth-input pr-10" placeholder="••••••••">
                <button type="button" @click="tampil = !tampil"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#004a8d] transition-colors p-1">
                    {{-- Ikon mata terbuka --}}
                    <svg x-show="!tampil" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{-- Ikon mata tertutup --}}
                    <svg x-show="tampil" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        @php // Pilihan Role @endphp
        <div class="space-y-1">
            <label for="role_id" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-0.5">
                Masuk Sebagai
            </label>
            <select id="role_id" name="role_id" required class="auth-input appearance-none cursor-pointer">
                <option value="" disabled selected>-- Pilih Role Aktif --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-1" />
        </div>

        @php // Ingat Saya @endphp
        <div class="flex items-center justify-between pt-1 pb-3">
            <label class="flex items-center group cursor-pointer">
                <input type="checkbox" name="remember"
                    class="w-3.5 h-3.5 rounded border-slate-300 text-[#004a8d] focus:ring-[#004a8d] transition-all">
                <span class="ml-2 text-[11px] font-bold text-slate-500 group-hover:text-slate-700 transition-colors uppercase tracking-tight">
                    Ingat Saya
                </span>
            </label>
        </div>

        @php // Tombol Masuk @endphp
        <button type="submit" class="btn-auth">
            MASUK KE DASHBOARD
        </button>
    </form>

    @php // Status Sistem @endphp
    <div class="mt-8 pt-5 border-t border-slate-100 flex justify-between items-center opacity-60">
        <div class="flex space-x-3">
            <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Security SSL</span>
            <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Encrypted</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-[8px] font-bold uppercase tracking-widest text-emerald-600">System Online</span>
        </div>
    </div>

</x-layouts.auth>