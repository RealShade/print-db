<?php

namespace App\Helpers;

use App\Models\Part;
use App\Models\Task;

class FilenamePlaceholderHelper
{
    /* **************************************** Static **************************************** */
    public static function generate(Task $task, ?Part $part = null) : string
    {
        $placeholder = '';

        if ($part) {
            $placeholder .= "[pid_{$part->id}(x1)@{$task->id}]";
        } else {
            $placeholder .= "[tid_{$task->id}]";
        }


        return $placeholder;
    }

    public static function generateWithWrapper(?Task $task = null, ?Part $part = null) : string
    {
        return view('components.filename-placeholder', [
            'placeholder' => self::generate($task, $part),
        ])->render();
    }
}
