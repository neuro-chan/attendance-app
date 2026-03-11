<?php

namespace App\Enums;

enum CorrectionStatus: int
{
    case Pending  = 0;
    case Approved = 1;

    public function label(): string
    {
        return match ($this) {
            self::Pending  => '承認待ち',
            self::Approved => '承認済み',
        };
    }
}
