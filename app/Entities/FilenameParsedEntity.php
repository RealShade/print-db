<?php

namespace App\Entities;

class FilenameParsedEntity
{

    protected ?int $partId   = null;
    protected ?int $taskId   = null;
    protected int  $quantity = 1;

    /* **************************************** Getters **************************************** */
    public function getPartId() : ?int
    {
        return $this->partId;
    }

    public function getQuantity() : int
    {
        return $this->quantity;
    }

    public function getTaskId() : ?int
    {
        return $this->taskId;
    }

    /* **************************************** Setters **************************************** */
    public function setPartId(?int $partId) : FilenameParsedEntity
    {
        $this->partId = $partId;

        return $this;
    }

    public function setQuantity(int $quantity) : FilenameParsedEntity
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setTaskId(?int $taskId) : FilenameParsedEntity
    {
        $this->taskId = $taskId;

        return $this;
    }

}
