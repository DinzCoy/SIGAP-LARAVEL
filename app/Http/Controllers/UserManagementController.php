<?php

namespace App\Http\Controllers;

// ini controller buat ngatur anak buah, ganti password, sampe pecat orang (hapus user)
// cuma role Admnin (id 2) yang boleh narikan di sini!

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{

    // spill semua user yang ada di database
    public function index(Request $request)
    {
        // lu bukan admin? cabut sana, jangan lancang!
        if ((int) session('active_role_id') !== User::ROLE_ADMIN) {
            abort(403, 'Panel ini rahasia, cuma Admin yang boleh masuk!');
        }

        $query = User::with('roles');

        // fitur search biar ga pusing nyari user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = (clone $query)->count() ?: 10;
        }

        $users = $query->with('roles')->orderBy('name')->paginate($perPage)->withQueryString()->onEachSide(1);
        $roles = Role::orderBy('id')->get();

        return view('users.index', compact('users', 'roles'));
    }

    // buat bikin akun baru biar bisa login
    public function store(Request $request)
    {
        // lu bukan admin? jangan ngimpi bisa bikin user baru
        if ((int) session('active_role_id') !== User::ROLE_ADMIN) {
            abort(403, 'Eits, cuma Admin yang bisa mendaftarkan warga baru!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', Password::min(8)],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // pastiin role bawaan juga nempel
            $user->roles()->sync($this->ensureDefaultRole($request->roles));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Akun pengguna berhasil dibuat.');
    }

    // update info user kalau ada yg ganti nama atau ganti role
    public function update(Request $request, User $user)
    {
        // edit role & profil itu hak eksklusif Admin
        if ((int) session('active_role_id') !== User::ROLE_ADMIN) {
            abort(403, 'Waduh, lu ga punya wewenang buat ngedit data orang lain!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $rolesToSync = $request->roles;

        $warning = null;
        // proteksi biar ga bunuh diri (hapus role admin sendiri ampe ga ada admin sisa)
        if ($user->hasRole(User::ROLE_ADMIN) && !in_array(User::ROLE_ADMIN, $rolesToSync, true)) {
            if ($this->countAdmins() <= 1) {
                $warning = 'User berhasil diupdate. Namun, Role Admin gagal dihapus karena minimal harus ada 1 Admin di sistem.';
                $rolesToSync[] = User::ROLE_ADMIN;
            }
        }

        DB::beginTransaction();
        try {
            $user->update($data);
            $user->roles()->sync($this->ensureDefaultRole($rolesToSync));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        if ($warning) {
            return redirect()->back()->with('error', $warning);
        }

        return redirect()->back()->with('success', 'Data akun dan role pengguna berhasil diperbarui.');
    }

    // reset password akun ke sandi default "password" — buat admin kalau user lupa sandi
    public function resetPassword(User $user)
    {
        // hak eksklusif Admin, jangan coba-coba kalau bukan Admin!
        if ((int) session('active_role_id') !== User::ROLE_ADMIN) {
            abort(403, 'Akses ditolak! Hanya Admin yang bisa mereset password.');
        }

        $user->update([
            'password' => Hash::make('password'),
        ]);

        return redirect()->back()->with('success', 'Password akun ' . $user->name . ' berhasil direset ke sandi default.');
    }

    // pecat user dari sistem biar ga bisa macem-macem lagi
    public function destroy(User $user)
    {
        // cuma Admin yang pegang tombol "Delete"
        if ((int) session('active_role_id') !== User::ROLE_ADMIN) {
            abort(403, 'Akses ditolak! Lu bukan Admin, jangan main hapus aja.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole(User::ROLE_ADMIN) && $this->countAdmins() <= 1) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus admin satu-satunya di sistem.');
        }

        try {
            $user->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus pengguna karena memiliki data riwayat (tiket/laporan) yang terkait di dalam sistem.');
            }
            return redirect()->back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus.');
    }

    private function countAdmins(): int
    {
        return User::withRole(User::ROLE_ADMIN)->count();
    }

    private function ensureDefaultRole(array $roles): array
    {
        if (!in_array(User::ROLE_USER, $roles, true)) {
            $roles[] = User::ROLE_USER;
        }

        return array_unique($roles);
    }
}
