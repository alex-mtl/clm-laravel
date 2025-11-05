<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tournament extends Model
{
    use HasFactory;
    const PHASES = [
        'draft' => 'Черновик',
        'registration' => 'Регистрация',
        'qualifying' => 'Квалификация',
        'finals' => 'Финалы',
        'closed' => 'Закрыт',
        'cancelled' => 'Отменен',
        'finished' => 'Завершен',
        'in_progress' => 'В процессе',
        'draft' => 'Черновик',

    ];

    protected $fillable = [
        'club_id',
        'name',
        'date_start',
        'date_end',
        'location',
        'logo',
        'banner',
        'stream_banner',
        'quota',
        'description',
        'duration',
        'players_quota',
        'games_quota',
        'prize',
        'participation_fee',
        'phase'
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'prize' => 'decimal:2',
        'participation_fee' => 'decimal:2'
    ];

    public function getDateStartDisplayAttribute()
    {
        return $this->date_start->format('Y-m-d'); // or ->toDateString()
    }

    public function getDateEndDisplayAttribute()
    {
        return $this->date_end->format('Y-m-d'); // or ->toDateString()
    }

    public function getDurationAttribute()
    {
        return $this->date_start->diffInDays($this->date_end) + 1;
    }
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'tournament_participants');
    }

    // В Tournament.php
    public function forbiddenCouples()
    {
        return $this->hasMany(TournamentCouple::class);
    }

// Метод для проверки запрещенной пары
    public function isForbiddenCouple($user1Id, $user2Id)
    {
        return $this->forbiddenCouples()
            ->where(function ($query) use ($user1Id, $user2Id) {
                $query->where('user1_id', $user1Id)->where('user2_id', $user2Id)
                    ->orWhere('user1_id', $user2Id)->where('user2_id', $user1Id);
            })
            ->exists();
    }

// Метод для добавления запрещенной пары
    public function addForbiddenCouple($user1Id, $user2Id, $reason = null)
    {
        return $this->forbiddenCouples()->create([
            'user1_id' => min($user1Id, $user2Id), // Всегда храним меньший ID первым
            'user2_id' => max($user1Id, $user2Id),
            'reason' => $reason
        ]);
    }

    public function judges()
    {
        return $this->belongsToMany(User::class, 'tournament_judges')
            ->withPivot('type');
    }

    public function joinRequests()
    {
        return $this->morphMany(Request::class, 'target')
            ->whereHas('type', fn($q) => $q->where('slug', 'tournament_join'));
    }

    public function getTournamentInfo()
    {
        $tournamentInfo = collect([
            [
                'label' => 'Клуб',
                'value' => $this->club->name,
            ],
            [
                'label' => 'Локация',
                'value' => $this->location,
            ],
            [
                'label' => 'Дата',
                'value' => $this->date_start_display,
            ],
            [
                'label' => 'Длительность',
                'value' => $this->duration ?? 1,
            ],
            [
                'label' => 'Квота игроков',
                'value' => $this->players_quota,
            ],
            [
                'label' => 'Количество игр',
                'value' => $this->games_quota,
            ],
            [
                'label' => 'Взнос за участие',
                'value' => $this->participation_fee,
            ],
            [
                'label' => 'Текущая фаза',
                'value' => $this->phase,
            ],

        ])->map(function($item) { // Changed from fn() to full function syntax
            return (object)$item;
        });

        return $tournamentInfo;
    }

    public function isParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function hasJoinRequest($userId)
    {
        return $this->joinRequests()->where('applicant_id', $userId)->exists();
    }

    public function getUserStatus($userId)
    {
        if ($this->isParticipant($userId)) {
            return 'participant';
        }

        if ($this->hasJoinRequest($userId)) {
            return $this->joinRequests()->where('applicant_id', $userId)->get()->first()->status;
        }

        return 'not_joined';
    }

    public function getResultsTable() {

        $stats = TournamentParticipant::select([
            'tournament_participants.user_id',
            'users.name as name',
            'users.avatar as avatar',
            DB::raw('COUNT(DISTINCT game_participants.game_id) as games_played'),
            DB::raw('COUNT(DISTINCT CASE WHEN game_participants.role = "citizen" THEN game_participants.game_id END) as citizen_games'),
            DB::raw('COUNT(DISTINCT CASE WHEN game_participants.role = "mafia" THEN game_participants.game_id END) as mafia_games'),
            DB::raw('COUNT(DISTINCT CASE WHEN game_participants.role = "don" THEN game_participants.game_id END) as don_games'),
            DB::raw('COUNT(DISTINCT CASE WHEN game_participants.role = "sheriff" THEN game_participants.game_id END) as sheriff_games'),
            DB::raw('SUM(CASE WHEN game_participants.score_base = 1 THEN 1 ELSE 0 END) as wins'),
            DB::raw('SUM(CASE WHEN game_participants.score_base = 1 AND game_participants.role = "citizen" THEN 1 ELSE 0 END) as citizen_wins'),
            DB::raw('SUM(CASE WHEN game_participants.score_base = 1 AND game_participants.role = "mafia" THEN 1 ELSE 0 END) as mafia_wins'),
            DB::raw('SUM(CASE WHEN game_participants.score_base = 1 AND game_participants.role = "don" THEN 1 ELSE 0 END) as don_wins'),
            DB::raw('SUM(CASE WHEN game_participants.score_base = 1 AND game_participants.role = "sheriff" THEN 1 ELSE 0 END) as sheriff_wins'),
//            DB::raw('SUM(CASE WHEN (game_participants.score_base = 1 and game_participants.role = "don") THEN 1 ELSE 0 END) as don_wins'),
//            DB::raw('SUM(CASE WHEN (game_participants.score_base = 1 and game_participants.role = "sheriff") THEN 1 ELSE 0 END) as sheriff_wins'),
            DB::raw('SUM(game_participants.score_1) as score_1'),
            DB::raw('SUM(game_participants.score_2) as score_2'),
            DB::raw('SUM(game_participants.score_3) as score_3'),
            DB::raw('SUM(game_participants.score_4) as score_4'),
            DB::raw('SUM(game_participants.score_5) as score_5'),
            DB::raw('SUM(game_participants.score_total) as score_total'),
            DB::raw('SUM(
                CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(games.props, \'$."first-kill"\')) = game_participants.slot
                THEN 1 ELSE 0
                END
            ) as first_kills'),
            DB::raw('SUM(
                CASE WHEN (JSON_UNQUOTE(JSON_EXTRACT(games.props, \'$."first-kill"\')) = game_participants.slot AND
                game_participants.score_base < 1 )
                THEN 1 ELSE 0
                END
            ) as ci_apply')
        ])
            ->join('users', 'tournament_participants.user_id', '=', 'users.id')
            ->leftJoin('events', 'events.tournament_id', '=', 'tournament_participants.tournament_id')
            ->leftJoin('games', 'games.event_id', '=', 'events.id')
            ->leftJoin('game_participants', function($join) {
                $join->on('game_participants.game_id', '=', 'games.id')
                    ->on('game_participants.user_id', '=', 'tournament_participants.user_id');
            })
            ->where('tournament_participants.tournament_id', $this->id)
            ->groupBy('tournament_participants.user_id', 'users.name')
            ->orderByDesc('score_total')
            ->get();

        $stats->each(function ($participant) {
            if (($participant->citizen_games + $participant->sheriff_games)>0) {
                $ci = $participant->first_kills / ($participant->citizen_games + $participant->sheriff_games);
                $ci = round($ci * $participant->ci_apply, 2);
                $ci = ($ci<=0.4) ? $ci : 0.4;
            } else {
                $ci = 0;
            }

            $participant->ci = $ci;
            $participant->score_total = $participant->score_total + $ci;

        });

        return $stats;
    }

}
