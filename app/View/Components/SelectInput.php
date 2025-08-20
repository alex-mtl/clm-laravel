<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectInput extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $required;
    public $placeholder;

    public function __construct(
        string $name,
        string $label = '',
               $options = [],
               $selected = null,
        bool $required = false,
        string $placeholder = ''
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = old($name, $selected);
        $this->required = $required;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('components.select-input');
    }
}
