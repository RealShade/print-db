<?php

namespace App\Http\Requests;

use App\Models\FilamentSpool;
use App\Models\PartTask;
use Illuminate\Foundation\Http\FormRequest;

class PrintJobFilamentSpoolRequest extends FormRequest
{
    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        $printJob = $this->route('printJob');
        if ($printJob === null) {
            return false;
        }
        $filamentSpool = $this->route('filamentSpool');

        return $printJob->printer->user_id === auth()->id()
            && ($filamentSpool === null || $printJob->spools->contains($filamentSpool));
    }

    public function rules() : array
    {
        return [
            'filament_spool_id' => [
                'required',
                'exists:App\Models\FilamentSpool,id,user_id,' . auth()->id(),
            ],
            'weight_used'       => 'required|decimal:0,4|min:0.0001',
        ];
    }

}
