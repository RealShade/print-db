<?php

namespace App\Helpers;

use App\Models\Part;
use App\Models\Task;
use Throwable;

class FilenamePlaceholderHelper
{
    /* **************************************** Static **************************************** */
    public static function generate(Task $task, ?Part $part = null, ?int $count = 1) : string
    {
        $placeholder = '';

        if ($part) {
            $placeholder .= "(pid_{$part->id}(x{$count})_{$task->id})";
        } else {
            $placeholder .= "(tid_{$task->id}(x{$count}))";
        }

        return $placeholder;
    }

    /**
     * @throws Throwable
     */
    public static function generateWithWrapper(?Task $task = null, ?Part $part = null, ?int $count = 1) : string
    {
        return view('components.filename-placeholder', [
            'placeholder' => self::generate($task, $part, $count),
        ])->render();
    }
}
