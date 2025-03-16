<?php

namespace App\Helpers;

use App\Models\Part;
use App\Models\Task;

class FilenamePlaceholderHelper
{
    public static function generate(?Task $task = null, ?Part $part = null): string
    {
        $placeholder = '';

        if ($task) {
            $placeholder .= "[tid_{$task->id}(x1)]";
        }

        if ($part) {
            $placeholder .= "[pid_{$part->id}(x1)]";
        }

        return $placeholder;
    }

    public static function generateWithWrapper(?Task $task = null, ?Part $part = null): string
    {
        return view('components.filename-placeholder', [
            'placeholder' => self::generate($task, $part)
        ])->render();
    }
}
