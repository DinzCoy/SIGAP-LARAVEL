<section class="space-y-6">
    <header class="border-b border-slate-100 pb-5">
        <h2 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
            <x-lucide-user-cog class="w-5 h-5 text-indigo-500 shrink-0" />
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">
            {{ __('Kelola informasi identitas diri dan alamat email utama Anda.') }}
        </p>
    </header>

    @if (Route::has('verification.send'))
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center gap-6">
            <div class="shrink-0 relative">
                @if ($user->photo_path)
                    <img class="h-20 w-20 object-cover rounded-2xl border-4 border-white shadow-md ring-2 ring-indigo-500/20" src="{{ asset('storage/' . $user->photo_path) }}" alt="Foto profil {{ $user->name }}">
                @else
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center border-4 border-white text-white font-extrabold text-2xl shadow-md ring-2 ring-indigo-500/20 uppercase">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="space-y-2 flex-1 text-center sm:text-left">
                <label class="block">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Foto Profil</span>
                    <input type="file" name="foto_profil" id="foto_profil" class="block w-full text-xs text-slate-500
                      file:mr-4 file:py-2.5 file:px-4
                      file:rounded-xl file:border-0
                      file:text-xs file:font-bold
                      file:bg-indigo-50 file:text-indigo-700
                      hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer transition-all
                    " accept="image/jpeg,image/jpg,image/png,image/webp"/>
                </label>
                <p class="text-[10px] font-semibold text-slate-400">JPEG, JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                <x-input-error class="mt-1" :messages="$errors->get('foto_profil')" />
            </div>
        </div>

        <div class="space-y-2">
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-slate-700 text-xs uppercase tracking-wider block" />
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-lucide-user class="h-5 w-5 text-slate-400" />
                </div>
                <input id="name" name="name" type="text"
                    class="block w-full pl-10 pr-4 py-3 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm transition-all placeholder-slate-400 font-medium text-slate-800"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap Anda" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="email" :value="__('Alamat Email')" class="font-bold text-slate-700 text-xs uppercase tracking-wider block" />
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-lucide-mail class="h-5 w-5 text-slate-400" />
                </div>
                <input id="email" name="email" type="email"
                    class="block w-full pl-10 pr-4 py-3 border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm transition-all placeholder-slate-400 font-medium text-slate-800"
                    value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="Masukkan alamat email Anda" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 rounded-xl border border-amber-100 text-amber-800">
                    <p class="text-xs font-semibold flex items-center gap-1.5">
                        <x-lucide-alert-circle class="w-4 h-4 text-amber-500" />
                        {{ __('Alamat email Anda belum terverifikasi.') }}
                        <button form="send-verification" class="underline text-xs text-amber-600 hover:text-amber-800 font-bold ml-auto">
                            {{ __('Kirim Ulang Email Verifikasi') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-emerald-600">
                            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-3 border-t border-slate-100">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-sm hover:shadow transition-all duration-200 cursor-pointer select-none">
                <x-lucide-save class="w-4 h-4" />
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100"
                >
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-500" />
                    {{ __('Perubahan profil Anda berhasil disimpan.') }}
                </div>
            @endif
        </div>
    </form>
</section>
