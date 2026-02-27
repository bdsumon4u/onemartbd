<?php

namespace App\Enums;

enum OrderStatus: int
{
    case Hold = 0;
    case Delivered = 1;
    case Processing = 2;
    case PendingPayment = 3;
    case Cancelled = 4;
    case PendingInvoice = 5;
    case OnDelivery = 6;
    case PendingReturn = 7;
    case Courier = 8;
    case NoResponse = 9;
    case Invoiced = 10;
    case Return = 11;
    case Incomplete = 12;
    case Confirmed = 13;
    case StockOut = 14;
    case PartialDelivery = 15;
    case Lost = 16;
    case PaidReturn = 17;
    case Exchange = 18;

    public function label(): string
    {
        return match ($this) {
            self::Hold => 'Hold',
            self::Delivered => 'Delivered',
            self::Processing => 'Processing',
            self::PendingPayment => 'Pending Payment',
            self::Cancelled => 'Cancelled',
            self::PendingInvoice => 'Pending Invoice',
            self::OnDelivery => 'On Delivery',
            self::PendingReturn => 'Pending Return',
            self::Courier => 'Courier',
            self::NoResponse => 'No Response',
            self::Invoiced => 'Invoiced',
            self::Return => 'Return',
            self::Incomplete => 'Incomplete',
            self::Confirmed => 'Confirmed',
            self::StockOut => 'Stock Out',
            self::PartialDelivery => 'Partial Delivery',
            self::Lost => 'Lost',
            self::PaidReturn => 'Paid Return',
            self::Exchange => 'Exchange',
        };
    }

    public static function labelsToValues(): array
    {
        $mapping = [];
        foreach (self::cases() as $status) {
            $mapping[$status->label()] = $status->value;
        }

        return $mapping;
    }

    /**
     * Check if the order is eligible for return receipt.
     */
    public function isEligibleForReturn(): bool
    {
        return in_array($this, self::preReturnStages(), true);
    }

    public static function preReturnStages(): array
    {
        return [self::OnDelivery, self::PendingReturn, self::Courier];
    }

    public function variant(): string
    {
        return match ($this) {
            self::Hold => 'warning',
            self::Delivered => 'success',
            self::Processing => 'info',
            self::PendingPayment => 'secondary',
            self::Cancelled => 'danger',
            self::PendingInvoice => 'warning',
            self::OnDelivery => 'primary',
            self::PendingReturn => 'danger',
            self::Courier => 'warning',
            self::NoResponse => 'warning',
            self::Invoiced => 'warning',
            self::Return => 'success',
            self::Incomplete => 'info',
            self::Confirmed => 'primary',
            self::StockOut => 'secondary',
            self::PartialDelivery => 'warning',
            self::Lost => 'dark',
            self::PaidReturn => 'success',
            self::Exchange => 'info',
        };
    }
}
