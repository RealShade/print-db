<?php

namespace App\Http\Requests;

use App\Models\FilamentLoaded;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilamentLoadedRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        $filamentLoaded = $this->route('filamentLoaded');
        $printer        = $this->route('printer');

        return $printer->user_id === auth()->id() && ($filamentLoaded === null || $filamentLoaded->printer_id === $printer->id);
    }

    public function rules() : array
    {
        return [
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique(FilamentLoaded::class, 'name')
                    ->where('printer_id', $this->input('printer_id'))
                    ->ignore($this->route('filamentLoaded')?->id),
            ],
            'attribute'         => [
                'required',
                'string',
                'max:255',
                Rule::unique(FilamentLoaded::class, 'attribute')
                    ->where('printer_id', $this->input('printer_id'))
                    ->ignore($this->route('filamentLoaded')?->id),
            ],
            'description'       => 'nullable|string',
            'filament_spool_id' => 'nullable|exists:App\Models\FilamentSpool,id,user_id,' . auth()->id(),
        ];
    }
}
