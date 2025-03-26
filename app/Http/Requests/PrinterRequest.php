<?php

namespace App\Http\Requests;

use App\Models\Printer;
use Illuminate\Foundation\Http\FormRequest;

class PrinterRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        $printer = $this->route('printer');

        return $printer === null || $printer->user_id === auth()->id();
    }

    public function messages() : array
    {
        return [
            'name.required' => 'Назва принтера є обов\'язковою.',
            'name.string'   => 'Назва принтера повинна бути рядком.',
            'name.max'      => 'Назва принтера не може бути довшою за :max символів.',
            'name.unique'   => 'Назва принтера вже зайнята.',
        ];
    }

    public function rules() : array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function($attribute, $value, $fail) {
                    $query = Printer::where('name', $value)
                        ->where('user_id', auth()->id());

                    if ($this->route('printer')) {
                        $query->where('id', '!=', $this->route('printer')->id);
                    }

                    if ($query->exists()) {
                        $fail('Назва принтера вже зайнята.');
                    }
                }],
        ];
    }
}
