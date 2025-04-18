<?php

namespace App\View\Components;

use App\Models\FilamentSpool;
use Illuminate\View\Component;
use Illuminate\View\View;

class FilamentSpoolSelect extends Component
{
    public ?int $value;
    public string $name;
    public string $id;
    public bool $required;
    public array $spools;

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
     * @return View
     */
    public function render() : View
    {
        return view('components.filament-spool-select');
    }
}
