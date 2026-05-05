<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    // Konstanta Status Peminjaman
    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'asset_id',
        'lender_id',
        'borrower_id',
        'loan_reason',
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

    //Relasi

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

    //Filter Query (Scopes)

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    //Fungsi Pembantu (Helpers)

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->due_date
            && now()->greaterThan($this->due_date);
    }

    /**
     * Cek apakah user berhak menyetujui atau menolak peminjaman ini.
     * Diizinkan: pemilik aset, Admin (role 2), atau Pengelola Aset (role 4).
     */
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

    /**
     * Cek apakah user berhak mengembalikan aset pinjaman ini.
     * Diizinkan: peminjam, pemilik, Admin (role 2), atau Pengelola Aset (role 4).
     */
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