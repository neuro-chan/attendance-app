<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeHelper
{
    public static function combineDateAndTime(Carbon $date, ?string $time): ?string
    {
        if (! $time) {
            return null;
        }

        return $date->format('Y-m-d').' '.$time;
    }
}
