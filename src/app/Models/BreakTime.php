<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'breaktimes';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    protected function casts(): array
    {
        return [
        'break_start' => 'datetime',
        'break_end'   => 'datetime',
        ];
    }

    // Relation
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakCorrections(): HasMany
    {
        return $this->hasMany(BreakCorrection::class, 'break_id');
    }

    // 休憩中判定
    public function isOngoing(): bool
    {
        return is_null($this->break_end);
    }
}
