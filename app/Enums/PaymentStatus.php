<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case Unpaid = 0;
    case Partial = 1;
    case Paid = 2;

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Unpaid',
            self::Partial => 'Partial',
            self::Paid => 'Paid',
        };
    }
}
