<?php

namespace App\Http\Controllers;

use App\Models\GameParticipant;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class GameParticipantController extends Controller
{
    public function index(Game $game)
    {
        $participants = $game->participants;
        return view('game_participants.index', compact('game', 'participants'));
    }

    public function create(Game $game)
    {
        $users = User::all();
        return view('game_participants.create', compact('game', 'users'));
    }

    public function store(Request $request, Game $game)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:game_participants,user_id,NULL,id,game_id,'.$game->id,
            'role' => 'nullable|string|max:16',
            'slot' => 'nullable|integer',
            'status' => 'nullable|string|max:16'
        ]);

        $game->participants()->attach($validated['user_id'], [
            'role' => $validated['role'] ?? null,
            'slot' => $validated['slot'] ?? null,
            'status' => $validated['status'] ?? null
        ]);

        return redirect()->route('games.participants.index', $game)
            ->with('success', 'Participant added successfully.');
    }

    public function edit(Game $game, User $user)
    {
        $participant = $game->participants()->where('user_id', $user->id)->first();
        return view('game_participants.edit', compact('game', 'participant'));
    }

    public function update(Request $request, Game $game, User $user)
    {
        $validated = $request->validate([
            'role' => 'nullable|string|max:16',
            'slot' => 'nullable|integer',
            'status' => 'nullable|string|max:16'
        ]);

        $game->participants()->updateExistingPivot($user->id, $validated);

        return redirect()->route('games.participants.index', $game)
            ->with('success', 'Participant updated successfully.');
    }

    public function destroy(Game $game, User $user)
    {
        $game->participants()->detach($user->id);
        return redirect()->route('games.participants.index', $game)
            ->with('success', 'Participant removed successfully.');
    }
}
