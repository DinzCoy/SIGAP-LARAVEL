<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMovementLog extends Model
{
    protected $fillable = [
        'asset_id',
        'old_user_id',
        'new_user_id',
        'old_room_id',
        'new_room_id',
        'action_type',
        'reason'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function oldUser()
    {
        return $this->belongsTo(User::class, 'old_user_id');
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'new_user_id');
    }

    public function oldRoom()
    {
        return $this->belongsTo(Room::class, 'old_room_id');
    }

    public function newRoom()
    {
        return $this->belongsTo(Room::class, 'new_room_id');
    }
}
