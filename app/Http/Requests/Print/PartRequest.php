<?php

namespace App\Http\Requests\Print;

use Illuminate\Foundation\Http\FormRequest;

class PartRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'name'         => __('part.name'),
            'version'      => __('part.version'),
            'version_date' => __('part.version_date'),
        ];
    }

    public function authorize() : bool
    {
        if ($this->route('part')) {
            return $this->route('part')->user_id === auth()->id();
        }

        return true;
    }

    public function rules() : array
    {
        return [
            'name'         => 'required|string|max:255',
            'version'      => 'nullable|string|max:50',
            'version_date' => 'nullable|date',
        ];
    }

    /* **************************************** Protected **************************************** */
    protected function prepareForValidation() : void
    {
        if (empty($this->input('version'))) {
            $this->merge(['version' => 'v0']);
        }
    }
}
