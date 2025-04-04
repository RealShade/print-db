<?php

namespace App\Http\Requests\Filament;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $colors
 */
class FilamentRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'name'               => __('filament.name'),
            'filament_vendor_id' => __('filament.vendor.field'),
            'filament_type_id'   => __('filament.type.field'),
            'colors'             => __('filament.colors'),
            'density'            => __('filament.density'),
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
            'name'               => 'required|string|max:255',
            'filament_vendor_id' => 'required|exists:filament_vendors,id,user_id,' . auth()->id(),
            'filament_type_id'   => 'required|exists:filament_types,id,user_id,' . auth()->id(),
            'colors'             => 'nullable|array|max:8',
            'colors.*'           => 'string|regex:/^rgba?\(\d{1,3},\s?\d{1,3},\s?\d{1,3}(,\s?\d(\.\d{1,2})?)?\)$/',
            'density'            => 'nullable|numeric|min:0|max:10',
        ];
    }

    /* **************************************** Protected **************************************** */
    protected function prepareForValidation() : void
    {
        if ($this->has('colors') && is_string($this->colors)) {
            $colors = array_map('trim', explode(',', $this->colors));
            $colors = array_filter($colors, fn($color) => !empty($color));
            $this->merge(['colors' => $colors]);
        }
    }
}
