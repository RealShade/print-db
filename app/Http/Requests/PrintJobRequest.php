<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrintJobRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
    {
        $printer = $this->route('printer');

        return $printer !== null && $printer->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() : array
    {
        return [
            'filename' => ['required', 'string', 'max:255'],
        ];
    }
}
