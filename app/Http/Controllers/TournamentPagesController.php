<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentJudges;
use App\Models\User;
use App\Models\Event;
use App\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RequestType;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TournamentPagesController extends Controller
{
    public $sidebarMenu;

    public function __construct()
    {
        $this->middleware('auth');

        $this->sidebarMenu = collect([
            [
                'icon' => 'info',
                'name' => 'Общая информация',
                'action' => 'info',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'groups',
                'name' => 'Участники',
                'action' => 'participants',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'question_exchange',
                'name' => 'Заявки',
                'action' => 'requests',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'interpreter_mode',
                'name' => 'Игры',
                'action' => 'games',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'scoreboard',
                'name' => 'Результаты',
                'action' => 'results',
                'handler' => false,
                'active' => false,
            ],

            [
                'icon' => 'balance',
                'name' => 'Судьи',
                'action' => 'judges',
                'handler' => false,
                'active' => false,
            ],
        ])->map(function($item) { // Changed from fn() to full function syntax
            return (object)$item;
        });
    }

    public function index()
    {
        $tournaments = Tournament::with('club')
            ->where('phase', '!=', 'draft')
            ->orderBy('date_start', 'desc')->paginate(10);
        return view('tournaments.index', [
            'tournaments' => $tournaments,
            'styles' => ['tournaments.css'],
            'noFooter' => true
        ]);
    }


    public function show(Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $tournamentInfo = $tournament->getTournamentInfo();

        $judgesCols = collect([
            [
                'name' => 'Судья',
                'class' => 'w-10',
                'prop' => 'name'
            ],
            [
                'name' => 'Тип',
                'class' => 'w-15',
                'prop' => 'type'
            ],

        ])->map(fn($item) => (object)$item);


        return view('tournaments.show', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'show',
            'club' => $tournament->club,
            'tournament' => $tournament,
//            'judges' => $judges,
            'judgesCols' => $judgesCols,
            'tournamentInfo' => $tournamentInfo,
            'sidebarMenu' => $this->sidebarMenu,
            'styles' => ['tournaments.css'],
            'scripts' => ['tournament-view.js']
        ]);
    }

    public function applicationForm(Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.application-form', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'show',
            'tournament' => $tournament,
            'styles' => ['tournaments.css']
        ]);
    }

    public function judgeCreate(Tournament $tournament)
    {

        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
        if ($judgeRole = Role::where('slug', 'judge')->first()) {
            $judges = $judgeRole->users->pluck('name', 'id');
        } else {
            $judges = collect();
        }
//        dd($judges);
        $judge = new TournamentJudges();
        $judgeTypes = TournamentJudges::types;

        return view('tournaments.judge-form', [
            compact('tournament'),
            'judges' => $judges,
            'judge' => $judge,
            'judgeTypes' => $judgeTypes,
            'layout' => $layout,
            'mode' => 'create',
            'tournament' => $tournament,
            'styles' => ['tournaments.css']
        ]);
    }

    public function wizardForm(Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $events = Event::where('tournament_id', $tournament->id)->get();
        if ($events->isEmpty()) {
            for($day = 1; $day <= $tournament->duration; $day++) {
                Event::create([
                     'name' => (($day == 1) ? 'Открытие' : (($day == $tournament->duration) ? 'Финал' : 'День ' . $day )),
                    'tournament_id' => $tournament->id,
                    'club_id' => $tournament->club_id,
                    'date_start' => $tournament->date_start->addDays($day - 1),
                    'date_end' => $tournament->date_start->addDays($day - 1),
                    'description' => (($day == 1) ? 'Начало в 18:00 ' : 'Начало в 10:00 '),
                    'games_quota' => 4
                ]);
            }
        }
        return view('tournaments.wizard-form', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'edit',
            'tournament' => $tournament,
            'styles' => ['tournaments.css']
        ]);
    }

    public function eventsUpdate(Request $request, Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $validated = $request->validate([
            'events.*.id' => 'required|exists:events,id',
            'events.*.name' => 'required|string|max:255',
            'events.*.date_start' => 'required|date|after_or_equal:tournament.date_start',
            'events.*.description' => 'nullable|string',
            'events.*.games_quota' => 'required|integer|min:1|max:10',
        ]);

        DB::transaction(function () use ($validated, $tournament) {
            $tablesCount = max(1, floor($tournament->players_quota / 10)); // Ensure at least 1 table
            $judges = $tournament->judges()->withPivot('type')->get();

            foreach ($validated['events'] as $eventData) {
                // 1. Update the Event record itself
                $event = Event::find($eventData['id']);
                $event->update([
                    'name' => $eventData['name'],
                    'description' => $eventData['description'],
                    'games_quota' => $eventData['games_quota'],
                ]);

                // 2. Handle Game generation
                $existingGamesCount = $event->games()->count();
                $requiredGamesCount = $eventData['games_quota'] * $tablesCount;

                if ($existingGamesCount < $requiredGamesCount) {
                    $this->createGamesForEvent(
                        event: $event,
                        gamesToCreate: $requiredGamesCount - $existingGamesCount,
                        tablesCount: $tablesCount,
                        judges: $judges
                    );
                } elseif ($existingGamesCount > $requiredGamesCount) {
                    // Optional: Handle case where we need to remove games
                    $event->games()
                        ->latest()
                        ->take($existingGamesCount - $requiredGamesCount)
                        ->delete();
                }
            }
        });

        return redirect()->route('tournaments.show', $tournament)
            ->with('tab', 'games')
            ->with('success', 'Расписание игр обновлено');

    }

    protected function createGamesForEvent(Event $event, int $gamesToCreate, int $tablesCount, Collection $judges)
    {
        $principalJudges = $judges->where('pivot.type', 'principal_judge');
        $regularJudges = $judges->where('pivot.type', '!=', 'principal_judge');

        if ($principalJudges->isEmpty() ) {
            throw new \Exception('Not enough judges assigned to tournament');
        } elseif ( ($regularJudges->count() + $principalJudges->count()) < $tablesCount) {
            throw new \Exception('Not enough judges assigned to tournament');
        }

        for ($i = 0; $i < $gamesToCreate; $i++) {
            $tableNumber = ($i % $tablesCount) + 1;

            $judge = $tableNumber === 1
                ? $principalJudges->random()
                : $regularJudges->random();

            $event->games()->create([
                'name' => 'Стол '.$tableNumber.' - Игра '.($i + 1),
                'table' => $tableNumber,
                'judge_id' => $judge->id,
                'date' => $event->date_start, // Example
                'props' => [
                    'table' => $tableNumber,
                    'event_id' => $event->id,
                    'game_number' => $i + 1,
                    'phase' => 'shuffle-slots',
                    'phase-title' => 'Рассадка',

                ],
                // other game fields
            ]);
        }
    }


    public function judgeStore(Request $request, Tournament $tournament)
    {
        $validatedData  = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:' . implode(',', array_keys(TournamentJudges::types)),
        ]);

        $validatedData['type'] = $validatedData['type'] ?? 'judge';

        $tournament->judges()->syncWithoutDetaching([
            $validatedData['user_id'] => ['type' => $validatedData['type']]
        ]);
//        $tournament->judges()->updateOrCreate(
//            ['user_id' => $validatedData['user_id']],
//            ['type' => $validatedData['type']]
//        );

        return redirect()->route('tournaments.show', $tournament)
            ->with('tab', 'judges')
            ->with('success', 'Роль успешно назначена');
    }


    public function applicationStore(Tournament $tournament)
    {
        $existing = RequestModel::where([
            'type_id' => RequestType::where('slug', 'tournament_join')->first()->id,
            'applicant_id' => auth()->user()->id,
            'target_id' => $tournament->id,
            'target_type' => Tournament::class,
        ])->exists();

        if(!$existing) {
            $request = RequestModel::create([
                'type_id' => RequestType::where('slug', 'tournament_join')->first()->id,
                'applicant_id' => auth()->user()->id,
                'target_id' => $tournament->id,
                'target_type' => Tournament::class,
                'data' => ['message' => 'Tournament join request', 'status' => 'pending']
            ]);
        }

        return redirect()->route('tournaments.show', $tournament)
            ->with('clm', 'Заявка отправлена!')
            ->with('tab', 'requests');
    }

    public function applicationApprove(Tournament $tournament, RequestModel $request)
    {
        // Verify this is a tournament join request
        $isValidRequest = $request->type->slug === 'tournament_join' &&
            $request->target_id === $tournament->id;

        if (!$isValidRequest) {
            return back()->with('error', 'Invalid request type or tournament mismatch');
        }

        // Only process if pending
        if ($request->status === 'pending') {
            DB::transaction(function () use ($request, $tournament) {
                // Update request status
                $request->update([
                    'status' => 'accepted',
                    'responder_id' => auth()->id(),
                    'responded_at' => now(),
                    'data' => array_merge($request->data ?? [], [
                        'message' => 'Tournament join request approved',
                        'approved_at' => now()->toDateTimeString()
                    ])
                ]);

                // Add user to tournament
                $tournament->participants()->syncWithoutDetaching($request->applicant_id);
            });

            return redirect()->route('tournaments.show', $tournament)
                ->with('clm', 'Заявка одобрена!')
                ->with('tab', 'requests');
        }

        return back()->with('error', 'Request already processed');
    }
}
