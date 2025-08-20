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
            'event' => new Event()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id' => 'sometimes|exists:clubs,id',
            'tournament_id' => 'nullable|exists:tournaments,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'logo' => 'nullable|image'
        ]);

        if (!isset($validated['club_id']) && $request->route('club')) {
            $validated['club_id'] = $request->route('club');
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

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
