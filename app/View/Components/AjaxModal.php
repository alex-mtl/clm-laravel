<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class AjaxModal extends Component
{
    public $endpoint;
    public $title;
    public $modalId;
    public $icon;
    public $class;
    public $callback;
    public $container;
    public $btnid;
    public $hidden;

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function __construct($endpoint, $title = 'Form', $modalId = 'ajaxModal', $icon = 'add_circle', $class = '', $callback = null, $btnid = null, $hidden = 'false', $container = null)
    {
        $this->endpoint = $endpoint;
        $this->title = $title;
        $this->modalId = $modalId;
        $this->icon = $icon;
        $this->class = $class;
        $this->callback = $callback;
        $this->btnid = $btnid ?? Str::random(8);
        $this->hidden = $hidden;
        $this->container = $container;

    }

    public function render()
    {
        return view('components.ajax-modal');
    }
}
