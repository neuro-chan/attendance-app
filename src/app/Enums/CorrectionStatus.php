<?php

namespace App\Enums;

enum CorrectionStatus: int
{
    case Pending  = 0;
    case Approved = 1;
}
