<?php

namespace App\Http\Requests\Print;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class FilamentPackagingRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'name'        => __('filament_packaging_type.name'),
            'weight'      => __('filament_packaging_type.weight'),
            'description' => __('filament_packaging_type.description'),
        ];
    }

    public function authorize() : bool
    {
        $packagingType = $this->route('packaging_type');

        return $packagingType === null || $packagingType->user_id === auth()->id();
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException(__('filament_packaging_type.not_found_or_not_owned'));
    }

    public function rules() : array
    {
        return [
            'name'        => 'required|string|max:255',
            'weight'      => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ];
    }
}
