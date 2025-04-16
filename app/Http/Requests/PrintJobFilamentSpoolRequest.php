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
            && ($filamentSpool === null || $printJob->filamentSpools->contains($filamentSpool));
    }

    public function rules() : array
    {
        return [
            'filament_spool_id' => [
                'required',
                'exists:' . app(FilamentSpool::class)->getTable() . ',id',
                function($attribute, $value, $fail) {
                    $filamentSpool = FilamentSpool::find($value);
                    if ($filamentSpool->user_id !== auth()->id()) {
                        $fail(__('validation.exists', ['attribute' => __('task.title')]));
                    }
                },
            ],
            'weight_used'       => 'required|decimal:0,4|min:0.0001',
        ];
    }

}
