<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SynchronizedInput extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $label;
    public $value;
    public $type;
    public $placeholder;
    public $required;
    public $readonly;

    public function __construct(
        string $name,
        string $type = 'text',
        string $label = '',
        string $value = '',
        string $placeholder = '',
        bool $required = false,
        bool $readonly = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->value = old($name, $value ?? '');
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->readonly = $readonly;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.synchronized-input');
    }
}
