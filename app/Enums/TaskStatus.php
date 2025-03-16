<?php

namespace App\Enums;

enum TaskStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
    case PRINTED = 'printed';
    case COMPLETED = 'completed';

    /* **************************************** Public **************************************** */
    public function id() : int
    {
        return match ($this) {
            self::NEW => 1,
            self::IN_PROGRESS => 2,
            self::CANCELLED => 3,
            self::PRINTED => 4,
            self::COMPLETED => 5,
        };
    }

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
