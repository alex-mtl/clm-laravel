<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CustomDropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $options;
    public $selected;
    public $label;
    public $readonly;
    public $class;

    public function __construct($name, $options = [], $selected = null, $label = '', $readonly = false, $class = null)
    {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->label = $label;
        $this->readonly = $readonly;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.custom-dropdown');
    }
}
