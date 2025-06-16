<?php

namespace App\Services;

use App\Models\Part;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PartFileService
{
    /**
     * Имя диска для хранения STL-файлов
     */
    public const DISK = 'public';

    /**
     * Папка для хранения STL-файлов
     */
    public const DIR = 'parts';

    /**
     * Сохранить STL-файл и вернуть имя файла
     */
    public function saveStlFile(Part $part, UploadedFile $file): string
    {
        $random = bin2hex(random_bytes(32));
        $filename = 'parts_' . $part->id . '_' . $random . '.stl';
        $file->storeAs(self::DIR, $filename, self::DISK);
        // Генерируем превью после сохранения
        $this->generatePreview($filename);
        return $filename;
    }

    /**
     * Удалить STL-файл по имени
     */
    public function deleteStlFile(?string $filename): void
    {
        if ($filename) {
            Storage::disk(self::DISK)->delete(self::DIR . '/' . $filename);
            // Удаляем превью PNG
            $pngName = preg_replace('/\.stl$/i', '.png', $filename);
            Storage::disk(self::DISK)->delete(self::DIR . '/' . $pngName);
        }
    }

    /**
     * Получить публичный URL для скачивания STL-файла
     */
    public function getStlFileUrl(?string $filename): ?string
    {
        if (!$filename) return null;
        return Storage::disk(self::DISK)->url(self::DIR . '/' . $filename);
    }

    /**
     * Получить публичный URL превью STL-файла (PNG)
     */
    public function getPreviewUrl(?string $stlFilename): ?string
    {
        if (!$stlFilename) return null;
        $pngName = preg_replace('/\.stl$/i', '.png', $stlFilename);
        if (!Storage::disk(self::DISK)->exists(self::DIR . '/' . $pngName)) {
            return null;
        }
        return Storage::disk(self::DISK)->url(self::DIR . '/' . $pngName);
    }

    /**
     * Проверить, существует ли превью STL-файла (PNG)
     */
    public function hasPreview(?string $stlFilename): bool
    {
        if (!$stlFilename) return false;
        $pngName = preg_replace('/\.stl$/i', '.png', $stlFilename);
        return Storage::disk(self::DISK)->exists(self::DIR . '/' . $pngName);
    }

    /**
     * Сгенерировать превью STL-файла (PNG) рядом с файлом
     */
    public function generatePreview(string $filename): void
    {
        $stlPath = Storage::disk(self::DISK)->path(self::DIR . '/' . $filename);
        $pngPath = preg_replace('/\.stl$/i', '.png', $stlPath);
        $bin = '/usr/bin/stl-thumb';
        $cmd = sprintf(
            '%s --size 200 --material 146c43 146c43 146c43 %s %s 2>&1',
            escapeshellcmd($bin),
            escapeshellarg($stlPath),
            escapeshellarg($pngPath)
        );
        exec($cmd);
    }
}
