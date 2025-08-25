<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameParticipant;
use App\Models\User;
use App\Models\City;
class GamePagesController extends Controller
{

    public function host(Game $game)
    {
        $gameRoles = Game::roles;
        $slots = Game::slots;
        foreach ($game->slots()->get() as $slot) {
//            dd($participant);
            $user = User::find($slot->user_id);
            if (!empty($user->avatar)) {
                $avatar = asset('storage/' . $user->avatar) ;
            } else {
                $avatar = '/img/no-avatar.svg';
            }
            $slots[$slot->slot] = [
                'user_id' => $slot->user_id,
                'name' => $slot->user_id ? User::find($slot->user_id)->name : null,
                'role' => $slot->role,
                'status' => $slot->status ?? 'alive',
                'avatar' => $avatar,
            ];
        }

        $speakerOptions =[];
        foreach ($slots as $slotKey => $slotData) {
            $speakerOptions[$slotKey] = $slotKey.' : '. $slotData['name'] ?? 'Unknown';
        }

        $citySelector = City::getCitySelector();
//        dd($slots);
        return view('games.host', [
            'game' => $game,
            'slots' => $slots,
            'gameRoles' => $gameRoles,
            'speakerOptions' => $speakerOptions,
            'citySelector' => $citySelector,
            'styles' => ['game-host.css'],
            'scripts' => ['game-host.js'],
            'mode' => 'edit'
        ]);
    }

    public function update(Request $request, Game $game)
    {
        // Accept only the fields you expect
        $validated = $request->validate([
            'phase' => 'required|string',
            'table' => 'nullable|integer',
            'slots' => 'nullable|array',
            'slots.*.user_id' => 'nullable|exists:users,id',
            'slots.*.role' => 'nullable|in:citizen,mafia,don,sheriff',
        ]);

        $day = $game->props['day'] ?? 0;

        $phase = $validated['phase'];

        if ($validated['phase'] === 'shuffle-slots') {
            $validated['slots'] = array_map(function ($slot) {
                return ['user_id' => $slot['user_id']];
            }, $validated['slots']);
            foreach ($validated['slots'] as $i => $slot) {

                GameParticipant::updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'slot' => $i
                    ],
                    $slot
                );
            }
            $phase = 'shuffle-roles';
            $phaseTitle = 'Раздача ролей';

        }elseif ($validated['phase'] === 'shuffle-roles') {
            $validated['slots'] = array_map(function ($slot) {
                return ['role' => $slot['role']];
            }, $validated['slots']);

            for ($i = 1; $i < 11; $i++) {
                $validated['slots'][$i]['role'] ??= 'citizen';
            }
//            dd($validated['slots']);
            foreach ($validated['slots'] as $i => $slot) {

                GameParticipant::updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'slot' => $i
                    ],
                    $slot
                );
            }
            $phase = 'night';
            $phaseTitle = 'Ночь - Договорка';
            $day = 0;
            $subPhase = 'cahoot';

        }

        $game->update([

            'props' => array_merge($game->props, [
//                'table' => $validated['table'],
//                'slots' => $validated['slots'] ?? [],
                'phase' => $phase,
                'phase-title' => $phaseTitle,
                'day' => $day,
                'sub-phase' => $subPhase ?? 'none',
            ])
        ]);

        return response()->json([
            'redirect' => url()->current() // Refresh same page
        ]);
    }

    public function phase(Request $request, Game $game)
    {
        // Accept only the fields you expect
        $validated = $request->validate([
            'phase' => 'required|string',
            'day' => 'nullable|integer',
            'title' => 'nullable|string',
        ]);

        $phase = $validated['phase'];
        $day = $validated['day'] ?? $game->props['day'] ?? 0;
        $title = $validated['title'] ?? $game->props['phase-title'] ?? '';

        if ($phase === 'back') {
            [$phase, $day, $title ] = $game->getBackPhase();
        }

        $game->update([

            'props' => array_merge($game->props, [
                'phase' => $phase,
                'phase-title' => $title,
                'day' => $day,
            ])
        ]);

        return response()->json([
            'redirect' => route('games.host', ['game' => $game->id]) // Refresh same page
        ]);
    }

    public function eliminateForm(Request $request, Game $game, int $slot)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        $slotParticipant = $game->slots()->where('slot', $slot)->first();

        return view('games.eliminate-form', [
            'game' => $game,
            'slot' => $slotParticipant,
            'layout' => $layout,
            'mode' => 'show',
//            'styles' => ['tournaments.css']
        ]);
    }

    public function eliminate(Request $request, Game $game, int $slot)
    {
        $slotParticipant = $game->slots()->where('slot', $slot)->first();

        $slotParticipant->update([
            'status' => 'eliminated'
        ]);

        return response()->json([
            'slot' => $slot,
            'status' => 'eliminated'


        ]);
    }

    public function restoreForm(Request $request, Game $game, int $slot)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        $slotParticipant = $game->slots()->where('slot', $slot)->first();

        return view('games.restore-form', [
            'game' => $game,
            'slot' => $slotParticipant,
            'layout' => $layout,
            'mode' => 'show',
//            'styles' => ['tournaments.css']
        ]);
    }

    public function restore(Request $request, Game $game, int $slot)
    {
        $slotParticipant = $game->slots()->where('slot', $slot)->first();

        $slotParticipant->update([
            'status' => 'alive'
        ]);

        return response()->json([
            'slot' => $slot,
            'status' => 'alive'
        ]);
    }
}
