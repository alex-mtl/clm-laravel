<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AvatarUpload extends Component
{
    public $initialAvatar;
    public $name;
    public string $targetSelector = '.user-avatar img';

    public function __construct($initialAvatar, $name = 'avatar', $targetSelector = '.user-avatar img',)
    {
        $this->targetSelector = $targetSelector;
        $this->initialAvatar = $initialAvatar;
        $this->name = $name;
    }

    public function render()
    {
        return view('components.avatar-upload');
    }
}
