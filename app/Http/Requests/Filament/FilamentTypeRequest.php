<?php
// app/Http/Requests/Print/FilamentTypeRequest.php

namespace App\Http\Requests\Filament;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class FilamentTypeRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes(): array
    {
        return [
            'name' => __('filament_type.name'),
        ];
    }

    public function authorize(): bool
    {
        $filamentType = $this->route('filament_type');

        return $filamentType === null || $filamentType->user_id === auth()->id();
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException(__('filament_type.not_found_or_not_owned'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
