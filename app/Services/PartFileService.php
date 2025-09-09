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
     * @var Part|null
     */
    protected ?Part $part;

    /* **************************************** Constructor **************************************** */
    /**
     * Создает новый экземпляр PartFileService
     */
    public function __construct(?Part $part = null)
    {
        $this->part = $part;
    }

    /* **************************************** Public **************************************** */
    /**
     * Удалить STL-файл по имени
     */
    public function deleteStlFile(?string $filename = null) : void
    {
        $filename = $filename ?? $this->part?->stl_filename;

        if ($filename) {
            Storage::disk(self::DISK)->delete(self::DIR . '/' . $filename);
            // Удаляем превью PNG
            $pngName = preg_replace('/\.stl$/i', '.png', $filename);
            Storage::disk(self::DISK)->delete(self::DIR . '/' . $pngName);
        }
    }

    /**
     * Проверить, существует ли превью STL-файла (PNG)
     */
    public function hasPreview(?string $stlFilename = null) : bool
    {
        $stlFilename = $stlFilename ?? $this->part?->stl_filename;
        if (!$stlFilename) {
            return false;
        }

        $pngName = preg_replace('/\.stl$/i', '.png', $stlFilename);

        return Storage::disk(self::DISK)->exists(self::DIR . '/' . $pngName);
    }

    /**
     * Сохранить STL-файл и вернуть имя файла
     */
    public function saveStlFile(UploadedFile $file, ?Part $part = null) : string
    {
        $part = $part ?? $this->part;
        if (!$part) {
            throw new \InvalidArgumentException('Part model is not set');
        }

        $random   = bin2hex(random_bytes(32));
        $filename = 'parts_' . $part->id . '_' . $random . '.stl';
        $file->storeAs(self::DIR, $filename, self::DISK);
        // Генерируем превью после сохранения
        $this->generatePreview($filename);

        return $filename;
    }

    /**
     * Сохранить STL-файл, загруженный по частям
     *
     * @param string $filePath Путь к временному файлу
     * @param string $originalName Оригинальное имя файла
     * @return string Имя сохраненного файла
     */
    public function saveChunkedStlFile(string $filePath, string $originalName) : string
    {
        if (!$this->part) {
            throw new \InvalidArgumentException('Part model is not set');
        }

        $random = bin2hex(random_bytes(32));
        $filename = 'parts_' . $this->part->id . '_' . $random . '.stl';

        // Копируем файл в целевую директорию
        $targetPath = Storage::disk(self::DISK)->path(self::DIR . '/' . $filename);
        copy($filePath, $targetPath);

        // Генерируем превью после сохранения
        $this->generatePreview($filename);

        return $filename;
    }

    /* **************************************** Getters **************************************** */
    /**
     * Получить публичный URL превью STL-файла (PNG)
     */
    public function getPreviewUrl(?string $stlFilename = null) : ?string
    {
        $stlFilename = $stlFilename ?? $this->part?->stl_filename;
        if (!$stlFilename) {
            return null;
        }

        $pngName = preg_replace('/\.stl$/i', '.png', $stlFilename);
        if (!Storage::disk(self::DISK)->exists(self::DIR . '/' . $pngName)) {
            return null;
        }

        return Storage::disk(self::DISK)->url(self::DIR . '/' . $pngName);
    }

    /**
     * Получить публичный URL для скачивания STL-файла
     */
    public function getStlFileUrl(?string $filename = null) : ?string
    {
        $filename = $filename ?? $this->part?->stl_filename;
        if (!$filename) {
            return null;
        }

        return Storage::disk(self::DISK)->url(self::DIR . '/' . $filename);
    }

    /* **************************************** Setters **************************************** */
    /**
     * Установить модель Part
     */
    public function setPart(?Part $part) : self
    {
        $this->part = $part;

        return $this;
    }

    /* **************************************** Protected **************************************** */
    /**
     * Сгенерировать превью STL-файла (PNG) рядом с файлом
     */
    protected function generatePreview(string $filename) : void
    {
        $stlPath = Storage::disk(self::DISK)->path(self::DIR . '/' . $filename);
        $pngPath = preg_replace('/\.stl$/i', '.png', $stlPath);
        $bin     = '/usr/bin/stl-thumb';
        $cmd     = sprintf(
            '%s --size 200 --material 146c43 146c43 146c43 %s %s 2>&1',
            escapeshellcmd($bin),
            escapeshellarg($stlPath),
            escapeshellarg($pngPath)
        );
        exec($cmd);
    }
}
