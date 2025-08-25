<?php
// app/Http/View/Composers/HeaderMenuComposer.php
namespace App\Http\View\Composers;

use App\Models\User;
use Illuminate\View\View;

class HeaderMenuComposer
{
    public function compose(View $view)
    {
        $user = auth()->user();

        $menuItems = [
            [
                'label' => 'Profile',
                'link' => route('user.profile', ['user' => $user]),
                'icon' => 'user',
            ],
        ];
        if ($user && $user->isAdmin()) {
            $menuItems[] = [
                'label' => 'Dashboard',
                'link' => route('dashboard'),
                'icon' => 'shield',
            ];
        }

        $menuItems[] = [
            'label' => 'Выйти',
            'link' => route('logout.form'),
            'icon' => 'logout',
        ];

        $view->with('menuItems', $menuItems);
    }
}
