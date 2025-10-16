<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::with('owner')->latest()->paginate(10);
        return view('clubs.index', [
            ...compact('clubs'),
            'styles' => ['clubs.css']
        ]);
    }

    public function create()
    {
//        $owners = User::all();
//        return view('clubs.create', compact('owners'));
        $countrySelector = Country::getCountrySelector();
        $citySelector = City::getCitySelector();

        return view('clubs.form', [
            'countrySelector' => $countrySelector,
            'citySelector' => $citySelector,
            'styles' => ['club-form.css'],
            'club' => new Club(),
            'mode' => 'create'
        ]);
    }



    public function store(Request $request)
    {
        $validatedData =$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|ratio:1,1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        foreach (array_keys(Club::IMG_TYPES) as $type) {
            if ($request->hasFile($type)) {
                $validatedData[$type] = Club::saveImg($request, $type);
            }
        }

        Club::create([...$validatedData, 'owner_id' => auth()->user()->id]);

        return redirect()->route('clubs.index')
            ->with('success', 'Club created successfully.');
    }

    public function show(Club $club)
    {
        if(request()->has('tab') ) {
            session()->flash('tab', request('tab')); // One-time flash
        }
        return view('clubs.show', [
            ...compact('club'),
            'styles' => ['club-view.css'],
            'scripts' => ['club-view.js']
        ]);
    }

    public function edit(Club $club)
    {
//        dd($club->owner_id, auth()->user()->id);
//        if($club->owner_id !== auth()->user()->id) {
            $this->authorize('manage_club', $club);
//        }

        $countrySelector = Country::getCountrySelector();
        $citySelector = City::getCitySelector();

        return view('clubs.form', [
            'countrySelector' => $countrySelector,
            'citySelector' => $citySelector,
            'styles' => ['club-form.css'],
            'club' => $club,
            'mode' => 'edit'
        ]);
    }

    public function update(Request $request, Club $club)
    {
        if (auth()->user()->id !== $club->owner_id) {
            throw ValidationException::withMessages([
                'owner' => 'You must be the owner of the club to perform this action.'
            ]);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
//            'owner_id' => 'required|exists:users,id',
        ]);

        foreach (array_keys(Club::IMG_TYPES) as $type) {
            if ($request->hasFile($type)) {
                $validatedData[$type] = Club::saveImg($request, $type);
            }
        }


        $club->update($validatedData);

        return redirect()->route('clubs.show', $club)
            ->with('success', 'Club updated successfully.');
    }

    public function destroy(Club $club)
    {
        $club->delete();
        return redirect()->route('clubs.index')
            ->with('success', 'Club deleted successfully.');
    }
}
