<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcReport extends Model
{
    use HasFactory;
    
    public const OFFLINE_THRESHOLD_MINUTES = 5;

    //Kolom yang dapat diisi secara massal.
    protected $fillable = [
        'hostname',
        'username',
        'ip_address',
        'mac_address',
        'room_name',
        'os_name',
        'os_build',
        'total_ram_kb',
        'ram_free_kb',
        'total_disk_b',
        'disk_free_b',
        'disk_status',
        'last_patch',
        'is_trouble',
        'trouble_note',
        'last_seen',
    ];

    //Pengaturan casting tipe data.
    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
            'is_trouble' => 'boolean',
        ];
    }

    //Daftar software yang terinstal di PC.
    public function installedSoftware()
    {
        return $this->hasMany(InstalledSoftware::class);
    }

    //Data aset yang terhubung dengan PC ini.
    public function asset()
    {
        return $this->hasOne(Asset::class, 'mac_address', 'mac_address');
    }

    /**
     * Cek apakah PC sedang offline berdasarkan last_seen.
     */
    public function isOffline(): bool
    {
        if (!$this->last_seen) {
            return true;
        }

        return $this->last_seen->lt(now()->subMinutes(self::OFFLINE_THRESHOLD_MINUTES));
    }

    public function scopeFilterByDate(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        return $query->when($startDate, fn($q) => $q->whereDate('last_seen', '>=', $startDate))
                     ->when($endDate, fn($q) => $q->whereDate('last_seen', '<=', $endDate));
    }

    /**
     * Scope: Hanya ambil PC yang sedang online (last_seen belum lewat threshold).
     */
    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('last_seen', '>=', now()->subMinutes(self::OFFLINE_THRESHOLD_MINUTES));
    }

    /**
     * Scope: Hanya ambil PC yang offline (last_seen lewat threshold atau null).
     */
    public function scopeOffline(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('last_seen', '<', now()->subMinutes(self::OFFLINE_THRESHOLD_MINUTES))
              ->orWhereNull('last_seen');
        });
    }
}