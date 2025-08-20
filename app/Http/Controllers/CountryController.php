<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $cols = collect([
            [
                'name' => 'Страна',
                'class' => 'w-10',
                'prop' => 'name'
            ],
            [
                'name' => 'Код',
                'class' => 'w-10',
                'prop' => 'code'
            ],
            [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions'
            ],

        ])->map(fn($item) => (object)$item);

        return view('countries.index', [
            'cols' => $cols,
            'countries' => Country::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return view('countries.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'code' => 'required|size:3']);
        Country::create($request->only('name', 'code'));
        return redirect()->route('countries.index');
    }

    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate(['name' => 'required', 'code' => 'required|size:3']);
        $country->update($request->all());
        return redirect()->route('countries.index');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('countries.index');
    }
}
