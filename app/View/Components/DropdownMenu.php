<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownMenu extends Component
{
    public $menuItems;

    public $menuOwnerId;
    /**
     * Create a new component instance.
     */
    public function __construct(array $menuItems, string $menuOwnerId)
    {
        $this->menuItems = $menuItems;
        $this->menuOwnerId = $menuOwnerId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-menu');
    }
}
