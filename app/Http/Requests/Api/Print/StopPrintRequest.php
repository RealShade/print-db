<?php

namespace App\Http\Requests\Api\Print;

use App\Helpers\ApiResponseHelper;
use App\Models\Printer;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @property mixed $printer_id
 */
class StopPrintRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        return true;
    }

    public function failedValidation(Validator $validator) : void
    {
        $errors = $validator->errors()->toArray();
        throw new HttpResponseException(ApiResponseHelper::error($errors, __('common.validation_errors'), 422));
    }

    public function messages() : array
    {
        return [
            'printer_id.required' => __('printer.validation.printer.required'),
            'printer_id.exists'   => __('printer.validation.printer.exists'),
        ];

    }

    public function rules() : array
    {
        return [
            'printer_id' => 'required|integer|exists:printers,id,user_id,' . auth()->id(),
        ];
    }

    /* **************************************** Getters **************************************** */
    public function getPrinter() : ?Printer
    {
        return Printer::where('id', $this->printer_id)
            ->where('user_id', auth()->id())
            ->first();
    }

}
