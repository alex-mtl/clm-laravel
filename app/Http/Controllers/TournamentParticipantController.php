<?php

namespace App\Http\Controllers;

use App\Models\TournamentParticipant;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentParticipantController extends Controller
{
    public function index(Tournament $tournament)
    {
        $participants = $tournament->participants;
        return view('tournament_participants.index', compact('tournament', 'participants'));
    }

    public function create(Tournament $tournament)
    {
        $users = User::all();
        return view('tournament_participants.create', compact('tournament', 'users'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:tournament_participants,user_id,NULL,id,tournament_id,'.$tournament->id
        ]);

        $tournament->participants()->attach($validated['user_id']);

        return redirect()->route('tournaments.participants.index', $tournament)
            ->with('success', 'Participant added successfully.');
    }

    public function destroy(Tournament $tournament, User $user)
    {
        $tournament->participants()->detach($user->id);
        return redirect()->route('tournaments.participants.index', $tournament)
            ->with('success', 'Participant removed successfully.');
    }
}
