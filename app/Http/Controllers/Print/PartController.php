<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Http\Requests\Print\PartRequest;
use App\Models\Catalog;
use App\Models\Part;
use App\Services\PartFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use RuntimeException;

class PartController extends Controller
{
    /* **************************************** Public **************************************** */
    public function create(Catalog $catalog) : View
    {
        $part = null;

        return view('print.parts.form', compact('part', 'catalog'));
    }

    public function destroy(Part $part) : JsonResponse
    {
        if ($part->tasks()->exists()) {
            abort(412, __('part.cannot_delete'));
        }
        $part->delete();

        return response()->json(['success' => true]);
    }

    public function edit(Part $part) : View
    {
        return view('print.parts.form', compact('part'));
    }

    /**
     * Обработка загрузки файла по частям через Dropzone
     */
    public function uploadChunk(Request $request) : JsonResponse
    {
        try {
            // Проверяем, есть ли файл в запросе
            if (!$request->hasFile('stl_file')) {
                throw new UploadMissingFileException();
            }

            $file = $request->file('stl_file');

            // Получаем информацию о частях (чанках) из запроса Dropzone
            $dzuuid = $request->input('dzuuid');
            $dzchunkindex = (int)$request->input('dzchunkindex');
            $dztotalchunkcount = (int)$request->input('dztotalchunkcount');
            $dzchunksize = (int)$request->input('dzchunksize');
            $dztotalfilesize = (int)$request->input('dztotalfilesize');

            // Логируем получение чанка для отладки
            \Log::info("Получен чанк {$dzchunkindex} из {$dztotalchunkcount} для файла {$dzuuid}");

            // Генерируем уникальное имя для временной директории чанков
            $tempDirectory = storage_path('app/chunks/' . $dzuuid);
            if (!file_exists($tempDirectory) && !mkdir($tempDirectory, 0777, true) && !is_dir($tempDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $tempDirectory));
            }

            // Имя файла для текущего чанка
            $chunkFilename = $tempDirectory . '/chunk.' . $dzchunkindex;

            // Сохраняем текущий чанк
            $file->move(dirname($chunkFilename), basename($chunkFilename));
            \Log::info("Сохранен чанк {$dzchunkindex} в {$chunkFilename}");

            // Если это последний чанк, проверяем наличие всех предыдущих чанков
            if ($dzchunkindex == $dztotalchunkcount - 1) {
                // Проверяем, все ли чанки загружены
                $allChunksUploaded = true;
                $missingChunks = [];

                for ($i = 0; $i < $dztotalchunkcount; $i++) {
                    $chunkPath = $tempDirectory . '/chunk.' . $i;
                    if (!file_exists($chunkPath)) {
                        $allChunksUploaded = false;
                        $missingChunks[] = $i;
                    }
                }

                // Если не все чанки загружены, ждем и не объединяем файл
                if (!$allChunksUploaded) {
                    \Log::warning("Последний чанк получен, но отсутствуют чанки: " . implode(', ', $missingChunks));

                    // Создаем файл-флаг, указывающий, что последний чанк уже загружен
                    file_put_contents($tempDirectory . '/last_chunk_uploaded', 'true');

                    return response()->json([
                        'success' => true,
                        'done' => 99, // Почти завершено, но ещё не объединено
                        'status' => true,
                        'message' => 'Ожидание загрузки всех частей файла...'
                    ]);
                }

                // Все чанки загружены, объединяем их в один файл
                return $this->mergeChunks($dzuuid, $dztotalchunkcount, $dztotalfilesize, $file->getClientOriginalName());
            } else {
                // Для промежуточных чанков проверяем, был ли уже загружен последний чанк
                $lastChunkUploadedFlag = $tempDirectory . '/last_chunk_uploaded';

                // Если последний чанк уже загружен, и текущий чанк был последним недостающим
                if (file_exists($lastChunkUploadedFlag)) {
                    // Проверяем, все ли чанки теперь загружены
                    $allChunksUploaded = true;

                    for ($i = 0; $i < $dztotalchunkcount; $i++) {
                        $chunkPath = $tempDirectory . '/chunk.' . $i;
                        if (!file_exists($chunkPath)) {
                            $allChunksUploaded = false;
                            break;
                        }
                    }

                    // Если все чанки загружены, объединяем их
                    if ($allChunksUploaded) {
                        \Log::info("Все чанки загружены после получения чанка {$dzchunkindex}. Объединяем.");
                        return $this->mergeChunks($dzuuid, $dztotalchunkcount, $dztotalfilesize, $file->getClientOriginalName());
                    }
                }

                // Для обычных промежуточных чанков просто возвращаем статус успешной загрузки
                return response()->json([
                    'success' => true,
                    'done' => (($dzchunkindex + 1) / $dztotalchunkcount * 100),
                    'status' => true
                ]);
            }
        } catch (\Exception $e) {
            // Логируем ошибку для отладки
            \Log::error('Ошибка загрузки файла: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки файла: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Объединяет чанки в один файл
     */
    private function mergeChunks(string $dzuuid, int $totalChunks, int $totalFileSize, string $originalName) : JsonResponse
    {
        $tempDirectory = storage_path('app/chunks/' . $dzuuid);

        // Генерируем имя для окончательного файла
        $finalFilename = md5($originalName . time()) . '.stl';
        $finalFilePath = storage_path('app/chunks/' . $finalFilename);

        try {
            // Открываем файл для записи
            $finalFile = fopen($finalFilePath, 'wb');

            // Объединяем все чанки
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $tempDirectory . '/chunk.' . $i;

                if (!file_exists($chunkPath)) {
                    // Если какого-то чанка нет, возвращаем ошибку
                    fclose($finalFile);
                    @unlink($finalFilePath); // Удаляем неполный файл

                    \Log::error("Чанк {$i} не найден при объединении");

                    return response()->json([
                        'success' => false,
                        'message' => "Часть файла {$i} не найдена. Попробуйте загрузить файл заново."
                    ], 400);
                }

                // Логируем размер чанка
                $chunkSize = filesize($chunkPath);
                \Log::info("Добавление чанка {$i}, размер: {$chunkSize} байт");

                // Добавляем содержимое чанка в итоговый файл
                $chunk = fopen($chunkPath, 'rb');
                stream_copy_to_stream($chunk, $finalFile);
                fclose($chunk);

                // Удаляем чанк после использования
                @unlink($chunkPath);
            }

            // Закрываем итоговый файл
            fclose($finalFile);

            // Удаляем флаг последнего чанка, если он существует
            @unlink($tempDirectory . '/last_chunk_uploaded');

            // Удаляем временную директорию
            @rmdir($tempDirectory);

            // Проверяем размер итогового файла
            $finalFileSize = filesize($finalFilePath);
            \Log::info("Итоговый файл создан: {$finalFilePath}, размер: {$finalFileSize} байт, ожидаемый размер: {$totalFileSize} байт");

            if ($finalFileSize != $totalFileSize && $totalFileSize > 0) {
                // Размеры не совпадают, возможно, часть данных потеряна
                @unlink($finalFilePath);

                \Log::error("Размер итогового файла ({$finalFileSize}) не соответствует ожидаемому ({$totalFileSize})");

                return response()->json([
                    'success' => false,
                    'message' => 'Размер полученного файла не соответствует ожидаемому. Попробуйте загрузить файл заново.'
                ], 400);
            }

            // Возвращаем результат с информацией о файле
            \Log::info("Файл успешно собран: {$finalFilename}, оригинальное имя: {$originalName}");

            return response()->json([
                'success' => true,
                'path' => $finalFilePath,
                'filename' => $finalFilename,
                'original_name' => $originalName
            ]);
        } catch (\Exception $e) {
            \Log::error("Ошибка при объединении чанков: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Чистим за собой при ошибке
            if (isset($finalFile) && is_resource($finalFile)) {
                fclose($finalFile);
            }

            if (file_exists($finalFilePath)) {
                @unlink($finalFilePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при объединении файла: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(PartRequest $request) : JsonResponse
    {
        $part             = new Part($request->validated());
        $part->user_id    = auth()->id();
        $part->catalog_id = $request->catalog_id;
        $part->save();

        // Обработка STL-файла (загруженного по частям или обычным способом)
        if ($request->has('chunk_file_path') && $request->has('chunk_original_name')) {
            // Для файла, загруженного по частям
            $filePath = $request->input('chunk_file_path');

            if (file_exists($filePath)) {
                $filename = $part->fileService()->saveChunkedStlFile($filePath, $request->input('chunk_original_name'));
                $part->stl_filename = $filename;
                $part->stl_original_name = $request->input('chunk_original_name');
                $part->save();

                // Удаляем временный файл
                @unlink($filePath);
            }
        } elseif ($request->hasFile('stl_file')) {
            // Для обычной загрузки файла
            $file = $request->file('stl_file');
            $filename = $part->fileService()->saveStlFile($file);
            $part->stl_filename = $filename;
            $part->stl_original_name = $file->getClientOriginalName();
            $part->save();
        }

        return response()->json(['success' => true]);
    }

    public function update(PartRequest $request, Part $part) : JsonResponse
    {
        $part->update($request->validated());

        // Удаление STL-файла по запросу пользователя
        if ($request->input('delete_stl') === '1' && $part->stl_filename) {
            $part->fileService()->deleteStlFile();
            $part->stl_filename = null;
            $part->stl_original_name = null;
            $part->save();
        }

        // Обработка STL-файла (загруженного по частям или обычным способом)
        if ($request->has('chunk_file_path') && $request->has('chunk_original_name')) {
            // Для файла, загруженного по частям
            $filePath = $request->input('chunk_file_path');

            if (file_exists($filePath)) {
                // Удалить старый файл, если был
                if ($part->stl_filename) {
                    $part->fileService()->deleteStlFile();
                }

                $filename = $part->fileService()->saveChunkedStlFile($filePath, $request->input('chunk_original_name'));
                $part->stl_filename = $filename;
                $part->stl_original_name = $request->input('chunk_original_name');
                $part->save();

                // Удаляем временный файл
                @unlink($filePath);
            }
        } elseif ($request->hasFile('stl_file')) {
            // Для обычной загрузки файла
            $file = $request->file('stl_file');
            // Удалить старый файл, если был
            if ($part->stl_filename) {
                $part->fileService()->deleteStlFile();
            }
            $filename = $part->fileService()->saveStlFile($file);
            $part->stl_filename = $filename;
            $part->stl_original_name = $file->getClientOriginalName();
            $part->save();
        }

        return response()->json(['success' => true]);
    }
}
