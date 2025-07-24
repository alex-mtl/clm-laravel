<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::with('owner')->latest()->paginate(10);
        return view('clubs.index', compact('clubs'));
    }

    public function create()
    {
        $owners = User::all();
        return view('clubs.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        Club::create([...$request->all(), 'owner_id' => auth()->user()->id]);

        return redirect()->route('clubs.index')
            ->with('success', 'Club created successfully.');
    }

    public function show(Club $club)
    {
        return view('clubs.show', compact('club'));
    }

    public function edit(Club $club)
    {
        $owners = User::all();
        return view('clubs.edit', compact('club', 'owners'));
    }

    public function update(Request $request, Club $club)
    {
        if (auth()->id() !== $club->owner_id) {
            throw ValidationException::withMessages([
                'owner' => 'You must be the owner of the club to perform this action.'
            ]);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
//            'owner_id' => 'required|exists:users,id',
        ]);


        $club->update($request->all());

        return redirect()->route('clubs.index')
            ->with('success', 'Club updated successfully.');
    }

    public function destroy(Club $club)
    {
        $club->delete();
        return redirect()->route('clubs.index')
            ->with('success', 'Club deleted successfully.');
    }
}