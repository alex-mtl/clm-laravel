<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    static public $sidebarMenu = [
        [
            'name' => 'Роли',
            'action' => 'roles',
            'handler' => 'window.location.href = "/roles";',
            'active' => false,
        ],

        [
            'name' => 'Пользователи',
            'action' => 'users',
            'handler' => 'window.location.href = "/users";',
            'active' => false,
        ],

        [
            'name' => 'Клубы',
            'action' => 'clubs',
            'handler' => 'window.location.href = "/manage/clubs";',
            'active' => false,
        ],

        [
            'name' => 'Страны',
            'action' => 'countries',
            'handler' => 'window.location.href = "/countries";',
            'active' => false,
        ],

        [
            'name' => 'Города',
            'action' => 'cities',
            'handler' => 'window.location.href = "/cities";',
            'active' => false,
        ],

        [
            'name' => 'Типы запросов',
            'action' => 'request-types',
            'handler' => 'window.location.href = "/request-types";',
            'active' => false,
        ],


    ];
    public function dashboard()
    {
        return view('dashboard', [
            'sidebarMenu' => $this->getSidebarMenu(),
            'styles' => ['dashboard.css']

        ]);
    }

    static public function getSidebarMenu($activeTab = null)
    {
        $sidebarMenu = collect(self::$sidebarMenu)->map(function($item) use ($activeTab) { // Changed from fn() to full function syntax
            $item = (object)$item;
            $item->active = $item->action == $activeTab;
            return $item;
        });
        return $sidebarMenu;
    }
}
