<?php

namespace App\Http\Requests;

use App\Models\PrinterFilamentSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrinterFilamentSlotRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        $filamentSlot = $this->route('filamentSlot');
        $printer        = $this->route('printer');

        return $printer->user_id === auth()->id() && ($filamentSlot === null || $filamentSlot->printer_id === $printer->id);
    }

    public function rules() : array
    {
        return [
            'name'              => [
                'required',
                'string',
                'max:255',
                Rule::unique(PrinterFilamentSlot::class, 'name')
                    ->where('printer_id', $this->input('printer_id'))
                    ->ignore($this->route('filamentSlot')?->id),
            ],
            'attribute'         => [
                'required',
                'string',
                'max:255',
                Rule::unique(PrinterFilamentSlot::class, 'attribute')
                    ->where('printer_id', $this->input('printer_id'))
                    ->ignore($this->route('filamentSlot')?->id),
            ],
            'description'       => 'nullable|string',
            'filament_spool_id' => 'nullable|exists:App\Models\FilamentSpool,id,user_id,' . auth()->id(),
        ];
    }
}
