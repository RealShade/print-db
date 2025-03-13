<?php

namespace App\Enums;

enum UserPermission: string
{

    case PRINT_LOG_ACCESS = 'print_log.access';

    /* **************************************** Public **************************************** */
    public function label() : string
    {
        return match ($this) {
            self::PRINT_LOG_ACCESS => __('user.permissions.print_log.access'),
        };
    }

    public function id() : int
    {
        return match ($this) {
            self::PRINT_LOG_ACCESS => 1,
        };
    }

}
