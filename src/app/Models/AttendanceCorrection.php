<?php

namespace App\Models;

use App\Enums\CorrectionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_clock_in',
        'requested_clock_out',
        'requested_note',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_clock_in' => 'datetime',
            'requested_clock_out' => 'datetime',
            'status' => CorrectionStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    // ========================
    // リレーション
    // ========================

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function breakCorrections(): HasMany
    {
        return $this->hasMany(BreakCorrection::class, 'correction_id');
    }

    // ========================
    // スコープ
    // ========================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', CorrectionStatus::Pending);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', CorrectionStatus::Approved);
    }

    // ========================
    // ステータス判定
    // ========================

    public function isPending(): bool
    {
        return $this->status === CorrectionStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === CorrectionStatus::Approved;
    }
}
