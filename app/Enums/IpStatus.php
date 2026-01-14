<?php

namespace App\Enums;

enum IpStatus: int
{
    case Allowed = 0;
    case Blocked = 1;
}
