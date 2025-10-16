<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SlotSelector extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $value;
    public $slotAvailability;
    public $selectedSlot;
    public $required;
    public $readonly;

    public function __construct(
        string $name,
        string $value = '',
        array $slotAvailability = [],
        int $selectedSlot = 0,
        bool $required = false,
        bool $readonly = false,

    ) {
        $this->name = $name;
        $this->selectedSlot = old($name, $selectedSlot ?? 0);
        $this->required = $required;
        $this->readonly = $readonly;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.slot-selector');
    }
}
