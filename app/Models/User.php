<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    //Menggunakan trait factory untuk pembuatan data user.
    use HasApiTokens, HasFactory, Notifiable;

    // Role
    public const ROLE_PIMPINAN = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_TEKNISI = 3;
    public const ROLE_PENGELOLA_ASET = 4;
    public const ROLE_PIC_RUANGAN = 5;
    public const ROLE_USER = 6;
    public const ROLE_KETUA_TIM = 7;

    //Mendapatkan nama role berdasarkan ID.
    public static function getRoleName(?int $roleId): string
    {
        return match ($roleId) {
            self::ROLE_PIMPINAN => 'Pimpinan',
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_TEKNISI => 'Teknisi',
            self::ROLE_PENGELOLA_ASET => 'Pengelola Barang',
            self::ROLE_PIC_RUANGAN => 'Pengelola Ruangan',
            self::ROLE_USER => 'User',
            self::ROLE_KETUA_TIM => 'Ketua Tim',
            default => 'Pengguna',
        };
    }

    //Mendapatkan rute dashboard berdasarkan ID Role.
    public static function getDashboardRoute(?int $roleId): ?string
    {
        return match ($roleId) {
            self::ROLE_PIMPINAN => 'pimpinan.dashboard',
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_TEKNISI => 'teknisi.dashboard',
            self::ROLE_PENGELOLA_ASET => 'pengelola_aset.dashboard',
            self::ROLE_PIC_RUANGAN => 'ruangan.dashboard',
            self::ROLE_USER => 'user.dashboard',
            self::ROLE_KETUA_TIM => 'ketua_tim.dashboard',
            default => null,
        };
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    //Kolom yang disembunyikan saat serialisasi data.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //Pengaturan casting tipe data.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Relasi Many-to-Many dengan model Role.
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    //Mendapatkan ID role yang sedang aktif di session.
    public function activeRoleId(): ?int
    {
        return session('active_role_id');
    }

    //Cek apakah user memiliki role tertentu berdasarkan ID.
    public function hasRole(int $roleId): bool
    {
        return $this->roles()->where('roles.id', $roleId)->exists();
    }

    //Tiket yang dilaporkan oleh user ini.
    public function ticketsReported(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'reported_by');
    }

    //Tiket yang ditugaskan ke user ini sebagai teknisi.
    public function ticketsAssigned(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'technician_id');
    }

    //Tiket yang dipimpin oleh user ini sebagai ketua tim.
    public function ticketsAsLeader(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'team_leader_id');
    }

    /**
     * Scope untuk menyaring pengguna yang memiliki ID Role tertentu.
     */
    public function scopeWithRole(Builder $query, int $roleId): Builder
    {
        return $query->whereHas('roles', fn($q) => $q->where('roles.id', $roleId));
    }
}
