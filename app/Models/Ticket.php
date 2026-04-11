<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;
use App\Models\User;
use App\Models\TicketReply;

class Ticket extends Model
{
    protected $fillable = [
        'type',
        'asset_id',
        'reported_by',
        'technician_id',
        'title',
        'description',
        'status',
        'priority',
        'estimated_cost',
    ];

    public function asset(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reporter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function technician(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketReply::class);
    }
}
