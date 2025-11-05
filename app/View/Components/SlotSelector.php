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
    public $label;
    public $value;

    public $slotAvailability;
    public $slotRoles;
    public $selectedSlot;
    public $required;
    public $readonly;
    public $callback;

    public function __construct(
        string $name,
        string $value = '',
        string $label = '',
        array $slotAvailability = [],
        array $slotRoles = [],
        int $selectedSlot = 0,
        bool $required = false,
        bool $readonly = false,
        string $callback = null

    ) {
        $this->name = $name;
        $this->label = $label;
        $this->selectedSlot = old($name, $selectedSlot ?? 0);
        $this->slotAvailability = $slotAvailability;
        $this->slotRoles = $slotRoles;
        $this->required = $required;
        $this->readonly = $readonly;
        $this->callback = $callback;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.slot-selector');
    }
}
