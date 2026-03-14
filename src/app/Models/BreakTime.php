<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BreakTime extends Model
{
    use HasFactory;

    // テーブル名が規約と異なるため明示
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
            'break_end' => 'datetime',
        ];
    }

    // ========================
    // リレーション
    // ========================

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakCorrections(): HasMany
    {
        return $this->hasMany(BreakCorrection::class, 'break_id');
    }

    // ========================
    // 判定
    // ========================

    // break_endがnullの場合は休憩中
    public function isOngoing(): bool
    {
        return is_null($this->break_end);
    }
}
