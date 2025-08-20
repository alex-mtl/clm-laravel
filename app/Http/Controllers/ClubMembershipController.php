<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubRequest;
use Illuminate\Http\Request;

class ClubMembershipController extends Controller
{
    // Request to join club
    public function requestJoin(Request $request, Club $club)
    {
        $request->validate(['message' => 'nullable|string']);

        ClubRequest::create([
            'user_id' => auth()->id(),
            'club_id' => $club->id,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Join request sent!');
    }

    // Approve join request
    public function approveRequest(ClubRequest $joinRequest)
    {
        $this->authorize('approve', $joinRequest);

        $joinRequest->update(['status' => 'approved']);

        ClubMember::firstOrCreate([
            'user_id' => $joinRequest->user_id,
            'club_id' => $joinRequest->club_id
        ]);

        return back()->with('success', 'Request approved!');
    }

    public function declineRequest(ClubRequest $joinRequest)
    {
        $this->authorize('approve', $joinRequest); // Reuse same authorization

        $joinRequest->update([
            'status' => 'rejected',
            'declined_at' => now()
        ]);

        // Optional: Add notification system here
        // Notification::send($joinRequest->user, new RequestDeclined($joinRequest));

        return back()->with('success', 'Request declined successfully');
    }

    // Leave club
    public function leave(Club $club)
    {
        ClubMember::where([
            'user_id' => auth()->id(),
            'club_id' => $club->id
        ])->delete();

        return back()->with('success', 'You left the club');
    }
}
