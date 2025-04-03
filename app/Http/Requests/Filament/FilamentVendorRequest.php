<?php

namespace App\Http\Requests\Filament;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class FilamentVendorRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'name'    => __('filament_vendor.name'),
            'rate'    => __('filament_vendor.rate'),
            'comment' => __('filament_vendor.comment'),
        ];
    }

    public function authorize() : bool
    {
        $vendor = $this->route('vendor');

        return $vendor === null || $vendor->user_id === auth()->id();
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException(__('filament_vendor.not_found_or_not_owned'));
    }

    public function rules() : array
    {
        return [
            'name'    => 'required|string|max:255',
            'rate'    => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ];
    }
}
