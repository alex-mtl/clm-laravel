<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

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
    public $invisible;
    public $class;
    public $btnid;
    public $callback;

    public function __construct($name, $options = [], $selected = null, $label = '', $readonly = false, $class = null, $invisible = 'false', $btnid = null, $callback = null)
    {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->label = $label;
        $this->readonly = $readonly;
        $this->class = $class;
        $this->invisible = $invisible;
        $this->btnid = $btnid ?? Str::random(8);
        $this->callback = $callback;
    }

    public function render()
    {
        return view('components.custom-dropdown');
    }
}
