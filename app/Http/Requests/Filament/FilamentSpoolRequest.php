<?php

namespace App\Http\Requests\Filament;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $spool
 */
class FilamentSpoolRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function attributes() : array
    {
        return [
            'filament_id'           => __('filament_spool.filament'),
            'filament_packaging_id' => __('filament_spool.packaging'),
            'weight_initial'        => __('filament_spool.weight_initial'),
            'weight_used'           => __('filament_spool.weight_used'),
            'date_first_used'       => __('filament_spool.date_first_used'),
            'date_last_used'        => __('filament_spool.date_last_used'),
            'cost'                  => __('filament_spool.cost'),
        ];
    }

    public function authorize() : bool
    {
        $spool = $this->route('spool');

        return $spool === null || $spool->user_id === auth()->id();
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException(__('filament_spool.not_found_or_not_owned'));
    }

    public function rules() : array
    {
        $attr = [
            'cost'                  => 'nullable|numeric|min:0',
        ];

        if ($this->spool) {
            $attr = array_merge($attr, [
                'weight_initial'  => 'required|numeric|min:0',
                'weight_used'     => 'nullable|numeric|min:0|lte:weight_initial',
                'date_first_used' => 'nullable|date',
                'date_last_used'  => 'nullable|date|after_or_equal:date_first_used',
            ]);
        } else {
            $attr = array_merge($attr, [
                'filament_id'           => 'required|exists:filaments,id,user_id,' . auth()->id(),
                'filament_packaging_id' => 'required|exists:filament_packagings,id,user_id,' . auth()->id(),
            ]);
        }

        return $attr;
    }
}
