<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    // Konstanta Kondisi Aset
    public const KONDISI_BAIK         = 'Baik';
    public const KONDISI_RUSAK_RINGAN = 'Rusak Ringan';
    public const KONDISI_RUSAK_BERAT  = 'Rusak Berat';

    public static function kondisiList(): array
    {
        return [self::KONDISI_BAIK, self::KONDISI_RUSAK_RINGAN, self::KONDISI_RUSAK_BERAT];
    }

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

    public function deviceName(): BelongsTo
    {
        return $this->belongsTo(DeviceName::class, 'device_name_id');
    }

    public function pcReport(): BelongsTo
    {
        return $this->belongsTo(PcReport::class, 'mac_address', 'mac_address');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Peminjaman
    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(AssetLoan::class)->where('status', AssetLoan::STATUS_ACTIVE);
    }

    public function pendingLoan(): HasOne
    {
        return $this->hasOne(AssetLoan::class)->where('status', AssetLoan::STATUS_PENDING);
    }

    //Helper Peminjaman
    public function isOnLoan(): bool
    {
        return $this->activeLoan()->exists();
    }

    public function isPendingLoan(): bool
    {
        return $this->pendingLoan()->exists();
    }

    public function activeBorrower(): ?User
    {
        $loan = $this->activeLoan;
        return $loan ? $loan->borrower : null;
    }

    // Scope untuk filter pencarian aset.
    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        $query->when($filters['device_name_id'] ?? null, function ($q, $deviceId) {
            $q->where('device_name_id', $deviceId);
        });

        $query->when($filters['room'] ?? null, function ($q, $roomSlug) {
            $q->whereHas('room', fn($r) => $r->where('slug', $roomSlug));
        });

        $query->when($filters['filter_linked'] ?? null, function ($q, $filterLinked) {
            match ($filterLinked) {
                'yes'   => $q->whereHas('pcReport'),
                'no'    => $q->whereDoesntHave('pcReport'),
                default => null,
            };
        });

        $query->when($filters['filter_bmn'] ?? null, function ($q, $filterBmn) {
            if ($filterBmn === 'yes') {
                $q->whereNotNull('bmn_number')->where('bmn_number', '!=', '');
            } elseif ($filterBmn === 'no') {
                $q->where(fn($inner) => $inner->whereNull('bmn_number')->orWhere('bmn_number', ''));
            }
        });

        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('bmn_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('deviceName', fn($d) => $d->where('brand', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        });

        return $query;
    }

    public function scopeFilterByDate(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        return $query->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                     ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
    }
}
