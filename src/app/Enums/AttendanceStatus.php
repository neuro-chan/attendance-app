<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case OffWork  = 'off_work';
    case Working  = 'working';
    case OnBreak  = 'on_break';
    case Finished = 'finished';
}
