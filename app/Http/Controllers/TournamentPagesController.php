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
use App\Services\TournamentScheduler;
use App\Models\TournamentCouple;

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

    public function getSidebarMenu($activeTab)
    {
        $sidebarMenu = collect($this->sidebarMenu)->map(function($item) use ($activeTab) { // Changed from fn() to full function syntax
            $item = (object)$item;
            $item->active = $item->action == $activeTab;
            return $item;
        });
        return $sidebarMenu;
    }

    public function index(Request $request)
    {
        $query = Tournament::with('club');

        // Apply phase filter if specified
        if ($request->has('phase') && in_array($request->phase, array_keys(Tournament::PHASES))) {
            $query->where('phase', $request->phase);
        } else {
            // Default: exclude draft, closed, finished
            $query->whereNotIn('phase', ['draft', 'closed', 'finished']);
        }

        $tournaments = $query->orderBy('date_start', 'asc')->paginate(10);

        return view('tournaments.index', [
            'tournaments' => $tournaments,
            'styles' => ['tournaments.css'],
            'noFooter' => true,
            'phase' => $request?->phase ?? null // Pass to view for UI feedback
        ]);
    }


    public function show(Tournament $tournament)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';
//        $canManage = auth()->user()->can('manage_tournament', $tournament);

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

        if ($tab = request()->query('tab') ?? null) {
            session()->flash('tab', $tab);
        }

        $sidebarMenu = $this->getSidebarMenu(session('tab') ?? 'info');

        $stats = $tournament->getResultsTable();
        return view('tournaments.show', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'show',
            'club' => $tournament->club,
            'tournament' => $tournament,
            'results' => $stats,
//            'judges' => $judges,
            'judgesCols' => $judgesCols,
            'tournamentInfo' => $tournamentInfo,
            'sidebarMenu' => $sidebarMenu,
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
        $this->authorize('manage_tournament', $tournament);

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

    public function coupleCreate(Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);

        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $couples = $tournament->forbiddenCouples()->with(['user1:id,name', 'user2:id,name'])->get();

        return view('tournaments.forms.couples-form', [
            compact('tournament'),
            'couples' => $couples,
            'layout' => $layout,
            'mode' => 'create',
            'tournament' => $tournament,
            'styles' => ['tournaments.css']
        ]);
    }

    public function wizardForm(Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);

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
                    'tables' => floor($tournament->players_quota / 10),
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

    public function scheduleForm(Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);

        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        $tables = floor($tournament->players_quota / 10);
        $rounds = 10;
        $scheduler = new TournamentScheduler($tournament, floor($tournament->players_quota / 10), $rounds);

        // Добавляем запрещенные пары (опционально)
//        $scheduler->addForbiddenPair(1, 5)
//            ->addForbiddenPair(3, 7);

        $schedule = $scheduler->generateSchedule();
        $stats = $scheduler->getScheduleStats($schedule);



        return view('tournaments.schedule-form', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'edit',
            'tournament' => $tournament,
            'schedule' => $schedule,
            'stats' => $stats,
            'tables' => $tables,
            'rounds' => $rounds,
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
            'events.*.tables' => 'required|integer|min:1|max:10',
            'events.*.games_quota' => 'required|integer|min:1|max:20',
        ]);

        DB::transaction(function () use ($validated, $tournament) {
            $judges = $tournament->judges()->withPivot('type')->get();

            foreach ($validated['events'] as $eventData) {
                $event = Event::find($eventData['id']);

                // Update event details
                $event->update([
                    'name' => $eventData['name'],
                    'description' => $eventData['description'],
                    'games_quota' => $eventData['games_quota'],
                    'tables' => $eventData['tables'],
                ]);

                // Sync games for this event
                $this->syncGamesForEvent(
                    event: $event,
                    tablesCount: $eventData['tables'],
                    gamesCount: $eventData['games_quota'],
                    judges: $judges
                );
            }
        });

        return redirect()->route('tournaments.show', $tournament)
            ->with('tab', 'games')
            ->with('success', 'Расписание игр обновлено');
    }

    protected function syncGamesForEvent(Event $event, int $tablesCount, int $gamesCount, Collection $judges)
    {
        $principalJudges = $judges->where('pivot.type', 'principal_judge');
        $regularJudges = $judges->where('pivot.type', '!=', 'principal_judge');
        $regularJudgesArray = $regularJudges->values()->all();
//        dd($regularJudgesArray);

        // Validation
        if ($principalJudges->isEmpty()) {
            throw new \Exception('Not enough principal judges assigned to tournament');
        }

        if (($regularJudges->count() + $principalJudges->count()) < $tablesCount) {
            throw new \Exception('Not enough judges assigned to tournament');
        }

        // Get existing games organized by table and game number
        $existingGames = $event->games()
            ->get()
            ->groupBy(['table', function ($game) {
                return $game->props['game_number'] ?? 0;
            }]);

        $gamesToCreate = [];
        $gamesToUpdate = [];
        $gamesToDelete = [];

        // Determine what needs to be created, updated, or deleted
        for ($tableN = 1; $tableN <= $tablesCount; $tableN++) {
            for ($gameN = 1; $gameN <= $gamesCount; $gameN++) {
                $code = 'T' . $tableN . 'G' . $gameN;

                if ($tableN === 1) {
                    // For table 1, always use a principal judge
                    $judge = $principalJudges->first();
                } else {
                    // For other tables, assign a regular judge consistently
                    // Use the table number as a seed for consistent assignment
                    $judgeIndex = ($tableN - 2) ;
                    $judge = $regularJudgesArray[$judgeIndex];
                }

                $gameData = [
                    'name' => 'Стол ' . $tableN . ' - Игра ' . $gameN,
                    'table' => $tableN,
                    'code' => $code,
                    'judge_id' => $judge->id,
                    'date' => $event->date_start,
                    'props' => [
                        'table' => $tableN,
                        'event_id' => $event->id,
                        'game_number' => $gameN,
                        'phase-code' => 'SHUFFLE-SLOTS',
                        'phase' => 'shuffle-slots',
                        'phase-title' => 'Рассадка',
                    ],
                ];

                // Check if game already exists for this table and game number
                $existingGame = $existingGames[$tableN][$gameN] ?? null;

                if ($existingGame) {
                    // Update existing game if needed
                    $existingGame = $existingGame->first();
                    $gamesToUpdate[] = [
                        'game' => $existingGame,
                        'data' => $gameData
                    ];
                } else {
                    // Create new game
                    $gamesToCreate[] = $gameData;
                }
            }
        }

        // Identify games to delete (those that exist but shouldn't)
        foreach ($existingGames as $table => $gamesByNumber) {
            foreach ($gamesByNumber as $gameNumber => $games) {
                if ($table > $tablesCount || $gameNumber > $gamesCount) {
                    foreach ($games as $game) {
                        $gamesToDelete[] = $game->id;
                    }
                }
            }
        }

        // Execute the changes
        if (!empty($gamesToDelete)) {
            $event->games()->whereIn('id', $gamesToDelete)->delete();
        }

        foreach ($gamesToUpdate as $update) {
            $update['game']->update($update['data']);
        }

        if (!empty($gamesToCreate)) {
            $event->games()->createMany($gamesToCreate);
        }
    }

    protected function createGamesForEvent(Event $event, int $gamesToCreate, int $tablesCount, Collection $judges, int $games)
    {
        $principalJudges = $judges->where('pivot.type', 'principal_judge');
        $regularJudges = $judges->where('pivot.type', '!=', 'principal_judge');

        if ($principalJudges->isEmpty() ) {
            throw new \Exception('Not enough principaljudges assigned to tournament');
        } elseif ( ($regularJudges->count() + $principalJudges->count()) < $tablesCount) {
            throw new \Exception('Not enough judges assigned to tournament');
        }

        for ($gameN = 1; $gameN <= $event->games_quota; $gameN++) {
            for ($tableN = 1; $tableN <= $event->tables; $tableN++) {
                $code = 'T' . $tableN . 'G' . ($gameN + 1);

                $judge = $tableN === 1
                    ? $principalJudges->random()
                    : $regularJudges->random();

                $event->games()->create([
                    'name' => 'Стол ' . $tableN . ' - Игра ' . $gameN,
                    'table' => $tableN,
                    'code' => $code,
                    'judge_id' => $judge->id,
                    'date' => $event->date_start, // Example
                    'props' => [
                        'table' => $tableN,
                        'event_id' => $event->id,
                        'game_number' => $gameN,
                        'phase-code' => 'SHUFFLE-SLOTS',
                        'phase' => 'shuffle-slots',
                        'phase-title' => 'Рассадка',

                    ],
                    // other game fields
                ]);
            }
        }
    }


    public function judgeStore(Request $request, Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);

        $validatedData  = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:' . implode(',', array_keys(TournamentJudges::types)),
        ]);

        $validatedData['type'] = $validatedData['type'] ?? 'judge';

        $tournament->judges()->syncWithoutDetaching([
            $validatedData['user_id'] => ['type' => $validatedData['type']]
        ]);

        return redirect()->route('tournaments.show', $tournament)
            ->with('tab', 'judges')
            ->with('success', 'Роль успешно назначена');
    }


    public function deleteJudgeForm(Request $request, Tournament $tournament, User $judge)
    {
        $this->authorize('manage_tournament', $tournament);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.forms.delete-judge-form', [
            'tournament' => $tournament,
            'judge' => $judge,
            'layout' => $layout,
            'mode' => 'show',
//            'styles' => ['tournaments.css']
        ]);
    }

    public function judgeDelete(Request $request, Tournament $tournament, User $judge)
    {
        $this->authorize('manage_tournament', $tournament);


        $tournament->judges()->detach([
            $judge->id
        ]);

        return redirect()->route('tournaments.show', $tournament)
            ->with('tab', 'judges')
            ->with('success', 'Судья удален из турнира');
    }

    public function coupleStore(Request $request, Tournament $tournament)
    {
        $this->authorize('manage_tournament', $tournament);
        $validatedData  = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'couples' => 'required|array',
            '_token' => 'required'
        ]);

        $tournamentId = $request->tournament_id;
        $couplesPayload = $request->couples;

        DB::transaction(function () use ($tournamentId, $couplesPayload) {
            // Get all existing couple IDs for this tournament
            $existingCoupleIds = TournamentCouple::where('tournament_id', $tournamentId)
                ->pluck('id')
                ->toArray();

            $payloadCoupleIds = [];
            $validCouples = [];

            // Process each couple from payload
            foreach ($couplesPayload as $coupleKey => $coupleData) {
                // Validate couple data
                if (!isset($coupleData['user1']) || !isset($coupleData['user2'])) {
                    continue; // Skip invalid couples
                }

                $user1Id = $coupleData['user1'];
                $user2Id = $coupleData['user2'];

                // Check if users exist and are valid
                if (!User::where('id', $user1Id)->exists() ||
                    !User::where('id', $user2Id)->exists()) {
                    continue; // Skip if users don't exist
                }

                // Check for duplicate users in the same couple
                if ($user1Id == $user2Id) {
                    continue; // Skip if same user
                }

                // Find existing couple or create new one
                $couple = TournamentCouple::updateOrCreate(
                    [
                        'tournament_id' => $tournamentId,
                        'user1_id' => $user1Id,
                        'user2_id' => $user2Id
                    ],
                    [
                        'tournament_id' => $tournamentId,
                        'user1_id' => $user1Id,
                        'user2_id' => $user2Id,
                        // Add any other fields you need
                    ]
                );

                $payloadCoupleIds[] = $couple->id;
                $validCouples[] = $couple;
            }

            // Delete couples that are not in the payload
            $couplesToDelete = array_diff($existingCoupleIds, $payloadCoupleIds);

            if (!empty($couplesToDelete)) {
                TournamentCouple::where('tournament_id', $tournamentId)
                    ->whereIn('id', $couplesToDelete)
                    ->delete();
            }

            return $validCouples;
        });


        return redirect()->route('tournaments.show', $tournament)
            ->with('tab', 'participants')
            ->with('success', 'Запрет на пары успешно установлен');
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
        $this->authorize('manage_tournament', $tournament);
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

    public function applicationDecline(Tournament $tournament, RequestModel $request)
    {
        $this->authorize('manage_tournament', $tournament);
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
                    'status' => 'declined',
                    'responder_id' => auth()->id(),
                    'responded_at' => now(),
                    'data' => array_merge($request->data ?? [], [
                        'message' => 'Tournament join request declined',
                        'declined_at' => now()->toDateTimeString()
                    ])
                ]);

                // Add user to tournament
                $tournament->participants()->detach($request->applicant_id);
            });

            return redirect()->route('tournaments.show', $tournament)
                ->with('clm', 'Заявка отклонена!')
                ->with('tab', 'requests');
        }

        return back()->with('error', 'Request already processed');
    }

    public function confimationForm(Tournament $tournament, User $user)
    {
        $this->authorize('manage_tournament', $tournament);
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('tournaments.remove-confirmation-form', [
            compact('tournament'),
            'layout' => $layout,
            'mode' => 'show',
            'tournament' => $tournament,
            'user' => $user,
            'styles' => ['tournaments.css']
        ]);
    }
    public function participantRemove(Tournament $tournament, User $user)
    {
        $this->authorize('manage_tournament', $tournament);
        // Verify this is a tournament join request

        $request = RequestModel::where('type_id', function($query) {
            $query->select('id')
                ->from('request_types')
                ->where('slug', 'tournament_join');
        })
            ->where('target_id', $tournament->id)
            ->where('applicant_id', $user->id)
            ->first();


        if ($request) {
            $request->update([
                'status' => 'pending',
                'responder_id' => null,
                'responded_at' => null,
                'data' => array_merge($request->data ?? [], [
                    'message' => 'Participant removed, request reset to pending',
                    'reset_at' => now()->toDateTimeString()
                ])
            ]);
        }

        $tournament->participants()->detach($user->id);
        // Only process if pending
        return redirect()->route('tournaments.show', $tournament)
            ->with('clm', 'Участник удален!')
            ->with('tab', 'participants');

    }


}
