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
            'clock_in'  => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    // Relation
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


    // Scope
    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('work_date', $year)
            ->whereMonth('work_date', $month);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('work_date', $date);
    }

    // 勤怠ステータス判定
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

    // 申請ステータス判定
    public function hasPendingCorrection(): bool
    {
        return $this->corrections()->pending()->exists();
    }
}
