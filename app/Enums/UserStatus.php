<?php

namespace App\Enums;

enum UserStatus: int
{
    case NEW = 0;
    case ACTIVE = 1;
    case BLOCKED = 2;
    case DELETED = 3;

    /* **************************************** Public **************************************** */
    public function label() : string
    {
        return match ($this) {
            self::NEW => __('user.status.new'),
            self::ACTIVE => __('user.status.active'),
            self::BLOCKED => __('user.status.blocked'),
            self::DELETED => __('user.status.deleted')
        };
    }
}
