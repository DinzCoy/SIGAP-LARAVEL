<x-app-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8 animate-fade-in pb-12">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight flex items-center gap-3">
                    <div class="p-2.5 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100/50 rounded-xl shadow-sm text-bps-blue">
                        <x-lucide-users class="w-6 h-6 stroke-[2.5]" />
                    </div>
                    Manajemen Pengguna
                </h1>
                <p class="mt-2 text-sm text-gray-500 max-w-2xl leading-relaxed">
                    Pusat otorisasi akses SIGAP. Kelola tingkat akses dan peran setiap personel yang mendaftar dalam sistem.
                </p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="openUserCreateModal()" class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-all bg-gray-900 rounded-xl border border-gray-800 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 shadow-[0_4px_14px_0_rgba(0,0,0,0.1)] hover:shadow-[0_6px_20px_rgba(0,0,0,0.15)] hover:-translate-y-0.5 overflow-hidden">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    <x-lucide-user-plus class="w-4 h-4 mr-2" />
                    Tambah Akun Baru
                </button>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="p-4 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200/60 text-emerald-800 rounded-xl flex items-center gap-3 shadow-sm transform transition-all duration-300">
                <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-500 shrink-0" />
                <span class="text-sm font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-700">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 class="p-4 bg-red-50/80 backdrop-blur-sm border border-red-200/60 text-red-800 rounded-xl flex items-center gap-3 shadow-sm transform transition-all duration-300">
                <x-lucide-alert-circle class="w-5 h-5 text-red-500 shrink-0" />
                <span class="text-sm font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif
        @if($errors->any())
            <div x-data="{ show: true }" x-show="show"
                 class="p-4 bg-red-50/80 backdrop-blur-sm border border-red-200/60 text-red-800 rounded-xl flex items-start gap-3 shadow-sm">
                <x-lucide-alert-triangle class="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                <div class="flex-1">
                    <span class="text-sm font-semibold">Terdapat kendala validasi data:</span>
                    <ul class="mt-1.5 list-disc list-inside text-sm text-red-700/80 space-y-0.5">
                        @foreach($errors->all() as $validationError)
                            <li>{{ $validationError }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700 p-1">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <div class="bg-white rounded-2xl p-6 border border-gray-200/60 shadow-sm flex flex-col justify-center relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-100 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1">Total Entitas</p>
                <div class="flex items-baseline gap-2 relative z-10">
                    <span class="text-3xl font-bold tracking-tight text-gray-900">{{ $users->total() }}</span>
                    <span class="text-sm font-medium text-gray-400">Akun Terdaftar</span>
                </div>
            </div>

            <div class="md:col-span-3 bg-white rounded-2xl p-6 border border-gray-200/60 shadow-sm flex flex-col justify-center">
                <form id="filterForm" action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <x-lucide-search class="h-4 w-4 text-gray-400 group-focus-within:text-bps-blue transition-colors" />
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="block w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white focus:border-bps-blue focus:ring-4 focus:ring-blue-100/50 text-sm transition-all placeholder-gray-400 text-gray-800"
                               placeholder="Pencarian nama, email, atau username...">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-white border border-gray-200 shadow-sm text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center gap-2">
                        <x-lucide-filter class="w-4 h-4 text-gray-500" />
                        Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-red-50 border border-red-100 text-red-600 rounded-xl text-sm font-semibold hover:bg-red-100 hover:border-red-200 transition-all flex items-center justify-center gap-2">
                            <x-lucide-x-circle class="w-4 h-4" />
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Menampilkan
                <span class="font-bold text-gray-700">{{ $users->firstItem() ?? 0 }}</span>
                –
                <span class="font-bold text-gray-700">{{ $users->lastItem() ?? 0 }}</span>
                dari
                <span class="font-bold text-bps-blue">{{ $users->total() }}</span> total pengguna
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden relative">
            <div class="overflow-x-auto min-h-[400px]">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50/80 border-b border-gray-200/60 text-xs text-gray-500 uppercase tracking-wider font-semibold">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center w-16">No</th>
                            <th scope="col" class="px-6 py-4">Profil Pengguna</th>
                            <th scope="col" class="px-6 py-4">Status & Otorisasi</th>
                            <th scope="col" class="px-6 py-4 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-gray-50/60 transition-colors group">
                                <td class="px-6 py-5 text-center text-gray-400 font-mono text-sm">
                                    {{ str_pad($users->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">

                                        @php
                                            $initials = strtoupper(substr($user?->name ?? 'U', 0, 2));
                                            $hue = crc32($user?->email ?? 'unknown') % 360;
                                            $safeName = addslashes($user?->name ?? 'User');
                                        @endphp
                                        <div class="relative w-12 h-12 flex-shrink-0">
                                            <div class="w-full h-full rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm ring-2 ring-white"
                                                 {!! 'style="background: linear-gradient(135deg, hsl(' . $hue . ', 70%, 60%), hsl(' . ($hue + 30) . ', 70%, 50%));"' !!}>
                                                {{ $initials }}
                                            </div>

                                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                        </div>

                                        <div>
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <h4 class="text-sm font-semibold text-gray-900 leading-tight">
                                                    {{ $user?->name ?? 'Unknown User' }}
                                                </h4>
                                                @if(auth()->id() == $user->id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase border border-gray-200">
                                                        Anda
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 font-medium">{{ $user->email }}</p>
                                            <p class="text-[11px] text-gray-400 font-mono mt-0.5 tracking-tight flex items-center gap-1">
                                                <x-lucide-fingerprint class="w-3 h-3" />
                                                {{ $user->username }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2 items-center">
                                        @foreach($user->roles as $role)
                                            @php
                                                $badgeStyle = match($role->id) {
                                                    1 => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-emerald-100',
                                                    2 => 'bg-red-50 text-red-700 border-red-200 ring-red-100',
                                                    3 => 'bg-amber-50 text-amber-700 border-amber-200 ring-amber-100',
                                                    4 => 'bg-blue-50 text-blue-700 border-blue-200 ring-blue-100',
                                                    5 => 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-indigo-100',
                                                    6 => 'bg-gray-50 text-gray-600 border-gray-200 ring-gray-100',
                                                    7 => 'bg-orange-50 text-orange-700 border-orange-200 ring-orange-100',
                                                    default => 'bg-slate-50 text-slate-700 border-slate-200 ring-slate-100'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold border {{ $badgeStyle }} rounded-md shadow-sm">
                                                {{ $role?->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                            onclick="prepareUserEditModal(this)"
                                            data-user="{{ json_encode($user) }}"
                                            data-roles="{{ json_encode($user->roles->pluck('id')) }}"
                                            class="p-2 text-blue-500 bg-blue-50 hover:text-blue-700 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200 hover:border-blue-300 shadow-sm"
                                            title="Edit Pengguna">
                                            <x-lucide-edit class="w-4 h-4" />
                                        </button>

                                        <form id="reset-pw-{{ $user->id }}" action="{{ route('users.resetPassword', $user->id) }}" method="POST" class="hidden">
                                            @csrf
                                        </form>
                                        <button type="button"
                                            onclick="confirmResetPassword('reset-pw-{{ $user->id }}', '{{ $safeName }}')"
                                            class="p-2 text-orange-500 bg-orange-50 hover:text-orange-700 hover:bg-orange-100 rounded-lg transition-colors border border-orange-200 hover:border-orange-300 shadow-sm"
                                            title="Reset Password ke Default">
                                            <x-lucide-key-round class="w-4 h-4" />
                                        </button>

                                        @if(auth()->id() !== $user->id)
                                        <form id="del-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('del-{{ $user->id }}', 'Yakin menghapus akun {{ $safeName }} secara permanen?')" class="p-2 text-red-500 bg-red-50 hover:text-red-700 hover:bg-red-100 rounded-lg transition-colors border border-red-200 hover:border-red-300 shadow-sm" title="Hapus Pengguna">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </form>
                                        @else
                                        <div class="p-2 text-gray-200 border border-gray-100 rounded-lg cursor-not-allowed" title="Tidak bisa menghapus akun sendiri">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-24 text-center">
                                    <div class="max-w-sm mx-auto flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                            <x-lucide-users-2 class="w-8 h-8 text-gray-300" />
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900 mb-1">Entitas Tidak Ditemukan</h3>
                                        <p class="text-sm text-gray-500">Kami tidak dapat menemukan pengguna yang sesuai dengan parameter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200/60 bg-gray-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <label for="per_page_bottom" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tampilkan:</label>
                    <select name="per_page" id="per_page_bottom" form="filterForm" onchange="this.form.submit()" class="block py-1.5 pl-3 pr-10 rounded-md border-gray-300 shadow-sm focus:border-bps-blue focus:ring-bps-blue sm:text-sm bg-white">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>
                @if($users->hasPages())
                    <div class="w-full md:w-auto">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('createModal')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 animate-slide-up">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="bg-gradient-to-r from-bps-blue to-blue-800 px-6 py-5 text-white flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-bold flex items-center gap-2.5" id="modal-title">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            Pendaftaran Akun Baru
                        </h3>
                        <button type="button" onclick="closeModal('createModal')" class="text-blue-100 hover:text-white transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-6 sm:px-8 space-y-5">
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Lengkapi formulir di bawah ini untuk menambahkan entitas ke dalam sistem.</p>

                        <div class="space-y-4">

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="name" required class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm text-gray-900 placeholder-gray-400" placeholder="Ketik nama di sini...">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">
                                        Username Unik <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="fingerprint" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="username" required class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm text-gray-900 placeholder-gray-400" placeholder="contoh: budi_123">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">
                                        Email Dinas <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </div>
                                        <input type="email" name="email" required class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm text-gray-900 placeholder-gray-400" placeholder="mail@bps.go.id">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">
                                    Kredensial Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i data-lucide="lock" class="w-4 h-4"></i>
                                    </div>
                                    <input type="password" name="password" required minlength="8" class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm font-mono placeholder-gray-400" placeholder="Minimal 8 karakter">
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-2.5">
                                    <label class="block text-xs font-semibold text-gray-700">Otorisasi Level (Roles)</label>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        Pilih minimal satu
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50/50 p-4 border border-gray-200/60 rounded-xl">
                                    @foreach($roles as $role)
                                        @php
                                            $roleIcon = match($role->id) {
                                                1 => 'briefcase',
                                                2 => 'shield',
                                                3 => 'wrench',
                                                4 => 'box',
                                                5 => 'door-open',
                                                6 => 'user',
                                                7 => 'users',
                                                default => 'award'
                                            };
                                        @endphp

                                        @if($role->id == 6)

                                            <div class="relative flex items-center gap-3 p-3 bg-blue-50/30 border border-bps-blue rounded-xl select-none h-16 ring-1 ring-bps-blue">
                                                <input type="hidden" name="roles[]" value="6" id="create_role_6">

                                                <div class="w-4 h-4 rounded border border-bps-blue flex items-center justify-center bg-bps-blue text-white">
                                                    <i data-lucide="check" class="w-3 h-3 stroke-[3.5]"></i>
                                                </div>

                                                <i data-lucide="{{ $roleIcon }}" class="w-4 h-4 text-bps-blue"></i>
                                                <span class="text-xs font-semibold text-gray-950 uppercase tracking-tight">{{ $role->name }}</span>
                                            </div>
                                        @else
                                            <label class="relative flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-bps-blue hover:bg-blue-50/30 transition-all select-none group has-[:checked]:border-bps-blue has-[:checked]:bg-blue-50/30 has-[:checked]:ring-1 has-[:checked]:ring-bps-blue h-16">
                                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="peer sr-only">

                                                <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center bg-white transition-all group-hover:border-bps-blue peer-checked:bg-bps-blue peer-checked:border-bps-blue text-white">
                                                    <i data-lucide="check" class="w-3 h-3 hidden group-has-[:checked]:block stroke-[3.5]"></i>
                                                </div>

                                                <i data-lucide="{{ $roleIcon }}" class="w-4 h-4 text-gray-400 group-hover:text-bps-blue peer-checked:text-bps-blue transition-colors"></i>
                                                <span class="text-xs font-semibold text-gray-700 group-hover:text-gray-900 peer-checked:text-gray-950 transition-colors uppercase tracking-tight">{{ $role->name }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-5 sm:px-8 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-bps-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-bps-blue focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Daftarkan Pengguna
                        </button>
                        <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all duration-200 flex items-center gap-2 bg-white">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Batalkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('editModal')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 animate-slide-up">
                <form id="editForm" method="POST">
                    @csrf @method('PUT')

                    <div class="bg-gradient-to-r from-bps-blue to-blue-800 px-6 py-5 text-white flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-bold flex items-center gap-2.5" id="modal-title">
                            <i data-lucide="user-cog" class="w-5 h-5"></i>
                            Edit Profil Pengguna
                        </h3>
                        <button type="button" onclick="closeModal('editModal')" class="text-blue-100 hover:text-white transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-6 sm:px-8 space-y-5">
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Perbarui identitas, kredensial, atau otorisasi level pengguna ini.</p>

                        <div class="space-y-4">

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="name" id="edit_name" required class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm text-gray-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">Username</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="fingerprint" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="username" id="edit_username" required class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm text-gray-900">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-2">Email</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </div>
                                        <input type="email" name="email" id="edit_email" required class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm text-gray-900">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 border-dashed">
                                <label class="block text-xs font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                                    <i data-lucide="key" class="w-3.5 h-3.5 text-gray-500"></i>
                                    Ubah Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i data-lucide="lock" class="w-4 h-4"></i>
                                    </div>
                                    <input type="password" name="password" minlength="8" placeholder="Kosongkan jika password tidak ingin diubah"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-bps-blue focus:outline-none transition-all duration-200 text-sm font-mono placeholder-gray-400">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-3">Penugasan Level Akses (Roles)</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50/50 p-4 border border-gray-200/60 rounded-xl">
                                    @foreach($roles as $role)
                                        @php
                                            $roleIcon = match($role->id) {
                                                1 => 'briefcase',
                                                2 => 'shield',
                                                3 => 'wrench',
                                                4 => 'box',
                                                5 => 'door-open',
                                                6 => 'user',
                                                7 => 'users',
                                                default => 'award'
                                            };
                                        @endphp

                                        @if($role->id == 6)

                                            <div class="relative flex items-center gap-3 p-3 bg-blue-50/30 border border-bps-blue rounded-xl select-none h-16 ring-1 ring-bps-blue">
                                                <input type="hidden" name="roles[]" value="6" id="edit_role_6">

                                                <div class="w-4 h-4 rounded border border-bps-blue flex items-center justify-center bg-bps-blue text-white">
                                                    <i data-lucide="check" class="w-3 h-3 stroke-[3.5]"></i>
                                                </div>

                                                <i data-lucide="{{ $roleIcon }}" class="w-4 h-4 text-bps-blue"></i>
                                                <span class="text-xs font-semibold text-gray-950 uppercase tracking-tight">{{ $role->name }}</span>
                                            </div>
                                        @else
                                            <label class="relative flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-bps-blue hover:bg-blue-50/30 transition-all select-none group has-[:checked]:border-bps-blue has-[:checked]:bg-blue-50/30 has-[:checked]:ring-1 has-[:checked]:ring-bps-blue h-16">
                                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="edit_role_{{ $role->id }}" class="peer sr-only">

                                                <div class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center bg-white transition-all group-hover:border-bps-blue peer-checked:bg-bps-blue peer-checked:border-bps-blue text-white">
                                                    <i data-lucide="check" class="w-3 h-3 hidden group-has-[:checked]:block stroke-[3.5]"></i>
                                                </div>

                                                <i data-lucide="{{ $roleIcon }}" class="w-4 h-4 text-gray-400 group-hover:text-bps-blue peer-checked:text-bps-blue transition-colors"></i>
                                                <span class="text-xs font-semibold text-gray-700 group-hover:text-gray-900 peer-checked:text-gray-950 transition-colors uppercase tracking-tight">{{ $role?->name }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-5 sm:px-8 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-bps-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue transition-all duration-200 flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bps-blue transition-all duration-200 flex items-center gap-2 bg-white">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Batalkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

        function confirmResetPassword(formId, userName) {
            Swal.fire({
                title: 'Reset Password?',
                html: `Password akun <strong>${userName}</strong> akan direset ke sandi default: <br><br><code style="background:#fff7ed;padding:6px 16px;border-radius:8px;font-size:1.1em;font-weight:bold;color:#c2410c;letter-spacing:2px;">password</code><br><br><small style="color:#6b7280;">Pengguna bisa mengganti sandi sendiri setelah login.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '🔑 Ya, Reset!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

</x-app-layout>
