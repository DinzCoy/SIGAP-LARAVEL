<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'device_name_id',
        'bmn_number',
        'brand',
        'serial_number',
        'mac_address',
        'room_id',
        'user_id',
        'allocated_at',
        'status_kondisi',
    ];

    protected $casts = [
        'allocated_at' => 'date',
    ];

    public function deviceName(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeviceName::class, 'device_name_id');
    }

    public function pcReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PcReport::class, 'mac_address', 'mac_address');
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
