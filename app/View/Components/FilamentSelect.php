<?php

namespace App\View\Components;

use App\Models\Filament;
use Illuminate\View\Component;
use Illuminate\View\View;

class FilamentSelect extends Component
{
    public ?int   $value;
    public string $name;
    public string $id;
    public bool   $required;
    public array  $filaments;

    /* **************************************** Constructor **************************************** */
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($value = null, $name = 'filament_id', $id = null, $required = false)
    {
        $this->value     = $value;
        $this->name      = $name;
        $this->id        = $id ?? $name;
        $this->required  = $required;
        $this->filaments = Filament::getForSelect();
    }

    /* **************************************** Public **************************************** */
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render() : View
    {
        return view('components.filament-select');
    }
}
