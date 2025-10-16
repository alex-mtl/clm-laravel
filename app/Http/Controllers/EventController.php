<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use App\Models\Tournament;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['club', 'tournament'])->get();
        return view('events.index', compact('events'));
    }

    public function create(Club $club)
    {
        $this->authorize('manage_club', $club);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        $clubs = Club::all();

        $tournaments = Tournament::all()->prepend(
            (object) ['id' => 'null', 'name' => '<empty>']
        )->pluck('name', 'id');

        return view('events.form', [
            compact('clubs'),
            'club' => $club,
            'tournaments' => $tournaments,
            'layout' => $layout,
            'mode' => 'create',
            'event' => new Event([ 'date_start' => now(), 'date_end' => now() ])
        ]);
    }

    public function store(Request $request, Club $club)
    {
        $this->authorize('manage_club', $club);
        $request->merge([
            'tournament_id' => $request->tournament_id === 'null' ? null : $request->tournament_id
        ]);
        $validated = $request->validate([
            'club_id' => 'sometimes|exists:clubs,id',
            'tournament_id' => 'nullable|exists:tournaments,id',
            'name' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'description' => 'nullable|string',
            'logo' => 'nullable|image'
        ]);

//        if (!isset($validated['club_id']) && $request->route('club')) {
        if (!isset($validated['club_id']) ) {
            $validated['club_id'] = $club->id;
        }

        if(isset($validated['tournament_id']) && $validated['tournament_id'] == 'null') {
            $validated['tournament_id'] = null;
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('event-logos', 'public');
        }

        $event = Event::create($validated);

//        return redirect()->route('events.index')->with('success', 'Event created successfully.');
        return redirect()->route('clubs.show', $event->club_id)
            ->with('clm', 'Event created!')
            ->with('tab', 'events');
    }

    public function show(Club $club, Event $event)
    {
        $this->authorize('manage_club', $club);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $tournaments = Tournament::all()->prepend(
            (object) ['id' => 'null', 'name' => '<empty>']
        )->pluck('name', 'id');

        return view('events.form', [
            compact('event'),
            'tournaments' => $tournaments,
            'layout' => $layout,
            'mode' => 'show',
            'club' => $club,
            'event' => $event,

        ] );
    }

    public function edit(Club $club, Event $event)
    {
        $this->authorize('manage_club', $club);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $clubs = Club::all();

        // Always prepend empty option to tournaments
        $tournaments = Tournament::all()->prepend(
            (object) ['id' => 'null', 'name' => '<empty>']
        )->pluck('name', 'id');
//        return view('events.edit', compact('event', 'clubs', 'tournaments'));
        return view('events.form', [
            compact('event'),
            'tournaments' => $tournaments,
            'layout' => $layout,
            'mode' => 'edit',
            'club' => $club,
            'event' => $event,

        ] );
    }

    public function update(Request $request, Club $club, Event $event)
    {
        $this->authorize('manage_club', $club);
        $validated = $request->validate([
            'club_id' => 'sometimes|exists:clubs,id',
            'tournament_id' => 'nullable|exists:tournaments,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'logo' => 'nullable|image'
        ]);

        if (!isset($validated['club_id']) && $request->route('club')) {
            $validated['club_id'] = $club->id;
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('event-logos', 'public');
        }

        $event->update($validated);

//        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
        return redirect()->route('clubs.show', $event->club_id)
            ->with('clm', 'Event updated!')
            ->with('tab', 'events');
    }

    public function destroy(Club $club, Event $event)
    {
        $this->authorize('manage_club', $club);
        $event->delete();
//        return redirect()->route('clubs.events.index', $club)->with('success', 'Event deleted successfully.');

        return redirect()->route('clubs.show', $club)
            ->with('clm', 'Event deleted!')
            ->with('tab', 'events');
    }
}
