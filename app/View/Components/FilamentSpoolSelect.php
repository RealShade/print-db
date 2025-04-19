<?php

namespace App\View\Components;

use App\Models\FilamentSpool;
use Illuminate\View\Component;

class FilamentSpoolSelect extends Component
{
    public $value;
    public $name;
    public $id;
    public $required;
    public $spools;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($value = null, $name = 'filament_spool_id', $id = null, $required = false)
    {
        $this->value = $value;
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->required = $required;
        $this->spools = FilamentSpool::getForSelect();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.filament-spool-select');
    }
}
