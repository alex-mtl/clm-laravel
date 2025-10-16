<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AvatarUpload extends Component
{
    public $initialAvatar;
    public $name;
    public $maxSize = 2048;
    public $aspectRatio = '1:1';
    public string $targetSelector = '.user-avatar img';

    public function __construct($initialAvatar, $name = 'avatar', $targetSelector = '.user-avatar img', $maxSize = 2048, $aspectRatio = '1:1')
    {
        $this->targetSelector = $targetSelector;
        $this->initialAvatar = $initialAvatar;
        $this->name = $name;
        $this->maxSize = $maxSize;
        $this->aspectRatio = $aspectRatio;
    }

    public function render()
    {
        return view('components.avatar-upload');
    }
}
