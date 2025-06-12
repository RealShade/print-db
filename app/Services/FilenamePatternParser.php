<?php

namespace App\Services;

use App\Entities\FilenameParsedEntity;
use InvalidArgumentException;

class FilenamePatternParser
{

    const string PATTERN = '/\((pid_(\d+)(\(x(\d+)\))?(_(\d+))?)\)|\((tid_(\d+)(\(x(\d+)\))?)\)/';

    /** @var FilenameParsedEntity[] */
    protected array $parsedData = [];

    /* **************************************** Constructor **************************************** */
    /**
     * Разбирает строку и извлекает данные
     *
     * @param string|null $filename Строка для разбора
     *
     * @throws InvalidArgumentException Если строка пуста или паттерн не найден
     */
    public function __construct(?string $filename)
    {
        if (empty($filename)) {
            throw new InvalidArgumentException(__('api.validation.filename_empty'));
        }

        if (!preg_match_all(static::PATTERN, $filename, $matches, PREG_SET_ORDER) || empty($matches)) {
            throw new InvalidArgumentException(__('api.validation.pattern_not_found'));
        }


        foreach ($matches as $match) {
            if (($match[2] ?? '') && ($match[5] ?? '')) {
                // Вариант с part_id и task_id: (pid_123(x2)_456)
                $this->parsedData[] = (new FilenameParsedEntity())
                    ->setPartId((int)$match[2])
                    ->setTaskId((int)$match[5])
                    ->setQuantity(!empty($match[4]) ? (int)$match[4] : 1);
            } elseif (($match[7] ?? '')) {
                // Вариант только с task_id: (tid_789(x3))
                $this->parsedData[] = (new FilenameParsedEntity())
                    ->setTaskId((int)$match[7])
                    ->setQuantity(!empty($match[9]) ? (int)$match[9] : 1);
            } elseif (($match[2] ?? '')) {
                // Вариант только с part_id: (pid_123(x2))
                $this->parsedData[] = (new FilenameParsedEntity())
                    ->setPartId((int)$match[2])
                    ->setQuantity(!empty($match[4]) ? (int)$match[4] : 1);
            }
        }
    }

    /* **************************************** Getters **************************************** */
    public function getParsedData() : array
    {
        return $this->parsedData;
    }

}
