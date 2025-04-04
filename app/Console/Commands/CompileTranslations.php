<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompileTranslations extends Command
{
    protected $signature = 'translations:compile {--lang=* : Языки для компиляции}';
    protected $description = 'Компилирует переводы в JS-файл';

    public function handle() : void
    {
        $languages = $this->option('lang') ?: ['uk', 'ru'];
        $translations = [];

        foreach ($languages as $lang) {
            $translations[$lang] = [];
            $path = resource_path("lang/$lang");

            if (File::isDirectory($path)) {
                $files = File::files($path);

                foreach ($files as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $translations[$lang][$filename] = require $file->getPathname();
                }
            }
        }

        $content = "window.translations = ".json_encode($translations).";\n";
        File::put(public_path('assets/js/translations.js'), $content);

        $this->info('Переводы скомпилированы в public/js/translations.js');
    }
}
