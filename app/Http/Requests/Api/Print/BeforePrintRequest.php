<?php

namespace App\Http\Requests\Api\Print;

use App\Http\Requests\Api\ApiRequest;

class BeforePrintRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'filename' => ['required', 'string', 'max:255'],
            'printer_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
