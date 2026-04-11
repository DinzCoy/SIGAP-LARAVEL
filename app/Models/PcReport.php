<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hostname',
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
            'is_trouble' => 'boolean',
        ];
    }

    /**
     * Get the installed software for the PC.
     */
    public function installedSoftware()
    {
        return $this->hasMany(InstalledSoftware::class);
    }

    /**
     * Get the asset data associated with the PC.
     */
    public function asset()
    {
        return $this->hasOne(Asset::class, 'mac_address', 'mac_address');
    }
}