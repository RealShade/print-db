<?php

namespace App\Traits;

use App\Models\Printer;

trait ParseFilament
{

    /* **************************************** Public **************************************** */
    public function parseFilament(array $slots, Printer $printer) : array
    {
        $result = [
            'success' => true,
            'errors'  => [],
            'data'    => [],
        ];

        if (!$slots) {
            return $result;
        }

        foreach ($slots as $slotName => $weight) {
            if (!is_numeric($weight)) {
                $result['success'] = false;
                $result['errors'][ $slotName ] = __('printer.validation.slots.float', ['slot' => $slotName]);
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
