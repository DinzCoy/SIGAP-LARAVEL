<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Room;

class Ticket extends Model
{
    // Konstanta Status Tiket
    public const STATUS_MENUNGGU_PENGELOLA = 'Menunggu Pengecekan Pengelola';
    public const STATUS_KE_KETUA_TIM = 'Diteruskan ke Ketua Tim';
    public const STATUS_KE_TEKNISI = 'Diteruskan ke Teknisi';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_MENUNGGU_BIAYA = 'Menunggu Persetujuan Biaya';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_SELESAI = 'Selesai';
    public const STATUS_DIBATALKAN = 'Dibatalkan';

    protected $fillable = [
        'type',
        'category',
        'asset_id',
        'reported_by',
        'technician_id',
        'team_leader_id',
        'title',
        'description',
        'status',
        'priority',
        'estimated_cost',
        'room_id',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    //UI Accessors

    //Class CSS untuk badge status tiket.
    //Penggunaan di Blade: {{ $ticket->status_badge_class }}
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status ?? 'Open') {
                'Menunggu Pengecekan Pengelola' => 'bg-orange-50 text-orange-700 border-orange-100',
                'Diteruskan ke Ketua Tim' => 'bg-violet-50 text-violet-700 border-violet-100',
                'Diteruskan ke Teknisi' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                'In Progress' => 'bg-blue-50 text-blue-700 border-blue-100',
                'Menunggu Persetujuan Biaya' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                'Approved' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                'Dibatalkan' => 'bg-red-50 text-red-700 border-red-100',
                default => 'bg-gray-100 text-gray-700 border-gray-200',
            }
        );
    }

    //Class CSS untuk badge prioritas tiket.
    //Penggunaan di Blade: {{ $ticket->priority_badge_class }}
    protected function priorityBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->priority ?? '') {
                'Tinggi' => 'bg-red-50 text-red-600',
                'Sedang' => 'bg-orange-50 text-orange-600',
                default => 'bg-green-50 text-green-600',
            }
        );
    }

    //Class CSS untuk badge tipe tiket (Asset/Layanan).
    //Penggunaan di Blade: {{ $ticket->type_badge_class }}
    protected function typeBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->type ?? '') === 'Asset'
            ? 'bg-blue-50 text-blue-700 border border-blue-100'
            : 'bg-purple-50 text-purple-700 border border-purple-100'
        );
    }

    //Label teks yang ramah pengguna untuk tipe tiket.
    //Penggunaan di Blade: {{ $ticket->type_label }}
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->type ?? '') === 'Asset' ? 'Perbaikan Aset BMN' : 'Bantuan Umum'
        );
    }

    //Nama icon Lucide berdasarkan tipe tiket.
    //Penggunaan di Blade: <x-dynamic-component :component="'lucide-' . $ticket->type_icon" />
    protected function typeIcon(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->type ?? '') === 'Asset' ? 'monitor' : 'wrench'
        );
    }

    //Class CSS untuk badge kategori tiket.
    //Penggunaan di Blade: {{ $ticket->category_badge_class }}
    protected function categoryBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->category ?? '') {
                'Service' => 'bg-indigo-50 text-indigo-700 border border-indigo-100',
                'Troubleshooting' => 'bg-amber-50 text-amber-700 border-amber-100',
                default => 'bg-gray-50 text-gray-700 border border-gray-100',
            }
        );
    }

    //Nama icon Lucide berdasarkan kategori tiket.
    //Penggunaan di Blade: <x-dynamic-component :component="'lucide-' . $ticket->category_icon" />
    protected function categoryIcon(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->category ?? '') {
                'Service' => 'wrench',
                'Troubleshooting' => 'code',
                default => 'help-circle',
            }
        );
    }

    // --- SLA Tracking Accessors ---

    protected function responseTimeHours(): Attribute
    {
        return Attribute::make(
            get: function() {
                if ($this->responded_at) return round($this->created_at->floatDiffInHours($this->responded_at), 1);
                if ($this->status !== self::STATUS_MENUNGGU_PENGELOLA) return round($this->created_at->floatDiffInHours($this->updated_at), 1);
                return round($this->created_at->floatDiffInHours(now()), 1);
            }
        );
    }

    protected function resolutionTimeHours(): Attribute
    {
        return Attribute::make(
            get: function() {
                if ($this->resolved_at) return round($this->created_at->floatDiffInHours($this->resolved_at), 1);
                if (in_array($this->status, [self::STATUS_SELESAI, self::STATUS_DIBATALKAN])) return round($this->created_at->floatDiffInHours($this->updated_at), 1);
                return round($this->created_at->floatDiffInHours(now()), 1);
            }
        );
    }

    protected function isSlaResponseBreached(): Attribute
    {
        return Attribute::make(
            get: function() {
                $limit = match($this->priority) {
                    'Tinggi' => 1,
                    'Sedang' => 4,
                    default => 24,
                };
                return $this->response_time_hours > $limit;
            }
        );
    }

    protected function isSlaResolutionBreached(): Attribute
    {
        return Attribute::make(
            get: function() {
                $limit = match($this->priority) {
                    'Tinggi' => 24,
                    'Sedang' => 48,
                    default => 72,
                };
                return $this->resolution_time_hours > $limit;
            }
        );
    }

    protected function slaStatusBadge(): Attribute
    {
        return Attribute::make(
            get: function() {
                if (in_array($this->status, [self::STATUS_SELESAI, self::STATUS_DIBATALKAN])) {
                    if ($this->is_sla_resolution_breached) {
                        return '<span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded text-[10px] font-bold">Terlambat Selesai</span>';
                    }
                    return '<span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold">Memenuhi SLA</span>';
                }

                if ($this->responded_at) {
                    if ($this->is_sla_resolution_breached) {
                        return '<span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded text-[10px] font-bold animate-pulse">Batas Waktu SLA</span>';
                    }
                    return '<span class="bg-blue-50 text-blue-600 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold">Aman (In Progress)</span>';
                }

                if ($this->is_sla_response_breached) {
                    return '<span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded text-[10px] font-bold animate-pulse">Terlambat Respons</span>';
                }

                return '<span class="bg-orange-50 text-orange-600 border border-orange-200 px-2 py-0.5 rounded text-[10px] font-bold">Menunggu Respons</span>';
            }
        );
    }
    public function scopeFilterByDate(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder
    {
        return $query->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                     ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
    }

    /**
     * Scope: tiket yang masih aktif (belum Selesai atau Dibatalkan).
     * Mengganti pola whereNotIn([STATUS_SELESAI, STATUS_DIBATALKAN]) yang berulang.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_SELESAI, self::STATUS_DIBATALKAN]);
    }
}
