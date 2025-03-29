<?php

namespace App\Enums;

enum TaskStatus: int
{
    case NEW = 0;
    case IN_PROGRESS = 1;
    case CANCELLED = 2;
    case PRINTED = 3;
    case COMPLETED = 4;

    /* **************************************** Public **************************************** */
    public function label() : string
    {
        return match ($this) {
            self::NEW => __('task.enum.status.new'),
            self::IN_PROGRESS => __('task.enum.status.in_progress'),
            self::CANCELLED => __('task.enum.status.cancelled'),
            self::PRINTED => __('task.enum.status.printed'),
            self::COMPLETED => __('task.enum.status.completed'),
        };
    }
}
