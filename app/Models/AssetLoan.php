<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{

    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_REJECTED = 'rejected';

    public const TYPE_PINJAM = 'pinjam';
    public const TYPE_MUTASI = 'mutasi';

    protected $fillable = [
        'asset_id',
        'lender_id',
        'borrower_id',
        'loan_reason',
        'type',
        'loaned_at',
        'due_date',
        'returned_at',
        'approved_at',
        'rejected_at',
        'status',
    ];

    protected $casts = [
        'loaned_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->due_date
            && now()->greaterThan($this->due_date);
    }

    public function canBeManagedBy(User $user): bool
    {
        $isAdminOrManager = in_array(
            (int) session('active_role_id'),
            [User::ROLE_ADMIN, User::ROLE_PENGELOLA_ASET],
            true
        );

        return ($this->lender_id === $user->id)
            || ($this->lender_id === null && $isAdminOrManager);
    }

    public function canBeReturnedBy(User $user): bool
    {
        $isAdminOrManager = in_array(
            (int) session('active_role_id'),
            [User::ROLE_ADMIN, User::ROLE_PENGELOLA_ASET],
            true
        );

        return ($this->borrower_id === $user->id)
            || ($this->lender_id === $user->id)
            || ($this->lender_id === null && $isAdminOrManager);
    }
}