<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\HeaderMenuComposer;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.header', function ($view) {
            if (app()->isBooted()) { // Ensure app is fully booted
                $view->with('menuItems', $this->getMenuItems());
            }
        });
    }

    public function getMenuItems()
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

        return $menuItems;
    }
}
