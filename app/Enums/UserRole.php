<?php

namespace App\Enums;

enum UserRole: string
{

    case ADMIN = 'admin';
    case USER = 'user';

    /* **************************************** Public **************************************** */
    public function id() : int
    {
        return match ($this) {
            self::ADMIN => 1,
            self::USER => 2,
        };
    }

    public function label() : string
    {
        return match ($this) {
            self::ADMIN => __('user.roles.admin'),
            self::USER => __('user.roles.user'),
        };
    }

}
