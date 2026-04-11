<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * The role ID that is always enforced as a baseline for all users.
     */
    private const DEFAULT_ROLE_ID = 6;

    /**
     * The Admin role ID. Used to prevent removing the last admin from the system.
     */
    private const ADMIN_ROLE_ID = 2;

    /**
     * Display a listing of users with optional keyword search.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                                       ->orWhere('email', 'like', "%{$search}%")
                                       ->orWhere('username', 'like', "%{$search}%"));
        }

        $users = $query->orderBy('name')->get();
        $roles = Role::orderBy('id')->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created user and assign the specified roles.
     * The default User role is always included regardless of input.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => ['required', Password::min(8)],
            'roles'    => 'required|array|min:1',
            'roles.*'  => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->roles()->sync($this->ensureDefaultRole($request->roles));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Akun pengguna berhasil dibuat.');
    }

    /**
     * Update the specified user's profile information.
     * Password is only updated when explicitly provided.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data akun pengguna berhasil diperbarui.');
    }

    /**
     * Update the roles assigned to the specified user.
     * Prevents removing the Admin role if this user is the only remaining admin.
     */
    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'roles'   => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($user->hasRole(self::ADMIN_ROLE_ID) && !in_array(self::ADMIN_ROLE_ID, $request->roles)) {
            if ($this->countAdmins() <= 1) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus role Admin. Minimal harus ada 1 Admin di sistem.');
            }
        }

        $user->roles()->sync($this->ensureDefaultRole($request->roles));

        return redirect()->back()->with('success', 'Role pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from the system.
     * Prevents self-deletion and deletion of the last remaining admin.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole(self::ADMIN_ROLE_ID) && $this->countAdmins() <= 1) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus admin satu-satunya di sistem.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus.');
    }

    /**
     * Count the total number of users who have the Admin role.
     */
    private function countAdmins(): int
    {
        return User::whereHas('roles', fn ($q) => $q->where('roles.id', self::ADMIN_ROLE_ID))->count();
    }

    /**
     * Ensure the default User role is always included in a given roles array.
     *
     * @param  array  $roles
     * @return array
     */
    private function ensureDefaultRole(array $roles): array
    {
        if (!in_array(self::DEFAULT_ROLE_ID, $roles)) {
            $roles[] = self::DEFAULT_ROLE_ID;
        }

        return $roles;
    }
}
