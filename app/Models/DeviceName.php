<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceName extends Model
{
    protected $fillable = ['brand', 'name', 'type', 'quantity', 'procurement_date', 'description', 'image'];

    protected $casts = [
        'procurement_date' => 'date',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'device_name_id');
    }
}
