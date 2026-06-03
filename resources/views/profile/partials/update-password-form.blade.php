@if (Route::has('password.update'))
<section class="space-y-6">
    <header class="border-b border-slate-100 pb-5">
        <h2 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
            <x-lucide-shield-check class="w-5 h-5 text-indigo-500 shrink-0" />
            {{ __('Ubah Kata Sandi') }}
        </h2>
        <p class="mt-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">
            {{ __('Pastikan akun Anda terlindungi dengan menggunakan kata sandi yang kuat dan aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="font-bold text-slate-700 text-xs uppercase tracking-wider block" />
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-lucide-lock class="h-5 w-5 text-slate-400" />
                </div>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="block w-full pl-10 pr-4 py-3 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm transition-all placeholder-slate-400 font-medium text-slate-800"
                    autocomplete="current-password" placeholder="Masukkan password saat ini" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password" :value="__('Password Baru')" class="font-bold text-slate-700 text-xs uppercase tracking-wider block" />
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-lucide-key-round class="h-5 w-5 text-slate-400" />
                </div>
                <input id="update_password_password" name="password" type="password"
                    class="block w-full pl-10 pr-4 py-3 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm transition-all placeholder-slate-400 font-medium text-slate-800"
                    autocomplete="new-password" placeholder="Masukkan password baru minimal 8 karakter" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" class="font-bold text-slate-700 text-xs uppercase tracking-wider block" />
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-lucide-key-round class="h-5 w-5 text-slate-400" />
                </div>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="block w-full pl-10 pr-4 py-3 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm transition-all placeholder-slate-400 font-medium text-slate-800"
                    autocomplete="new-password" placeholder="Ulangi password baru Anda" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-3 border-t border-slate-100">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-sm hover:shadow transition-all duration-200 cursor-pointer select-none">
                <x-lucide-save class="w-4 h-4" />
                {{ __('Ubah Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100"
                >
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-500" />
                    {{ __('Password Anda berhasil diperbarui.') }}
                </div>
            @endif
        </div>
    </form>
</section>
@endif
