<?php

namespace App\Http\Requests\Api\Print;

use App\Http\Requests\Api\ApiRequest;
use App\Models\Printer;

/**
 * @property mixed $printer_id
 */
class BeforePrintRequest extends ApiRequest
{

    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        return true;
    }

    public function messages() : array
    {
        return [
            'printer_id.required' => __('printer.validation.printer_id'),
        ];

    }

    public function rules() : array
    {
        return [
            'filename'   => ['required', 'string', 'max:255'],
            'printer_id' => ['required', 'integer', 'min:1', 'exists:printers,id,user_id,' . auth()->id()],
        ];
    }

    /* **************************************** Getters **************************************** */
    public function getPrinter() : ?Printer
    {
        return Printer::where('id', $this->printer_id)
            ->where('user_id', auth()->id())
            ->first();
    }

    /* **************************************** Protected **************************************** */
    protected function prepareForValidation() : void
    {
        if (!$this->has('printer_id')) {
            $this->merge([
                'printer_id' => auth()->user()->printers()->first()->id ?? null,
            ]);
        }
    }
}
