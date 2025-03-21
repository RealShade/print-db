<?php

namespace App\Enums;

enum PrinterStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case DELETED = 3;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('printer.status.active'),
            self::INACTIVE => __('printer.status.inactive'),
            self::DELETED => __('printer.status.deleted'),
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::ACTIVE => 'active',
            self::INACTIVE => 'inactive',
            self::DELETED => 'deleted',
        };
    }
}
