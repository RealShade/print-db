<?php

namespace App\Traits;

use App\Http\Requests\Api\Print\AfterPrintRequest;
use App\Models\Printer;

trait ParseFilament
{

    /* **************************************** Public **************************************** */
    public function parseFilament(AfterPrintRequest $request, Printer $printer) : array
    {
        $result = [
            'success' => true,
            'errors'  => [],
            'data'    => [
                'slots' => [],
            ],
        ];

        $slots = $request->slots ?? null;
        if (!$slots) {
            return $result;
        }

        if (!is_array($slots)) {
            return [
                'success' => false,
                'errors'  => [
                    'slots' => __('printer.validation.slots.array'),
                ],
            ];
        }

        foreach ($slots as $slotName => $weight) {
            if (!is_numeric($weight)) {
                $result['success'] = false;
                $result['errors'][ $slotName ] = __('printer.validation.slots.not_found', ['slot' => $slotName]);
                continue;
            }

            $slot = $printer->filamentSlots()
                ->where('attribute', $slotName)
                ->first();
            if (!$slot) {
                $result['success'] = false;
                $result['errors'][ $slotName ] = __('printer.validation.slots.not_found', ['slot' => $slotName]);
                continue;
            }

            $result['data']['input'][ $slotName ] = $weight;
        }

        return $result;
    }

}
