<?php

namespace App\Http\Requests\Print;

use Illuminate\Foundation\Http\FormRequest;

class PartRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->route('part')) {
            return $this->route('part')->user_id === auth()->id();
        }
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'version_date' => 'required|date',
        ];
    }
}
