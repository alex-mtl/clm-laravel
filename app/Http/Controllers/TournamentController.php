<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Club;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TournamentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    protected function rules(?Tournament $tournament = null): array
    {
        return [
            'club_id' => 'nullable|exists:clubs,id',
            'name' => 'required|string|max:255',
            'date_start' => 'required|date',
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
            'logo' => 'nullable|image|mimes:webp,jpeg,png,svg,jpg|max:2048|ratio:1,1',
            'banner' => 'nullable|image|mimes:webp,jpeg,png,svg,jpg|max:5120|ratio:3,1',
            'stream_banner' => 'nullable|image|mimes:webp,jpeg,svg,png,jpg|max:5120|ratio:16,9',
        ];
    }
    public function index()
    {
        $tournaments = Tournament::with('club')->get();
        return view('tournaments.index', compact('tournaments'));
    }

    public function create(Club $club)
    {
        $this->authorize('create_tournament', $club);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $nextFriday = Carbon::now()->next(Carbon::FRIDAY);
        $nextSunday = $nextFriday->copy()->addDays(2);

        // Create new tournament with default dates
        $tournament = new Tournament([
            'phase' => 'draft',
            'date_start' => $nextFriday,
            'date_end' => $nextSunday
        ]);

        return view('tournaments.form', [
            'layout' => $layout,
            'mode' => 'create',
            'club' => $club,
            'styles' => ['tournament-form.css'],
            'tournament' => $tournament,
            'tournamentPhases' => Tournament::PHASES,
        ]);
    }

    public function store(Request $request, Club $club)
    {
        $this->authorize('create_tournament', $club);

        $validated = $request->validate($this->rules());

//        if (!isset($validated['club_id']) && $request->route('club')) {
        if (!isset($validated['club_id'])) {
            $validated['club_id'] = $club->id;
        }

        $files = ['logo', 'banner', 'stream_banner'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $validated[$file] = $request->file($file)->store("tournament-{$file}s", 'public');
            }
        }
        $validated['duration'] = \Carbon\Carbon::parse($validated['date_start'])
                ->diffInDays(\Carbon\Carbon::parse($validated['date_end'])) + 1;

        $tournament = Tournament::create($validated);

//        return redirect()->route('tournaments.index')->with('success', 'Tournament created successfully.');
        return redirect()->route('clubs.show', $tournament->club_id)
            ->with('clm', 'Tournament created!')
            ->with('tab', 'tournaments');
    }

    public function show(Club $club, Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.form', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'show',

            'styles' => ['tournament-form.css'],
            'club' => $club,
            'tournament' => $tournament,
            'tournamentPhases' => Tournament::PHASES,
        ]);
    }

    public function edit(Club $club, Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.form', [
            compact('tournament'),
            'layout' => $layout,
            'tournament' => $tournament,
            'styles' => ['tournament-form.css'],
            'mode' => 'edit',
            'club' => $club,
            'tournamentPhases' => Tournament::PHASES,

        ]);
    }

    public function update(Request $request, Club $club, Tournament $tournament)
    {
//        dd($request->all());
        $this->authorize('manage_tournament', $tournament);
//        if ($request->hasFile('logo')) {
//            $file = $request->file('logo');
//            dd([
//                'file_name' => $file->getClientOriginalName(),
//                'extension' => $file->getClientOriginalExtension(),
//                'mime_type' => $file->getMimeType(),
//                'size' => $file->getSize(),
//                'is_valid' => $file->isValid()
//            ]);
//        }

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

        $validated['duration'] = \Carbon\Carbon::parse($validated['date_start'])
                ->diffInDays(\Carbon\Carbon::parse($validated['date_end'])) + 1;

        $tournament->update($validated);

//        return redirect()->route('tournaments.index')->with('success', 'Tournament updated successfully.');
        return redirect()->route('clubs.show', $tournament->club_id)
            ->with('clm', 'Tournament updated!')
            ->with('tab', 'tournaments');
    }

    public function destroy(Club $club, Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);
        $tournament->delete();
        return redirect()->route('tournaments.index')->with('success', 'Tournament deleted successfully.');
    }
}
