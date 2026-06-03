<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight flex items-center gap-2.5">
            <x-lucide-user class="w-6 h-6 text-indigo-500 shrink-0" />
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div x-data="{ activeTab: 'personal-info' }" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <div class="lg:col-span-4 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

                <div class="h-28 bg-gradient-to-r from-indigo-600 to-purple-600 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
                    <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-white/5 blur-xl"></div>
                    <div class="absolute -left-10 -bottom-10 w-28 h-28 rounded-full bg-white/5 blur-lg"></div>
                </div>

                <div class="px-6 pb-6 text-center relative -mt-12">
                    <div class="inline-block relative">
                        @if ($user->photo_path)
                            <img class="h-24 w-24 object-cover rounded-2xl border-4 border-white shadow-xl ring-1 ring-slate-100" src="{{ asset('storage/' . $user->photo_path) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-24 w-24 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center border-4 border-white text-white font-extrabold text-3xl shadow-xl ring-1 ring-slate-100 uppercase">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 h-4 w-4 rounded-full bg-emerald-500 border-2 border-white shadow animate-pulse"></span>
                    </div>

                    <h3 class="mt-4 font-black text-xl text-slate-800 tracking-tight leading-tight">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-1 tracking-wide">{{ $user->email }}</p>

                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <span class="inline-flex px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-black border border-indigo-100/50 uppercase tracking-wider">
                            {{ \App\Models\User::getRoleName(session('active_role_id')) }}
                        </span>
                        <span class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black border border-emerald-100/50 uppercase tracking-wider">
                            Online
                        </span>
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-5 text-left">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3 px-3">Menu Profil</span>
                        <nav class="space-y-1.5">
                            <button @click="activeTab = 'personal-info'"
                                :class="activeTab === 'personal-info' ? 'bg-indigo-50/50 text-indigo-600 border-indigo-100/30' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold border transition-all text-left focus:outline-none">
                                <span :class="activeTab === 'personal-info' ? 'text-indigo-500' : 'text-slate-400'" class="flex items-center justify-center shrink-0">
                                    <x-lucide-user class="w-5 h-5" />
                                </span>
                                Informasi Personal
                            </button>
                            <button @click="activeTab = 'update-password'"
                                :class="activeTab === 'update-password' ? 'bg-indigo-50/50 text-indigo-600 border-indigo-100/30' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold border transition-all text-left focus:outline-none">
                                <span :class="activeTab === 'update-password' ? 'text-indigo-500' : 'text-slate-400'" class="flex items-center justify-center shrink-0">
                                    <x-lucide-key-round class="w-5 h-5" />
                                </span>
                                Ubah Password
                            </button>
                            <button @click="activeTab = 'danger-zone'"
                                :class="activeTab === 'danger-zone' ? 'bg-red-50 text-red-700 border-red-100/30' : 'text-slate-600 hover:bg-red-50/50 border-transparent'"
                                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold border transition-all text-left focus:outline-none">
                                <span :class="activeTab === 'danger-zone' ? 'text-red-500' : 'text-slate-400'" class="flex items-center justify-center shrink-0">
                                    <x-lucide-trash-2 class="w-5 h-5" />
                                </span>
                                Hapus Akun
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">

            <div x-show="activeTab === 'personal-info'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                id="personal-info" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div x-show="activeTab === 'update-password'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-cloak
                id="update-password" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div x-show="activeTab === 'danger-zone'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-cloak
                id="danger-zone" class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                <div class="p-6 sm:p-8 bg-red-50/10">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
