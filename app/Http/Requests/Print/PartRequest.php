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
        $part = $this->route('part');

        return $part === null || $part->user_id === auth()->id();
    }

    public function messages() : array
    {
        return [
            'parts.exists' => __('part.not_found_or_not_owned'),
        ];
    }

    public function rules() : array
    {
        return [
            'name'         => 'required|string|max:255',
            'version'      => 'nullable|string|max:50',
            'version_date' => 'nullable|date',
            'part'         => 'nullable|exists:parts,id,user_id,' . auth()->id(),
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
