<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Number extends Component
{
    public mixed $value;
    public int  $precision;
    public bool $isNoEmpty = false;

    /**
     * Create a new component instance.
     *
     * @param mixed $value
     * @param int   $precision
     * @param bool  $noEmpty
     */
    public function __construct(mixed $value, int $precision = 2, bool $noEmpty = false)
    {
        $this->value = $value;
        $this->precision = $precision;
        $this->isNoEmpty = $noEmpty;
    }

    /**
     * Получение целой части
     */
    public function integerPart() : float
    {
        return floor((float)$this->value);
    }

    /**
     * Получение дробной части
     */
    public function fractionPart() : string
    {
        $fraction = bcsub((string)$this->value, (string)$this->integerPart(), $this->precision);
        $fraction = ltrim($fraction, '0.');

        return str_pad($fraction, $this->precision, '0', STR_PAD_RIGHT);
    }

    public function noEmpty() : bool
    {
        return $this->isNoEmpty;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string|Closure|\Illuminate\View\View
     */
    public function render() : View|string|Closure|\Illuminate\View\View
    {
        return view('components.number');
    }
}
