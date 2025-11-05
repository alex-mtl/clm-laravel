<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameParticipant;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class GamePagesController extends Controller
{

    public function host(Game $game)
    {
        $gameRoles = Game::ROLES;
        $slots = Game::slots;

        if(!isset($game->props['day'])) {
            $props = $game->props;
            $props['day'] = 0;
            $game->update([
                'props' => $props
            ]);
        }

        $day = $game->props['day'];

        $nominees = $game->props['days']['D'.$day]['nominees'] ?? [];
        if(empty($game->props['phase-code']) && $game->props['phase'] == 'shuffle-slots') {
            $props = $game->props;
            $props['day'] = 0;
            $props['phase-code'] = 'SHUFFLE-SLOTS';
            $props['days']['D0'] = ['nominees' => [], 'votes' => [], 'speakers' => [], 'active_speaker' => null];
            $game->update(['props' => $props]);
        } elseif(empty($game->props['phase-code'])) {
            $props = $game->props;
            $props['phase-code'] = 'SHUFFLE-SLOTS';
            $game->update(['props' => $props]);
        }
//        dd( $game->props['phase-code'] );

        $slots = $game->slots()->get();

        if ($slots->isEmpty()) {
            // Создаем слоты по умолчанию
            for ($i = 1; $i <= 10; $i++) { // или нужное количество слотов
                GameParticipant::updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'slot' => $i
                    ],
                    [
                        'role' => 'citizen',
                        'role_title' => Game::ROLES['citizen'] ?? 'Гражданин', // исправлено на role_title
                        'status' => 'alive',
                        'warns' => 0,
                        'avatar' => null,
                        'candidate' => 0,
                        'score_base' => 0,
                        'score_1' => 0,
                        'score_2' => 0,
                        'score_3' => 0,
                        'score_4' => 0,
                        'score_5' => 0,
                        'score_total' => 0,
                        'mark' => 'zero',
                    ]
                );
            }

            // Перезагружаем слоты после создания
            $slots = $game->slots()->get();
        }

        foreach ($slots as $slot) {
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
                'role-title' => Game::ROLES[$slot?->role ?? 'citizen'],
                'status' => $slot->status ?? 'alive',
                'warns' => $slot->warns ?? 0,
                'avatar' => $avatar,
                'candidate' => $nominees[$slot->slot] ?? 0,
                'score_base'  => $slot->score_base ?? 0,
                'score_1'     => $slot->score_1 ?? 0,
                'score_2'     => $slot->score_2 ?? 0,
                'score_3'     => $slot->score_3 ?? 0,
                'score_4'     => $slot->score_4 ?? 0,
                'score_5'     => $slot->score_5 ?? 0,
                'score_total' => $slot->score_total ?? 0,
                'mark' => $slot->mark ?? 'zero',
            ];
        }


//        dd($slots);
//        dd($slots);
        $speakerOptions =[];
        $marks = Game::MARK_OPTIONS;
        foreach ($slots as $slotKey => $slotData) {
            $speakerOptions[$slotKey] = $slotKey.' : '. ($slotData['name'] ?? 'Unknown');
        }

        $citySelector = City::getCitySelector();

        $toasts = $game->getToasts();
//        dd($game->props);
        return view('games.host', [
            'game' => $game,
            'slots' => $slots,
            'day' => $day,
            'gameRoles' => $gameRoles,
            'speakerOptions' => $speakerOptions,
            'marks' => $marks,
            'toasts' => $toasts,
            'citySelector' => $citySelector,
            'styles' => ['game-host.css'],
            'scripts' => ['game-host.js'],
            'mode' => 'edit'
        ]);
    }

    public function stream(string $key)
    {
        if (empty($key) || preg_match('/[a-z0-9]./', $key)) {
            abort(404);
        }
        $game = Game::where('props->stream->stream-key', $key)
            ->where('props->stream->enabled', 'live')
            ->orderByDesc('id')
            ->first();

        if ($game === null) {
            abort(404);
        }

        if(empty($game->props['init'] ?? null)) {
            $game->slotsInit();
            $game->update(['props' => array_merge($game->props ?? [], ['init' => true])]);
        }


        $gameRoles = Game::ROLES;
        $slots = Game::slots;

        foreach ($game->slots()->get() as $slot) {
            $user = User::find($slot->user_id);
            if (!empty($user->avatar)) {
                $avatar = asset('storage/' . $user->avatar) ;
            } else {
                $avatar = '/img/no-avatar.svg';
            }
            $slots[$slot->slot] = [
                'user_id' => $slot->user_id,
                'name' => $slot->user_id ? User::find($slot->user_id)->name : null,
                'role' => $slot?->role ?? 'citizen',
                'role-title' => Game::ROLES[$slot?->role ?? 'citizen'],
                'status' => $slot->status ?? 'alive',
                'warns' => $slot->warns ?? 0,
                'avatar' => $avatar,
                'score_base'  => $slot->score_base ?? 0,
                'score_1'     => $slot->score_1 ?? 0,
                'score_2'     => $slot->score_2 ?? 0,
                'score_3'     => $slot->score_3 ?? 0,
                'score_4'     => $slot->score_4 ?? 0,
                'score_5'     => $slot->score_5 ?? 0,
                'score_total' => $slot->score_total ?? 0,
                'mark' => $slot->mark ?? 'zero',
            ];
        }

        $currentGame = [
            'id' => $game->id,
            'streamKey' => $game->props['stream']['stream-key'],
            'slots' => $slots,
            'nominations' => [],
            'kills' => [],
            'donChecks' => [],
            'sheriffChecks' => [],
        ];

        return view('games.stream', [
            'game' => $game,
            'currentGame' => $currentGame,
            'gameRoles' => $gameRoles,
            'slots' => $slots,
//            'styles' => ['game-stream.css'],
            'styles' => ['game-stream.css', 'custom.css'],
            'scripts' => ['game-stream.js'],
            'mode' => 'edit'
        ]);
    }

    public function streamState(Game $game,string $key)
    {
        if ($key !== $game->props['stream']['stream-key']) {
            abort(404);
        }

        $gameRoles = Game::ROLES;

//        $killedList = array_values($game->props['killedList'] ?? []);

//        $votedList = array_values($game->props['votedList'] ?? []);
        $phaseTitle = $game->props['phase-title'] ?? '';

        $slots = Game::slots;


        $day = $game->props['day'] ?? 0;

        $killedList = [];
        $bg = [];
        $pc = [];
//        for($i=1; $i<=$day; $i++) {
        for($i=1; $i<=10; $i++) {
            if(isset($game->props['days']['D'.$i]['shooting'])) {
                $victim = $game->props['days']['D'.$i]['shooting']['victim'] ?? 'X';
                $killedList[$i] = $victim;
                if(isset($game->props['days']['D'.$i]['best-guess'])) {
                    $bg[$victim] = $game->props['days']['D'.$i]['best-guess'];
                }

                if(isset($game->props['days']['D'.$i]['protocol-color'])) {
                    $pc[$victim] = $game->props['days']['D'.$i]['protocol-color'];
                }
            }
        }

        $votedList = [];

//        for($i=1; $i<=$day; $i++) {
        for($i=1; $i<=10; $i++) {
            if(isset($game->props['days']['D'.$i]['voting'])) {
                $votedList = array_merge( $votedList, $game->props['days']['D'.$i]['voting']['result'] ?? []);
            }
        }

        if($game->props['phase'] === 'day') {
            $nominees = $game->props['days']['D'.$day]['nominees'] ?? [];
        } else {
            $nominees = [];
        }

        $donChecks = [];
//        for($i=1; $i<=$day; $i++) {
        for($i=1; $i<=10; $i++) {
            if(isset($game->props['days']['D'.$i]['don-check'])) {
                $donChecks[] = $game->props['days']['D' . $i]['don-check']['target'] ?? 'X';
            }
        }

        $sheriffChecks = [];
//        for($i=1; $i<=$day; $i++) {
        for($i=1; $i<=10; $i++) {
            if(isset($game->props['days']['D'.$i]['sheriff-check'])) {
                $sheriffChecks[] = $game->props['days']['D' . $i]['sheriff-check']['target'] ?? 'X';
            }
        }

        foreach ($game->slots()->get() as $slot) {
//            dd($participant);
            $user = User::find($slot->user_id);
            if (!empty($user->avatar)) {
                $avatar = asset('storage/' . $user->avatar) ;
            } else {
                $avatar = '/img/no-avatar.svg';
            }

//            $random = rand(0, 3);
            $slots[$slot->slot] = [
                'user_id' => $slot->user_id,
                'name' => $slot->user_id ? User::find($slot->user_id)->name : null,
                'role' => $slot->role,
                'role-title' => Game::ROLES[$slot?->role ?? 'citizen'],
                'status' => $slot->status ?? 'alive',
                'warns' => $slot->warns ?? 0,
                'avatar' => $avatar,
                'score_base'  => $slot->score_base ?? 0,
                'score_1'     => $slot->score_1 ?? 0,
                'score_2'     => $slot->score_2 ?? 0,
                'score_3'     => $slot->score_3 ?? 0,
                'score_4'     => $slot->score_4 ?? 0,
                'score_5'     => $slot->score_5 ?? 0,
                'score_total' => $slot->score_total ?? 0,
                'mark' => $slot->mark ?? 'zero',
            ];
        }


        $gameID = $game->id;
        $gameTitle = $game->name;
        if($game->props['stream']['enabled'] !== 'live') {
            $nextGame = Game::where('event_id', $game->event_id)
                ->where('table', $game->table)
                ->where('props->stream->stream-key', $key)
                ->where('props->stream->enabled', 'live')
                ->first();

            if($nextGame) {
                $gameID = $nextGame->id;
            }

        }
        $currentGame = [
            'id' => $gameID,
            'name' => $gameTitle,
            'streamKey' => $game->props['stream']['stream-key'],
            'showRoles' => $game->props['stream']['show-roles'] ?? 'off',
            'settings' => $game->props['stream'],
            'slots' => $slots,
            'nominations' => array_values($nominees),
            'killedList' => $killedList,
            'bestGuess' => $bg,
            'protocolColor' => $pc,
            'votedList' => $votedList,
            'donChecks' => $donChecks,
            'sheriffChecks' => $sheriffChecks,
            'phaseTitle' => $phaseTitle
        ];

        return response()->json([
            'status' => 'ok',
            'game' => $currentGame
        ]);
    }


    public function update(Request $request, Game $game)
    {
        // Accept only the fields you expect
        $validated = $request->validate([
            'phase-code' => 'nullable|string|in:'.implode(',', array_keys(Game::PHASES_ORDER)),
            'phase' => 'required|string',
            'subPhase' => 'nullable|string',
            'sub-phase' => 'nullable|string',
            'table' => 'nullable|integer',
            'day' => 'nullable|integer',
            'slots' => 'nullable|array',
            'slots.*.user_id' => 'nullable|exists:users,id',
            'slots.*.role' => 'nullable|in:citizen,mafia,don,sheriff',
            'scores.*.score_base'  => 'nullable|numeric',
            'scores.*.score_1'     => 'nullable|numeric',
            'scores.*.score_2'     => 'nullable|numeric',
            'scores.*.score_3'     => 'nullable|numeric',
            'scores.*.score_4'     => 'nullable|numeric',
            'scores.*.score_5'     => 'nullable|numeric',
            'scores.*.mark'     => 'nullable|in:m5,m4,m3,m2,m1,zero,p1,p2,p3,p4,p5',
            'scores.*.score_total' => 'nullable|numeric',

        ]);

        if($validated['day'] ?? false) {
            if($validated['day'] !== $game->props['day']) {
//                dd($validated['day'], $game->props['day']);
                $props = $game->props;
                $props['day'] = $validated['day'];
                $game->update([
                    'props' => $props
                ]);
            }
        }
//        $day = $game->props['day'] ?? 0;
        $day = $validated['day'] ?? $game->props['day'] ?? 0;
//        dd($validated['day'], $game->props['day'], $day);

        $phase = $validated['phase'];
        $subPhase = $validated['subPhase'] ?? $validated['sub-phase'] ??'none';
        $code = $validated['phase-code'] ?? strtoupper($phase);
//

        if ($validated['phase-code'] ) {

//            $nextPhase = $game->getNextPhase($code);
            $nextPhase = $game->getPhase($code);
        }

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

            $nextPhase = $game->getNextPhase($code);
//            dd($nextPhase);
            $currentProps = $nextPhase;
//            $currentProps = [
//                'prev-phase' => 'SHUFFLE-SLOTS',
//                'phase-code' => 'SHUFFLE-ROLES',
//                'phase' => 'shuffle-roles',
//                'sub-phase' => 'none',
//                'phase-title' => 'Раздача ролей',
//                'next-phase' => 'SHERIFF-SIGN',
//            ];

        } elseif ($validated['phase'] === 'shuffle-roles') {
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
            $currentProps = $nextPhase;
//            $currentProps = [
//                'phase-code' => 'SHUFFLE-ROLES',
//                'phase' => 'night',
//                'phase-title' => 'Ночь - Договорка',
//                'day' => 0,
//                'subPhase' => 'cahoot',
//            ];

        } elseif ($validated['phase'] === 'game-over') {
            $currentProps = $nextPhase;
//            $currentProps = [
//                'phase' => 'score',
//                'phase-title' => 'Результаты (очки)',
//                'subPhase' => '',
//            ];

        } elseif ($validated['phase'] === 'night') {

            $currentProps = $nextPhase;
            $currentProps['day'] = $game->props['day'];
//            $currentProps = [
//                'phase' => $phase,
//                'sub-phase' => $subPhase,
//                'phase-title' => Game::PHASES[$phase][$subPhase]['title'] ?? 'UNKNOWN '. $subPhase,
//                'timer' => Game::PHASES[$phase][$subPhase]['timer'] ?? 0,
////                'subPhase' => '',
//            ];

        } elseif ($validated['phase'] === 'day') {
//            dd($game->props, $request->all());
            $currentProps = $nextPhase;
            $currentProps['day'] = $game->props['day'];
//            $currentProps = [
//                'phase' => $phase,
//                'sub-phase' => $subPhase,
//                'phase-title' => Game::PHASES[$phase][$subPhase]['title'] ?? 'UNKNOWN '. $phase.' '.$subPhase,
//                'timer' => Game::PHASES[$phase][$subPhase]['timer'] ?? 0,
//            ];

            if($subPhase === 'first-speaker') {

                $newDay = $validated['day'] ?? $day;
                if(!isset($game->props['days'])) {
                    $currentProps['days'] = [];
                } else {
                    $currentProps['days'] = $game->props['days'];
                }
                if(!isset($game->props['days']['D'.$newDay])) {
                    $currentProps['days']['D'.$newDay] = ['nominees' => [], 'votes' => [], 'speakers' => [], 'active_speaker' => null];
                }
                $currentProps['day'] = $newDay;

                if ($newDay === 0) {
                    $minAliveSlotNumber = $game->slots()
                        ->where(function($query) {
                            $query->where('status', 'alive')
                                ->orWhereNull('status');
                    })
                        ->min('slot');
                    $currentProps['active_speaker'] = $minAliveSlotNumber;
                    $currentProps['speakers'] = [ $minAliveSlotNumber ];
                } else {
//                    dd($game->props);
//                    dd(isset($game->props['days']['D'.$newDay]['speakers']), count($game->props['days']['D'.$newDay]['speakers']));
                    if(!isset($game->props['days']['D'.$newDay]['speakers']) || count($game->props['days']['D'.$newDay]['speakers']) === 0) {

//                        $aliveSlots = $game->slots()->where('status', 'alive')->pluck('slot')->toArray();
                        $aliveSlots = $game->slots()
                            ->where(function($query) {
                                $query->where('status', 'alive')
                                    ->orWhereNull('status');
                            })->pluck('slot')->toArray();
                        $minAliveSlotNumber = 0;
                        foreach ($aliveSlots as $slot) {
                            for($i=0; $i<=$newDay; $i++) {
                                if(isset($game->props['days']['D'.$i]['speakers']) && count($game->props['days']['D'.$i]['speakers']) > 0) {
                                    if($slot === $game->props['days']['D'.$i]['speakers'][0]) {
                                        continue 2;
                                    }
                                }
                            }
                            $minAliveSlotNumber = $slot;
                            break;
                        }
                        $currentProps['active_speaker'] = $minAliveSlotNumber;
                        $currentProps['speakers'] = [ $minAliveSlotNumber ];

                        $currentProps['days']['D'.$newDay]['active_speaker'] = $minAliveSlotNumber;
                        $currentProps['days']['D'.$newDay]['speakers'] = [ $minAliveSlotNumber];
                    }

                }


            }


        } elseif ($validated['phase'] === 'score') {
            foreach ($validated['scores'] as $i => $scores) {
                GameParticipant::updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'slot'    => $i,
                    ],
                    [
                        'score_base'  => $scores['score_base'] ?? 0,
                        'score_1'     => $scores['score_1'] ?? 0,
                        'score_2'     => $scores['score_2'] ?? 0,
                        'score_3'     => $scores['score_3'] ?? 0,
                        'score_4'     => $scores['score_4'] ?? 0,
                        'score_5'     => $scores['score_5'] ?? 0,
                        'score_total' => $scores['score_total'] ?? 0,
                        'mark' => $scores['mark'] ?? 'zero',
                        'mark_number' => Game::MARK_OPTIONS[$scores['mark'] ?? 'zero'],
                    ]
                );
            }

            $currentProps = $nextPhase;
//
//            $currentProps = [
//                'phase' => 'finished',
//                'phase-title' => 'Игра окончена',
//                'sub-phase' => 'none',
//            ];

        }
//        'props' => array_merge($game->props, [
//        'phase' => $phase,
//        'phase-title' => $phaseTitle,
//        'day' => $day,
//        'sub-phase' => $subPhase ?? 'none',
//    ])

        $game->update([
            'props' => [...($game->props ?? []), ...$currentProps]
        ]);

        return response()->json([
            'redirect' => url()->current() // Refresh same page
        ]);
    }

    public function phase(Request $request, Game $game)
    {
        // Accept only the fields you expect
        $validated = $request->validate([
            'phase-code' => 'nullable|string|in:'.implode(',', array_keys(Game::PHASES_ORDER)),
            'phase' => 'required|string',
            'day' => 'nullable|integer',
            'title' => 'nullable|string',
        ]);

        $phaseCode = $validated['phase-code'];
        $phase = $validated['phase'];
        $day = $validated['day'] ?? $game->props['day'] ?? 0;
        $title = $validated['title'] ?? $game->props['phase-title'] ?? '';

        if ($phase === 'back') {
            $phaseProps = $game->getBackPhase($phaseCode, $day);

        }

        $game->update([
            'props' => array_merge($game->props, $phaseProps ?? [])
        ]);

        return response()->json([
            'redirect' => route('games.host', ['game' => $game->id]) // Refresh same page
        ]);
    }

    public function marksCalc(Request $request)
    {
        // Accept only the fields you expect
//        dd($request->all());
        $team = $request->input('team');

        if ($team !== 'mafia') {
            $teamMarks = $request->input('marks');
            foreach ($teamMarks as $player) {
                $player['points'] = 0;
            }
        } else {
            $teamMarks = $request->input('marks');
            $lookup = "";
            $fixed = false;
            $prev = 0;
//            $next = 0;
            foreach ($teamMarks as $player) {
                $lookup .= $player['number'] . ",";
                if(($player['number'] - $prev) >= 3) {
                    $fixed = true;
                } else {
                    $fixed = false;
                }
                $prev = $player['number'];
            }
            $lookup = substr($lookup, 0, -1);
            if($fixed) {
                $res = [0, 0, 0.7];
            } else {
                $res = config('points.points')[$lookup];
            }

            foreach ($res as $key => $value) {
                $teamMarks[$key]['points'] = $value;
            }

        }
        return response()->json([
            'marks' => $teamMarks
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



    public function votingForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        $day = $game->props['day'] ?? 0;
        $dayOptions = ['D0' => "День 0"];
        if(!empty($game->props['days'] ?? [])) {
            for($i=1; $i<=10; $i++) {
                if(array_key_exists('D'.$i, $game->props['days'])) {
                    $dayOptions['D'.$i] = "День ".$i;
                }
            }
        }

        $nominees = $game->props['days']['D'.$day]['nominees'] ?? [];
        $votedList = $game->props['days']['D'.$day]['voting']['result'] ?? [];
        $slots = $game->slots()->get();
        $alive =0;
        foreach ($slots as $slot) {
            if(($slot->status ?? 'alive') === 'alive') {
                $alive++;
            } else {

            }
        }

        return view('games.forms.voting-form', [
            'game' => $game,
            'alive' => $alive,
            'votingDay' => 'D'.$day,
            'dayOptions' => $dayOptions,
            'votedList' => $votedList,
            'nominees' => $nominees,
            'layout' => $layout,
            'mode' => 'show',
//            'styles' => ['tournaments.css']
        ]);
    }


    public function shootingForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $slots = $game->slots()->get();
        $mafTeam = [];
        foreach ($slots as $slot) {
            if(($slot->status ?? 'alive') === 'alive') {
                if(in_array($slot->role, ['mafia', 'don'])){
                    $mafTeam[$slot->slot] = 0;
                }
            }
        }

        if($request->input('addDay','false') == 'true') {
            $props = $game->props;
            $day = $props['day'];
            $props['day'] = $day+1;
            if(!isset($game->props['days']['D'.$props['day']])) {
                $props['days']['D'.$props['day']] = ['nominees' => [], 'votes' => [], 'speakers' => [], 'active_speaker' => null];
            }

            $game->update([
                'props' => $props
            ]);

        }

        $day = $game->props['day'] ?? 1;
        $dayOptions = ['D1' => "Ночь 1"];
        for($i=2; $i<=10; $i++) {
            if(array_key_exists('D'.$i, $game->props['days'])) {
                $dayOptions['D'.$i] = "Ночь ".$i;
            }
        }
        $mafia = $game->props['days']['D'.$day]['shooting']['mafia'] ?? [];
//        dd($props['days']['D'.$day]['shooting']);
        foreach ($mafia as $key => $value) {
            if (array_key_exists($key, $mafTeam)) {
                $mafTeam[$key] = $value;
            }
        }

        return view('games.forms.shooting-form', [
            'game' => $game,
            'mafTeam' => $mafTeam,
            'shootingDay' => 'D'.$day,
            'dayOptions' => $dayOptions,
            'mafia' => $mafia,
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }
    public function donCheckForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $slots = $game->slots()->get();
        $roles = [];
        foreach ($slots as $slot) {
            $roles[$slot->slot] = $slot->role;
        }

        $day = $game->props['day'] ?? 1;
        $dayOptions = ['D1' => "Ночь 1"];
        for($i=2; $i<=10; $i++) {
            if(array_key_exists('D'.$i, $game->props['days'] ?? [])) {
                $dayOptions['D'.$i] = "Ночь ".$i;
            }
        }

        $donCheck = $game->props['days']['D'.$day]['don-check']['target'] ?? 0;


        return view('games.forms.don-check-form', [
            'game' => $game,
            'roles' => $roles,
            'donCheck' => $donCheck,
            'dayOptions' => $dayOptions,
            'donCheckDay' => 'D'.$day,
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }

    public function protocolColorForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $day = $game->props['day'] ?? 1;
        $dayOptions = ['D1' => "Ночь 1"];
        for($i=2; $i<=10; $i++) {
            if(array_key_exists('D'.$i, $game->props['days'])) {
                $str = "Ночь ".$i;
                if(isset($game->props['days']['D'.$i]['shooting']['victim'])) {
                    $str .= ' Убит игрок # '.$game->props['days']['D'.$i]['shooting']['victim'];
                } else {
                    $str .= ' Никто не убит';
                }
                $dayOptions['D'.$i] = $str;
            }
        }

        $protocolColor = $game->props['days']['D'.$day]['protocol-color'] ?? [
            'slot' => 0,
            'color' => 'red'
        ];


        return view('games.forms.protocol-color-form', [
            'game' => $game,
            'protocolColorDay' => 'D'.$day,
            'dayOptions' => $dayOptions,
            'slot' => $protocolColor['slot'],
            'color' => $protocolColor['color'],
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }

    public function bestGuessForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $day = $game->props['day'] ?? 0;
        if($day === 0) {
            throw new \Exception('Day not set');
        }
        $bestGuess = ["M1" => 0, "M2" => 0, "M3" => 0];
        $bg = $game->props['days']['D'.$day]['best-guess'] ?? null;
        if(!empty($bg)) {
            foreach ($bg as $key => $value) {
                $bestGuess[$key] = $value['slot'];
            }
        }


        return view('games.forms.best-guess-form', [
            'game' => $game,
            'bestGuess' => $bestGuess,
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }


    public function sheriffCheckForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $slots = $game->slots()->get();
        $roles = [];
        foreach ($slots as $slot) {
            $roles[$slot->slot] = $slot->role;
        }

        $day = $game->props['day'] ?? 1;
        $dayOptions = ['D1' => "Ночь 1"];
        for($i=2; $i<=10; $i++) {
            if(array_key_exists('D'.$i, $game->props['days'])) {
                $dayOptions['D'.$i] = "Ночь ".$i;
            }
        }

        $sheriffCheck = $game->props['days']['D'.$day]['sheriff-check']['target'] ?? 0;



        return view('games.forms.sheriff-check-form', [
            'game' => $game,
            'roles' => $roles,
            'dayOptions' => $dayOptions,
            'sheriffCheckDay' => 'D'.$day,
            'sheriffCheck' => ($sheriffCheck === 'X') ? 0 : $sheriffCheck,
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }


    public function streamSettingsForm(Request $request, Game $game)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        if(!isset($game->props['stream'])) {
            $previousGame = \App\Models\Game::where('event_id', $game->event_id)
                ->where('table', $game->table)
                ->where('id', '<', $game->id)
                ->orderByDesc('id')
                ->first();

            $streamKey = $previousGame?->props['stream']['stream-key'] ?? strtoupper(Str::random(8));

            $game->update([
                'props' => array_merge($game->props, [
                    'stream' => [
                        'enabled' => false,
                        'stream-key' => $streamKey,
                    ]
                ])
            ]);
        }

        return view('games.stream-settings-form', [
            'game' => $game,
            'settings' => $game->props['stream'],
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }

    public function deleteForm(Request $request, Game $game)
    {
        $this->authorize('manage_tournament', $game->event->tournament);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('games.forms.delete-form', [
            'game' => $game,
            'layout' => $layout,
            'mode' => 'show',
//            'styles' => ['tournaments.css']
        ]);
    }

    public function streamSettingsUpdate(Request $request, Game $game)
    {

        $props = $game->props;

        $updates = [];

        if($request->has('stream_key')) {
            $props['stream']['stream-key'] = $request->input('stream_key');
            $updates['stream-key'] = $request->input('stream_key');
        }

        foreach([ 'subphase', 'name', 'voted', 'killed', 'roles', 'judge' ] as $key) {
            if($request->has('show_'.$key)) {
                $props['stream']['show-'.$key] = $request->input('show_'.$key);
                $updates['show-'.$key] = $request->input('show_'.$key);
            }
        }


        $game->update([
            'props' => $props
        ]);

        if (!empty($updates)) {
            Game::where('event_id', $game->event_id)
                ->where('table', $game->table)
                ->where('id', '!=', $game->id) // исключаем текущую игру
                ->get()
                ->each(function ($otherGame) use ($updates) {
                    $otherProps = $otherGame->props;

                    foreach ($updates as $key => $value) {
                        $otherProps['stream'][$key] = $value;
//                        if ($key === 'stream-key') {
//                            $otherProps['stream']['stream-key'] = $value;
//                        } elseif ($key === 'show-roles') {
//                            $otherProps['stream']['show-roles'] = $value;
//                        }
                    }

                    $otherGame->update(['props' => $otherProps]);
                });
        }
//        if($request->has('stream_key')) {
//            $props['stream']['stream-key'] = $request->input('stream_key');
//        }
//        if($request->has('show_roles')) {
//            $props['stream']['show-roles'] = $request->input('show_roles');
//        }

        return response()->json([
            'status' => 'ok',
            'stream' => $game->props['stream']
        ]);
    }

    public function streamStart(Request $request, Game $game)
    {
        $streamKey = $game->props['stream']['stream-key'] ?? null;

        if (!$streamKey) {
            return response()->json(['error' => 'No stream key set'], 400);
        }


        // 1. Выключаем все игры с таким же event_id и stream-key
        Game::where('event_id', $game->event_id)
            ->whereJsonContains('props->stream->stream-key', $streamKey)
            ->update([
                'props->stream->enabled' => false,
            ]);

        // 2. Включаем только текущую
        $props = $game->props;
        $props['stream']['enabled'] = 'live';
        $game->update([
            'props' => $props,
        ]);

        return response()->json(['status' => 'ok']);
    }


    public function speaker(Game $game, int $slot)
    {
        $speakers = $game->props['days']['D'.($game->props['day'] ?? '0')]['speakers'] ?? [];
        $speakers[] = $slot;
        $props = $game->props;
        $props['days']['D'.($game->props['day'] ?? '0')]['active_speaker'] = $slot;
        $props['days']['D'.($game->props['day'] ?? '0')]['speakers'] = $speakers;
        $game->update([
            'props' => $props
        ]);

        return response()->json([
            'active_speaker' => $slot,
            'speakers' => $speakers,
            'status' => 'ok'
        ]);

    }

    public function candidate(Request $request, Game $game, int $slot)
    {
        $validated = $request->validate([
            'slot' => 'required|integer',
            'candidate' => 'required|integer',
            'day' => 'required|integer',
        ]);
        $candidate = $validated['candidate'];
        $day = $validated['day'];

        //$currentProps['days']['D'.$newDay] = ['nominees' => [], 'votes' => [], 'speakers' => [], 'active_speaker' => null];

        $nominees = $game->props['days']['D'.$day]['nominees'] ?? [];
        if((int)$candidate === 0) {
            unset($nominees[$slot]);
        } else {
            $nominees[$slot] = $candidate;
        }

        $props = $game->props;
        $props['days']['D'.$day]['nominees'] = $nominees;

        $game->update([
            'props' => $props
        ]);
// dd($validated);
        return response()->json([
            'slot' => $slot,
            'candidate' => $candidate,
            'day' => $day,
            'nominees' => $nominees,
            'status' => 'ok'
        ]);

    }

    public function warn(Request $request, Game $game, int $slot)
    {
        $slotParticipant = $game->slots()->where('slot', $slot)->first();

        $warns = $slotParticipant->warns ?? 0;
        if($request->has('remove') && $request->input('remove')) {
            if($warns > 0) {
                if ($warns === 4) {
                    if ($slotParticipant->status === 'eliminated') {
                        $eliminatedList = $game->props['eliminated'] ?? [];
                        $eliminatedList = array_diff($eliminatedList, [$slot]);
                        $game->update([
                            'props' => array_merge($game->props, [
                                'eliminated' => $eliminatedList
                            ])
                        ]);
                        $slotParticipant->update([
                            'status' => 'alive'
                        ]);
                    }
                }
                $warns--;
                $slotParticipant->update([
                    'warns' => $warns
                ]);

            }
        } else {
            if($warns<4) {

                $warns++;
                $slotParticipant->update([
                    'warns' => $warns
                ]);

                if ($warns === 4) {
                    $status = $this->slotEliminate($game, $slot, 'eliminated');
                }
            }
        }

        return response()->json([
            'slot' => $slot,
            'slot-status' => $slotParticipant->status,
            'warns' => $warns,
            'status' => 'ok'
        ]);

    }


    public function eliminate(Request $request, Game $game, int $slot)
    {

        $validated = $request->validate([
            'status' => 'required|in:eliminated,alive,killed,voted',
        ]);

        $status = $this->slotEliminate($game, $slot, $validated['status']);

        return response()->json([
            'slot' => $slot,
            'status' => $status
        ]);
    }

    public function slotEliminate(Game $game, int $slot, string $status) {
        $slotParticipant = $game->slots()->where('slot', $slot)->first();

        $currentStatus = $slotParticipant->status;
        $killedList = $game->props['killedList'] ?? [];
        $votedList = $game->props['votedList'] ?? [];
        $eliminatedList = $game->props['eliminated'] ?? [];

        if(($status === 'eliminated') && in_array($slot, $eliminatedList)) {
            $eliminatedList = array_diff($eliminatedList, [$slot]);
            $game->update([
                'props' => array_merge($game->props, [
                    'eliminated' => $eliminatedList
                ])
            ]);
        }

        if(($status === 'voted') && in_array($slot, $votedList)) {
            $votedList = array_diff($votedList, [$slot]);
            $game->update([
                'props' => array_merge($game->props, [
                    'votedList' => $votedList
                ])
            ]);
        }
        if(($status === 'killed') && in_array($slot, $killedList)) {
            $killedList = array_diff($killedList, [$slot]);
            $game->update([
                'props' => array_merge($game->props, [
                    'killedList' => $killedList
                ])
            ]);
        }
        if($status === 'eliminated' && !in_array($slot, $eliminatedList)) {
            $eliminatedList[] = $slot;

            $game->update([
                'props' => array_merge($game->props, [
                    'eliminated' => $eliminatedList
                ])
            ]);
        }

        if($status === 'voted' && !in_array($slot, $votedList)) {
//            dd($votedList);
            $votedList[] = $slot;
            $game->update([
                'props' => array_merge($game->props, [
                    'votedList' => $votedList
                ])
            ]);
        }

        if($status === 'killed' && !in_array($slot, $killedList)) {

            $killedList[] = $slot;

            $game->update([
                'props' => array_merge($game->props, [
                    'killedList' => $killedList
                ])
            ]);
//            dd($status, $slot, $killedList, $game->props['killedList']);
        }

        $slotParticipant->update([
            'status' => $status
        ]);

        return $status;
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



    public function voting(Request $request, Game $game)
    {

        $votingDay = $request->input('votingDay');
        $day = (int)str_replace('D', '', $votingDay);
        $props = $game->props;

        $votingRes = $request->input('no_voting', false);
        $votedList = [];
        if ($request->has('voted_list')) {
            $votedListString = $request->input('voted_list');
            $votedList = explode(',', $votedListString);

            // Trim whitespace from each element
            $votedList = array_map('trim', $votedList);

            // Remove empty elements
            $votedList = array_filter($votedList);
        }


        $night = $request->input('night', 'off');
        if($night === 'on') {
            $props['days'][$votingDay]['voting']['result'] = [];
            $votedList = [];
        } elseif(count($votedList) > 0) {
            $props['days'][$votingDay]['voting']['result'] = $votedList;
            foreach ($votedList as $voted) {
                $status = $this->slotEliminate($game, $voted, 'voted');
            }


        } elseif($votingRes) {
            $props['days'][$votingDay]['voting']['result'] = [];
        } else {
            $victim = $request->input('voting_result');
            $votedList = [ $victim ];
            $props['days'][$votingDay]['voting']['result'][] = $votedList;
            $status = $this->slotEliminate($game, $victim, 'voted');
        }

        $props['day'] = $day+1;
        if(!isset($game->props['days']['D'.$props['day']])) {
            $props['days']['D'.$props['day']] = ['nominees' => [], 'votes' => [], 'speakers' => [], 'active_speaker' => null];
        }

        $game->update([
            'props' => $props
        ]);


        //props['days']['D'.$day]['VR'] =
//        if(($night === 'on') || (($request->input('no_voting') ?? false) == true) && !empty($votedList)) {
        if(empty($votedList)) {
            return response()->json([
                'status' => 'ok',
                'no_voting' => true,
                'reason' => $request->input('no_voting_reason'),
                'day' => $day+1,
            ]);
        } else {
            return response()->json([
                'status' => 'ok',
                'result' => true,
                'guilty' => $votedList,
                'day' => $day,
            ]);
        }

    }

    public function shooting(Request $request, Game $game)
    {
        $mafia = $request->input('mafia', []);
        $shootingDay = $request->input('shootingDay');

        $victim = 0;
        foreach ($mafia as $slot => $target) {
            if ($target > 0 && $victim === 0) {
                $victim = $target;
            } elseif ($target != $victim) {
                $victim = 0;
                break;
            }
        }
        //$day = $game->props['day'] ?? 0;
        $day = (int)str_replace('D', '', $shootingDay);

        $props = $game->props;
        if(empty($props['days'][$shootingDay]['shooting']['victim'] ?? null)) {
            $props['day'] = $day;
        }
        $props['days'][$shootingDay]['shooting']['mafia'] = $mafia;

        if($victim > 0) {
            $status = $this->slotEliminate($game, $victim, 'killed');
        } else {
            $status = 'missed';
            $victim = 'X';
            $killedList = $props['killedList'] ?? [];
            $props['killedList'] = array_splice( $killedList, (int)$day, 0, $victim);
        }
        $props['days'][$shootingDay]['shooting']['victim'] = $victim;

        $game->update([
            'props' => $props
        ]);

        return response()->json([
            'status' => 'ok',
            'target' => $victim,
            'result' => $status,
            'day' => $day
        ]);
    }

    public function donCheck(Request $request, Game $game)
    {
        $donCheck = $request->input('donCheck');
        if($donCheck > 0) {
            $role = $game->slots()->where('slot', $donCheck)->first()->role;
        } else {
            $donCheck = 'X';
            $role = 'citizen';
        }
        $result = ($role === 'sheriff') ? 'yes' : 'no';
        $donCheckDay = $request->input('donCheckDay');
        $day = (int)str_replace('D', '', $donCheckDay);
        $props = $game->props;
        if($day !== $game->props['day']) {
            $props['day'] = $day;
        }


        $props['days'][$donCheckDay]['don-check'] = [
            'target' => $donCheck,
            'sheriff' => $result
        ];

        $game->update([
            'props' => $props
        ]);

        return response()->json([
            'status' => 'ok',
            'target' => $donCheck,
            'result' => $result,
            'day' => $day
        ]);
    }

    public function protocolColor(Request $request, Game $game)
    {

        $slot = $request->input('slot');
        $color = $request->input('color');

        $protocolColorDay = $request->input('protocolColorDay');
        $day = (int)str_replace('D', '', $protocolColorDay);

        $props = $game->props;

        $props['days']['D'.$day]['protocol-color'] = [
            'slot' => $slot,
            'color' => $color
        ];

        if($day !== $game->props['day']) {
            $props['day'] = $day;
        }

        $game->update([
            'props' => $props
        ]);

        return response()->json([
            'status' => 'ok',
            'slot' => $slot,
            'color' => $color,
            'day' => $day
        ]);
    }

    public function bestGuess(Request $request, Game $game)
    {
        $bestGuess = $request->input('bestGuess', []);
        $score = 0;
        foreach ($bestGuess as $key => $slot) {
            $role = $game->slots()->where('slot', $slot)->first()->role;
            $k = in_array($role, ['don', 'mafia']) ? 1 : 0;
            $score += $k;
            $bestGuess[$key] = ['slot' => $slot, 'role' => $role, 'k' => $k];
        }

        $day = $game->props['day'] ?? 1;

        $props = $game->props;


        $props['days']['D'.$day]['best-guess'] = $bestGuess;
        $victim = $props['days']['D'.$day]['shooting']['victim'] ?? 0;
        $props['first-kill'] = $victim;

        $game->update([
            'props' => $props
        ]);

        if ($score >= 2) {
//            $victim = $props['days']['D'.$day]['shooting']['victim'] ?? 0;
//            dd($day, $score, $victim);
            if($victim > 0) {
                DB::transaction(function () use ($game, $victim, $score) {
                    // Обнуляем score_4 у всех слотов игры
                    $game->slots()->update(['score_4' => 0]);

                    // Устанавливаем score_4 для жертвы
                    $game->slots()
                        ->where('slot', $victim)
                        ->update([
                            'score_4' => ($score == 2) ? 0.3 : 0.5
                        ]);
                });
            }
        }

        return response()->json([
            'status' => 'ok',
            'result' => $bestGuess
        ]);
    }

    public function sheriffCheck(Request $request, Game $game)
    {
        $sheriffCheck = $request->input('sheriffCheck');
        if($sheriffCheck > 0) {
            $role = $game->slots()->where('slot', $sheriffCheck)->first()->role;
        } else {
            $sheriffCheck = 'X';
            $role = 'citizen';
        }
        $result = (in_array($role, ['mafia', 'don'])) ? 'yes' : 'no';

        $sheriffCheckDay = $request->input('sheriffCheckDay');
        $day = (int)str_replace('D', '', $sheriffCheckDay);
        $props = $game->props;
        if($day !== $game->props['day']) {
            $props['day'] = $day;
        }

        $props['days'][$sheriffCheckDay]['sheriff-check'] = [
            'target' => $sheriffCheck,
            'mafia' => $result
        ];

        $game->update([
            'props' => $props
        ]);
        $nextPhase = 'DAY-SPEECH';
        if($day === 1) {
            $victim = $props['days']['D'.$day]['shooting']['victim'] ?? 0;
            if($victim > 0) {
//                $nextPhase = 'FIRST-KILL';
                $nextPhase = 'BEST-GUESS';

            }
        } else {
            $victim = $props['days']['D'.$day]['shooting']['victim'] ?? 0;
            if($victim > 0) {
                $nextPhase = 'LAST-SPEECH-KILLED';
            }
        }
//        dd($game->props);

        return response()->json([
            'status' => 'ok',
            'target' => $sheriffCheck,
            'result' => $result,
            'nextPhase' => $nextPhase,
            'day' => $day
        ]);
    }
    public function restore(Request $request, Game $game, int $slot)
    {
        $slotParticipant = $game->slots()->where('slot', $slot)->first();


        if($slotParticipant->status === 'eliminated') {
            $eliminatedList = $game->props['eliminated'] ?? [];
            $eliminatedList = array_diff($eliminatedList, [$slot]);
            $game->update([
                'props' => array_merge($game->props, [
                    'eliminated' => $eliminatedList
                ])
            ]);
        } elseif ($slotParticipant->status === 'killed') {
            $killedList = $game->props['killedList'] ?? [];
            $killedList = array_diff($killedList, [$slot]);
            $game->update([
                'props' => array_merge($game->props, [
                    'killedList' => $killedList
                ])
            ]);
        } elseif($slotParticipant->status === 'voted') {
            $votedList = $game->props['votedList'] ?? [];
            $votedList = array_diff($votedList, [$slot]);
            $game->update([
                'props' => array_merge($game->props, [
                    'votedList' => $votedList
                ])
            ]);
        }

        $slotParticipant->update([
            'status' => 'alive'
        ]);

        return response()->json([
            'slot' => $slot,
            'status' => 'alive'
        ]);
    }

    public function delete(Game $game)
    {
        $id = $game->id;
        $tournament = $game->event->tournament;
        if (empty($tournament)) {
            return response()->json([
                'gameID' => $id,
                'status' => 'failed'
            ]);
        } else {
            $game->delete();

            return redirect()->route('tournaments.show', [
                'tournament' => $tournament,
                'tab' => 'games'
            ])
                ->with('tab', 'games')
                ->with('success', 'Game deleted successfully.');
        }

    }
}
