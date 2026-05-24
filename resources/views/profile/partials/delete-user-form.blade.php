<section class="space-y-6">
    <header class="border-b border-red-100 pb-5">
        <h2 class="text-lg font-black text-red-800 tracking-tight flex items-center gap-2">
            <x-lucide-alert-triangle class="w-5 h-5 text-red-600 shrink-0 animate-bounce" />
            {{ __('Zona Bahaya: Hapus Akun') }}
        </h2>
        <p class="mt-1.5 text-xs font-semibold text-red-400 uppercase tracking-wider">
            {{ __('Tindakan ini bersifat permanen dan tidak dapat dibatalkan.') }}
        </p>
    </header>

    <div class="p-4 bg-red-50 rounded-xl border border-red-100/50 text-red-800 text-sm leading-relaxed font-medium">
        {{ __('Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen dari server SIGAP. Sebelum menghapus akun Anda, pastikan Anda telah mencadangkan atau mengunduh data penting yang mungkin Anda butuhkan.') }}
    </div>

    <div>
        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-sm hover:shadow transition-all duration-200 cursor-pointer select-none"
        >
            <x-lucide-trash-2 class="w-4 h-4" />
            {{ __('Hapus Akun Saya') }}
        </button>
    </div>

    <!-- Confirmation Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('delete')

            <div class="text-center sm:text-left space-y-2">
                <h2 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <x-lucide-alert-triangle class="w-5 h-5 text-red-500 shrink-0" />
                    {{ __('Apakah Anda yakin ingin menghapus akun?') }}
                </h2>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider leading-relaxed">
                    {{ __('Masukkan password Anda untuk memverifikasi tindakan penghapusan akun permanen.') }}
                </p>
            </div>

            <!-- Password Verification Input -->
            <div class="space-y-2">
                <x-input-label for="password" value="{{ __('Kata Sandi Konfirmasi') }}" class="font-bold text-slate-700 text-xs uppercase tracking-wider block" />
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-lucide-lock class="h-5 w-5 text-slate-400" />
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full pl-10 pr-4 py-3 border-slate-200 focus:border-red-500 focus:ring-red-500 rounded-xl text-sm transition-all placeholder-slate-400 font-medium text-slate-800"
                        placeholder="Masukkan password Anda untuk konfirmasi"
                        required
                    />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')"
                    class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all duration-200 cursor-pointer select-none"
                >
                    {{ __('Batal') }}
                </button>

                <button 
                    type="submit"
                    class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-sm hover:shadow transition-all duration-200 cursor-pointer select-none"
                >
                    <x-lucide-trash-2 class="w-4 h-4" />
                    {{ __('Hapus Permanen') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
