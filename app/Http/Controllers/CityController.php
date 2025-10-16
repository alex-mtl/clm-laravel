<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Country;

class CityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorize('super_admin', new Role());
            return $next($request);
        });
    }
    public function index()
    {
        $sidebarMenu = SuperAdminController::getSidebarMenu('cities');

        $cols = collect([
            [
                'name' => 'Страна',
                'class' => 'w-10',
                'prop' => 'country.name'
            ],
            [
                'name' => 'Город',
                'class' => 'w-10',
                'prop' => 'name'
            ],
            [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions',
                'ajax' => true,
            ],

        ])->map(fn($item) => (object)$item);
        return view('cities.index', [
            'cols' => $cols,
            'sidebarMenu' => $sidebarMenu,
            'cities' => City::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        $layout  = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        $city = new City(['country_id' => 1]);
        $countrySelector = Country::getCountrySelector();

        return view('cities.create',[
            'layout' => $layout,
            'city' => $city,
            'mode' => 'create',
            'countrySelector' => $countrySelector,
            'countries' => Country::all()
        ]);
    }

    public function store(Request $request)
    {
//        dd($request->all(), $request->only('name', 'country_id'));
        $request->validate(['name' => 'required', 'country_id' => 'required']);
//        City::create($request->only('name', 'country_id'));
        City::create([
            'name' => $request->name,
            'country_id' => $request->country_id
        ]);
        return redirect()->route('cities.index');
    }

    public function edit(City $city)
    {
        return view('cities.edit', [
            ...compact('city'),
            'countries' => Country::all()

        ]);
    }

    public function update(Request $request, City $city)
    {
        $request->validate(['name' => 'required',  'country_id' => 'required']);
        $city->update($request->all());
        return redirect()->route('cities.index');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('cities.index');
    }

    public function show(City $city)
    {
        return view('cities.show', [
            ...compact('city'),
        ]);
    }
}
