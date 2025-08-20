<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Country;

class CityController extends Controller
{
    public function index()
    {
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
                'prop' => 'actions'
            ],

        ])->map(fn($item) => (object)$item);
        return view('cities.index', [
            'cols' => $cols,
            'cities' => City::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return view('cities.create',[
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
