<?php

namespace App\Enums;

enum PrintJobStatus: int
{
    const string LANG_PREFIX = 'enum.print_job_status.';

    case PRINTING = 0; // Друкується
    case COMPLETED = 1; // Завершено
    case CANCELLED = 2; // Скасовано
    case UNKNOWN = 3; // Невідомо

    public function label(): string
    {
        return match ($this) {
            self::PRINTING => __('enum.print_job.status.printing'),
            self::COMPLETED => __('enum.print_job.status.completed'),
            self::CANCELLED => __('enum.print_job.status.cancelled'),
            self::UNKNOWN => __('enum.print_job.status.unknown'),
        };
    }
}
