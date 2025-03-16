<?php

namespace App\Providers;

use App\Facades\FilenamePlaceholder;
use App\Helpers\FilenamePlaceholderHelper;
use Illuminate\Support\ServiceProvider;

class FilenamePlaceholderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('filename-placeholder', function() {
            return new FilenamePlaceholderHelper();
        });

        class_alias(FilenamePlaceholder::class, 'FilenamePlaceholder');
    }
}
