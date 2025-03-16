<?php

namespace App\Facades;

use App\Models\Part;
use App\Models\Task;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string generateWithWrapper(Task $task, ?Part $part = null)
 * @see FilenamePlaceholderHelper
 */
class FilenamePlaceholder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filename-placeholder';
    }
}
