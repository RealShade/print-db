<?php

namespace App\Http\Requests\Print;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class FilamentRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'name' => __('filament.name'),
            'filament_vendor_id' => __('filament.vendor'),
            'filament_type_id' => __('filament.type'),
            'colors' => __('filament.colors'),
            'density' => __('filament.density'),
            'cost' => __('filament.cost'),
        ];
    }

    public function authorize() : bool
    {
        $filament = $this->route('filament');

        return $filament === null || $filament->user_id === auth()->id();
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException(__('filament.not_found_or_not_owned'));
    }

    public function rules() : array
    {
        return [
            'name' => 'required|string|max:255',
            'filament_vendor_id' => 'required|exists:filament_vendors,id',
            'filament_type_id' => 'required|exists:filament_types,id',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'density' => 'nullable|numeric|min:0|max:10',
            'cost' => 'nullable|numeric|min:0',
        ];
    }
    
    protected function prepareForValidation()
    {
        if ($this->has('colors') && is_string($this->colors)) {
            $colors = array_map('trim', explode(',', $this->colors));
            $colors = array_filter($colors, fn($color) => !empty($color));
            $this->merge(['colors' => $colors]);
        }
    }
}
