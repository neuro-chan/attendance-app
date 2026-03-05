<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_id',
        'break_id',
        'requested_break_start',
        'requested_break_end',
    ];

    protected function casts(): array
    {
        return [
            'requested_break_start' => 'datetime',
            'requested_break_end'   => 'datetime',
        ];
    }

    // Relation
    public function correction(): BelongsTo
    {
        return $this->belongsTo(AttendanceCorrection::class, 'correction_id');
    }

    public function breakTime(): BelongsTo
    {
        return $this->belongsTo(BreakTime::class, 'break_id');
    }

    // 新規追加された休憩か判定
    public function isNewBreak(): bool
    {
        return is_null($this->break_id);
    }
}
