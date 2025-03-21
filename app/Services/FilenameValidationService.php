<?php

namespace App\Services;

use App\Traits\ParsesFilenameTemplate;
use Illuminate\Support\Facades\Auth;

class FilenameValidationService
{
    use ParsesFilenameTemplate;

    public function validate(string $filename): array
    {
        return $this->parseFilename($filename, Auth::id());
    }
}
