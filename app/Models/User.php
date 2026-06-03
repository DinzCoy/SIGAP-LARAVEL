<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_PIMPINAN = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_TEKNISI = 3;
    public const ROLE_PENGELOLA_ASET = 4;
    public const ROLE_PIC_RUANGAN = 5;
    public const ROLE_USER = 6;
    public const ROLE_KETUA_TIM = 7;

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

    public function isAdminOrPengelola(): bool
    {

        if (request()->is('api/*') || request()->expectsJson()) {
            return $this->roles()->whereIn('roles.id', [self::ROLE_ADMIN, self::ROLE_PENGELOLA_ASET])->exists();
        }

        $activeRole = session('active_role_id');
        if ($activeRole) {
            return in_array((int)$activeRole, [self::ROLE_ADMIN, self::ROLE_PENGELOLA_ASET]);
        }

        return $this->roles()->whereIn('roles.id', [self::ROLE_ADMIN, self::ROLE_PENGELOLA_ASET])->exists();
    }

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
        'fcm_token',
        'photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function activeRoleId(): ?int
    {
        return session('active_role_id');
    }

    public function hasRole(int $roleId): bool
    {
        return $this->roles()->where('roles.id', $roleId)->exists();
    }

    public function ticketsReported(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'reported_by');
    }

    public function ticketsAssigned(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'technician_id');
    }

    public function ticketsAsLeader(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'team_leader_id');
    }

    public function scopeWithRole(Builder $query, int $roleId): Builder
    {
        return $query->whereHas('roles', fn($q) => $q->where('roles.id', $roleId));
    }
}
