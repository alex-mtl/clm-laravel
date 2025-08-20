<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Event;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Event $event)
    {
        $games = $event->games;
        return view('games.index', compact('event', 'games'));
    }

    public function create(Event $event)
    {
        return view('games.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after:start',
            'description' => 'nullable|string',
            'props' => 'nullable|json',
            'protocol' => 'nullable|json'
        ]);

        $event->games()->create($validated);

        return redirect()->route('events.games.index', $event)
            ->with('success', 'Game created successfully.');
    }

    public function show(Event $event, Game $game)
    {
        return view('games.show', compact('event', 'game'));
    }

    public function edit(Event $event, Game $game)
    {
        return view('games.edit', compact('event', 'game'));
    }

    public function update(Request $request, Event $event, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after:start',
            'description' => 'nullable|string',
            'props' => 'nullable|json',
            'protocol' => 'nullable|json'
        ]);

        $game->update($validated);

        return redirect()->route('events.games.index', $event)
            ->with('success', 'Game updated successfully.');
    }

    public function destroy(Event $event, Game $game)
    {
        $game->delete();
        return redirect()->route('events.games.index', $event)
            ->with('success', 'Game deleted successfully.');
    }
}
