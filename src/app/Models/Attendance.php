<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    // ========================
    // リレーション
    // ========================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes(): HasMany
    {
        return $this->hasMany(BreakTime::class);
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    // ========================
    // スコープ
    // ========================

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('work_date', $year)
            ->whereMonth('work_date', $month);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('work_date', $date);
    }

    // ========================
    // 属性・判定
    // ========================

    /**
     * 打刻状況から勤怠ステータスを判定する
     * 未出勤 → 出勤中 → 休憩中 → 退勤済 の順で判定
     */
    public function getStatusAttribute(): AttendanceStatus
    {
        if (is_null($this->clock_in)) {
            return AttendanceStatus::OffWork;
        }
        if (! is_null($this->clock_out)) {
            return AttendanceStatus::Finished;
        }
        if ($this->breakTimes()->whereNull('break_end')->exists()) {
            return AttendanceStatus::OnBreak;
        }

        return AttendanceStatus::Working;
    }

    /**
     * 承認待ちの修正申請が存在するか判定
     */
    public function hasPendingRequest(): bool
    {
        return $this->corrections()->pending()->exists();
    }
}
