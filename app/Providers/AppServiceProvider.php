<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\HeaderMenuComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
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

        Validator::extend('ratio', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) < 2) {
                return false;
            }

            list($width, $height) = getimagesize($value->getPathname());
            $expectedRatio = $parameters[0] / $parameters[1];
            $actualRatio = $width / $height;

            return abs($actualRatio - $expectedRatio) < 0.1;
        });

        Validator::replacer('ratio', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':ratio'], ["{$parameters[0]}:{$parameters[1]}"], $message);
        });

        Paginator::defaultView('vendor.pagination.default');
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
