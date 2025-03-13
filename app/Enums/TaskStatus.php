<?php

namespace App\Enums;

enum TaskStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function id(): int
    {
        return match($this) {
            self::NEW => 1,
            self::IN_PROGRESS => 2,
            self::CANCELLED => 3,
            self::COMPLETED => 4,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::NEW => __('task.status.new'),
            self::IN_PROGRESS => __('task.status.in_progress'),
            self::CANCELLED => __('task.status.cancelled'),
            self::COMPLETED => __('task.status.completed'),
        };
    }
}
