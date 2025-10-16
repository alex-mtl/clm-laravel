<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;

class ClubPagesController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('super_admin', new Role());
            return $next($request);
        });
    }
    public function index()
    {
        $clubSelector = Club::all()->pluck('name', 'id');
        $sidebarMenu = SuperAdminController::getSidebarMenu('clubs');

        return view('manage.clubs.index', [
            'clubSelector' => $clubSelector,
            'sidebarMenu' => $sidebarMenu,
            'scripts' => ['manage-clubs.js']
        ]);
    }


}
