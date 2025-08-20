<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Club;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TournamentController extends Controller
{
    protected function rules(?Tournament $tournament = null): array
    {
        return [
            'club_id' => 'nullable|exists:clubs,id',
            'name' => 'required|string|max:255',
            'date_start' => 'required|date|after_or_equal:today',
            'date_end' => 'required|date|after_or_equal:date_start',
            'location' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'quota' => 'nullable|integer|min:1',
            'players_quota' => 'nullable|integer|min:1',
            'games_quota' => 'nullable|integer|min:1',
            'prize' => 'nullable|numeric|min:0',
            'participation_fee' => 'nullable|numeric|min:0',
            'phase' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,svg,jpg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,svg,jpg|max:5120',
            'stream_banner' => 'nullable|image|mimes:jpeg,svg,png,jpg|max:5120'
        ];
    }
    public function index()
    {
        $tournaments = Tournament::with('club')->get();
        return view('tournaments.index', compact('tournaments'));
    }

    public function create(Club $club)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $nextFriday = Carbon::now()->next(Carbon::FRIDAY);
        $nextSunday = $nextFriday->copy()->addDays(2);

        // Create new tournament with default dates
        $tournament = new Tournament([
            'date_start' => $nextFriday,
            'date_end' => $nextSunday
        ]);

        return view('tournaments.form', [
            'layout' => $layout,
            'mode' => 'create',
            'club' => $club,
            'tournament' => $tournament,
        ]);
    }

    public function store(Request $request)
    {
//        $validated = $request->validate([
//            'club_id' => 'sometimes|exists:clubs,id',
//            'name' => 'required|string|max:255',
//            'date_start' => 'required|date',
//            'date_end' => 'required|date|after_or_equal:date_start',
//            'logo' => 'nullable|image',
//            'banner' => 'nullable|image',
//            'stream_banner' => 'nullable|image'
//        ]);

        $validated = $request->validate($this->rules());

        if (!isset($validated['club_id']) && $request->route('club')) {
            $validated['club_id'] = $request->route('club');
        }

        $files = ['logo', 'banner', 'stream_banner'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $validated[$file] = $request->file($file)->store("tournament-{$file}s", 'public');
            }
        }
        $validated['duration'] = $validated['date_start']->diffInDays($validated['date_end']) + 1;

        $tournament = Tournament::create($validated);

//        return redirect()->route('tournaments.index')->with('success', 'Tournament created successfully.');
        return redirect()->route('clubs.show', $tournament->club_id)
            ->with('clm', 'Tournament created!')
            ->with('tab', 'tournaments');
    }

    public function show(Club $club, Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.form', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'show',
            'club' => $club,
            'tournament' => $tournament
        ]);
    }

    public function edit(Club $club, Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.form', [
            compact('tournament'),
            'layout' => $layout,
            'tournament' => $tournament,
            'mode' => 'edit',
            'club' => $club

        ]);
    }

    public function update(Request $request, Club $club, Tournament $tournament)
    {

        $validated = $request->validate($this->rules());

        if (!isset($validated['club_id']) && $request->route('club')) {
            $validated['club_id'] = $club->id;
        }

//        dd($validated);
        $files = ['logo', 'banner', 'stream_banner'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $validated[$file] = $request->file($file)->store("tournament-{$file}s", 'public');
            }
        }

        $validated['duration'] = $validated['date_start']->diffInDays($validated['date_end']) + 1;

        $tournament->update($validated);

//        return redirect()->route('tournaments.index')->with('success', 'Tournament updated successfully.');
        return redirect()->route('clubs.show', $tournament->club_id)
            ->with('clm', 'Tournament updated!')
            ->with('tab', 'tournaments');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return redirect()->route('tournaments.index')->with('success', 'Tournament deleted successfully.');
    }
}
