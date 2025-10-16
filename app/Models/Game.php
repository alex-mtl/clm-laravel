<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Game extends Model
{
    use HasFactory;

    const ROLES = [
        'mafia' => 'Мафия',
        'sheriff' => 'Шериф',
        'citizen' => 'Мирный',
        'don' => 'Дон',
    ];

    const PHASES_ORDER = [
        'SHUFFLE-SLOTS' => [
            'prev-phase' => 'SHUFFLE-SLOTS',
            'phase' => 'shuffle-slots',
            'sub-phase' => 'none',
            'day' => 0,
            'next-phase' => 'SHUFFLE-ROLES'
        ],
        'SHUFFLE-ROLES' => [
            'prev-phase' => 'SHUFFLE-SLOTS',
            'phase' => 'shuffle-roles',
            'sub-phase' => 'none',
            'day' => 0,
            'next-phase' => 'NIGHT-CAHOOT'
        ],
        'NIGHT-CAHOOT' => [
            'prev-phase' => 'SHUFFLE-ROLES',
            'phase' => 'night',
            'sub-phase' => 'cahoot',
            'day' => 0,
            'next-phase' => 'SHERIFF-SIGN'
        ],
        'SHERIFF-SIGN' => [
            'prev-phase' => 'NIGHT-CAHOOT',
            'phase' => 'night',
            'sub-phase' => 'sheriff-sign',
            'day' => 0,
            'next-phase' => 'FREE'
        ],
        'FREE' => [
            'prev-phase' => 'SHERIFF-SIGN',
            'phase' => 'night',
            'sub-phase' => 'free',
            'day' => 0,
            'next-phase' => 'SPEECH'
        ],
        'SPEECH' => [
            'prev-phase' => 'FREE',
            'phase' => 'day',
            'sub-phase' => 'first-speaker',
            'day' => 0,
            'next-phase' => 'VOTING'
        ],
        'DAY-SPEECH' => [
            'prev-phase' => 'SHERIFF-CHECK',
            'phase' => 'day',
            'sub-phase' => 'first-speaker',
            'day' => 1,
            'next-phase' => 'VOTING'
        ],
        'VOTING' => [
            'prev-phase' => 'SPEECH',
            'phase' => 'day',
            'sub-phase' => 'voting-round',
            'day' => 0,
            'next-phase' => 'SPLIT-SPEECH'
        ],
        'SPLIT-SPEECH' => [
            'prev-phase' => 'VOTING',
            'phase' => 'day',
            'sub-phase' => 'split-speech',
            'day' => 0,
            'next-phase' => 'LAST-SPEECH-VOTED'
        ],
        'LAST-SPEECH-VOTED' => [
            'prev-phase' => 'SPLIT-SPEECH',
            'phase' => 'day',
            'sub-phase' => 'last-speech-voted',
            'day' => 0,
            'next-phase' => 'SHOOTING'
        ],
        'SHOOTING' => [
            'back' => -1,
            'prev-phase' => 'LAST-SPEECH-VOTED',
            'phase' => 'night',
            'sub-phase' => 'shooting',
            'day' => 1,
            'next-phase' => 'DON-CHECK',
        ],
        'DON-CHECK' => [
            'prev-phase' => 'SHOOTING',
            'phase' => 'night',
            'sub-phase' => 'don-check',
            'day' => 1,
            'next-phase' => 'SHERIFF-CHECK'
        ],
        'SHERIFF-CHECK' => [
            'prev-phase' => 'DON-CHECK',
            'phase' => 'night',
            'sub-phase' => 'sheriff-check',
            'day' => 1,
            'next-phase' => 'FIRST-KILL',
        ],
        'FIRST-KILL' => [
            'prev-phase' => 'SHERIFF-CHECK',
            'phase' => 'night',
            'sub-phase' => 'first-kill',
            'day' => 1,
            'next-phase' => 'BEST-GUESS',
        ],
        'BEST-GUESS' => [
            'prev-phase' => 'FIRST-KILL',
            'phase' => 'night',
            'sub-phase' => 'best-guess',
            'day' => 1,
            'next-phase' => 'LAST-SPEECH-KILLED',
        ],
        'LAST-SPEECH-KILLED' => [
            'prev-phase' => 'SHERIFF-CHECK',
            'phase' => 'day',
            'sub-phase' => 'last-speech-killed',
            'day' => 1,
            'next-phase' => 'PROTOCOL-COLOR',
        ],
        'PROTOCOL-COLOR' => [
            'prev-phase' => 'LAST-SPEECH-KILLED',
            'phase' => 'day',
            'sub-phase' => 'protocol-color',
            'day' => 1,
            'next-phase' => 'DAY-SPEECH',
        ],
        'SCORE' => [
            'prev-phase' => 'LAST-SPEECH-KILLED',
            'phase' => 'game-over',
            'sub-phase' => 'score',
            'day' => 1,
            'next-phase' => 'SCORE-SAVE',
        ],
        'SCORE-SAVE' => [
            'prev-phase' => 'SCORE',
            'phase' => 'score',
            'sub-phase' => 'save',
            'day' => 1,
            'next-phase' => 'FINISHED',
        ],
        'FINISHED' => [
            'prev-phase' => 'SCORE',
            'phase' => 'finished',
            'sub-phase' => 'none',
            'day' => 1,
            'next-phase' => 'FINISHED',
        ],

    ];
    const PHASES = [
        'game-over' => [
            'score' => ['title' => 'Результаты', 'timer' => 0]
        ],
        'shuffle-slots' => [
            'none' => ['title' => 'Рассадка', 'timer' => 60],
        ],
        'shuffle-roles' => [
            'none' => ['title' => 'Раздача ролей', 'timer' => 60],
        ],
        'score' => [
            'none' => ['title' => 'Результаты', 'timer' => 60],
        ] ,
        'finished' => [
            'none' => ['title' => 'Игра окончена', 'timer' => 60],
        ],

        'night' => [
            'cahoot' => ['title' => 'Договорка', 'timer' => 60],
            'sheriff-sign' => ['title' => 'Ночь - Шериф', 'timer' => 10],
            'free' => ['title' => 'Свободная посадка', 'timer' => 20],
            'pause' => ['title' => 'Пауза', 'timer' => 0],
            'shooting' => ['title' => 'Стрельба', 'timer' => 20],
            'don-check' => ['title' => 'Проверка Дона', 'timer' => 10],
            'sheriff-check' => ['title' => 'Проверка Шерифа', 'timer' => 10],
            'best-guess' => ['title' => 'Лучший ход', 'timer' => 10],
            'first-kill' => ['title' => 'Первый убиенный', 'timer' => 10],
        ],
        'day' => [
            'none' => ['title' => 'День', 'timer' => 60],
            'first-speaker' => ['title' => 'День', 'timer' => 60],
            'speaker' => ['title' => 'День', 'timer' => 60],
            'voting-round' => ['title' => 'Голосование', 'timer' => 60],
            'last-speech-killed' => ['title' => 'Завещание убитого игрока', 'timer' => 60],
            'last-speech-voted' => ['title' => 'Завещание заголосованного игрока', 'timer' => 60],
            'protocol-color' => ['title' => 'Цвет под протокол', 'timer' => 10],
            'split-speech' => ['title' => 'Перестрелка', 'timer' => 30],

        ]
    ];

    const slots = [
        '1' => [],
        '2' => [],
        '3' => [],
        '4' => [],
        '5' => [],
        '6' => [],
        '7' => [],
        '8' => [],
        '9' => [],
        '10' => [],
    ];

    const MARK_OPTIONS = [
            'm5' => -5,
            'm4' => -4,
            'm3' => -3,
            'm2' => -2,
            'm1' => -1,
            'zero' => 0,
            'p1' => 1,
            'p2' => 2,
            'p3' => 3,
            'p4' => 4,
            'p5' => 5
    ];


    protected $fillable = [
        'event_id',
        'name',
        'date',
        'start',
        'end',
        'description',
        'props',
        'protocol',
        'judge_id',
        'table',
        'code',
    ];

    protected $casts = [
        'date' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
        'props' => 'array',
        'protocol' => 'array',
    ];


    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'game_participants')
            ->using(GameParticipant::class)
            ->withPivot(['role', 'slot', 'status'])
            ->whereNull('user_id');
    }

    public function slots()
    {
        return $this->hasMany(GameParticipant::class, 'game_id');
    }

    public function slotsInit()
    {
        for ($i = 1; $i <= 10; $i++) {
            // Используем firstOrCreate чтобы создать только если не существует
            $this->slots()->firstOrCreate(
                ['slot' => $i],
                [
                    'slot' => $i,
                    'user_id' => null,
                    'name' => null,
                    'role' => 'citizen', // значение по умолчанию
                    'status' => 'alive', // значение по умолчанию
                    // добавьте другие поля по необходимости
                ]
            );
        }

        return $this->slots; // возвращаем обновленный список слотов
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function getJudgeTypeAttribute()
    {
        if (!$this->judge_id) return null;

        return optional(
            $this->event->tournament->judges->firstWhere('id', $this->judge_id)
        )->pivot->type ?? 'judge';
    }

    protected static function booted()
    {
        static::created(function ($game) {
            $game->update([
                'props' => array_merge($game->props ?? [], ['id' => $game->id])
            ]);
        });
    }

    public function getBackPhase($phaseCode, $day = null)
    {
        $currPhase = Game::PHASES_ORDER[$phaseCode];
        $phase = Game::PHASES_ORDER[$currPhase['prev-phase']]; // prev


        if(isset($currPhase['back'])) {
            $phase['day'] = $day + $currPhase['back'];
        } else {
            $phase['day'] = $day;
        }
        $phase['phase-code'] = $currPhase['prev-phase'];
        $phase['phase-title'] = Game::PHASES[$phase['phase']][$phase['sub-phase']]['title'] ?? 'UNKNOWN '. $phase['phase'].' '.$phase['sub-phase'];
        $phase['timer'] = Game::PHASES[$phase['phase']][$phase['sub-phase']]['timer'] ?? 0;

        return $phase;
    }

    public function getNextPhase($phaseCode)
    {
        $currPhase = Game::PHASES_ORDER[$phaseCode];
        $phase = Game::PHASES_ORDER[$currPhase['next-phase']]; // prev

        $phase['phase-code'] = $currPhase['next-phase'];
        $phase['phase-title'] = Game::PHASES[$phase['phase']][$phase['sub-phase']]['title'] ?? 'UNKNOWN '. $phase['phase'].' '.$phase['sub-phase'];
        $phase['timer'] = Game::PHASES[$phase['phase']][$phase['sub-phase']]['timer'] ?? 0;

        return $phase;
    }

    public function getPhase($phaseCode)
    {
//        $currPhase = Game::PHASES_ORDER[$phaseCode];
        $phase = Game::PHASES_ORDER[$phaseCode]; // prev

        $phase['phase-code'] = $phaseCode;
        $phase['phase-title'] = Game::PHASES[$phase['phase']][$phase['sub-phase']]['title'] ?? 'UNKNOWN '. $phase['phase'].' '.$phase['sub-phase'];
        $phase['timer'] = Game::PHASES[$phase['phase']][$phase['sub-phase']]['timer'] ?? 0;

        return $phase;
    }

    public function getToasts() {
        $toasts = [];
        if($this->props['phase-code'] == 'SHERIFF-CHECK') {
            if(($this->props['days']['D'.$this->props['day']]['shooting']['victim'] ?? 0) >0) {
                $toasts[] = [
                    'message' => 'Мафия убила игрока номер: '.$this->props['days']['D'.$this->props['day']]['shooting']['victim'],
                    'type' => 'info',
                ];
            } else {
                $toasts[] = [
                    'message' => 'Мафия промахнулась',
                    'type' => 'info',
                ];
            }
            $donCheck = $this->props['days']['D'.$this->props['day']]['don-check']['target'] ?? 0;
            if($donCheck > 0) {
                if($this->props['days']['D'.$this->props['day']]['don-check']['sheriff'] === 'yes') {
                    $toasts[] = [
                        'message' => 'Дон проверил игрока номер: '.$donCheck.'. Это ШЕРИФ!',
                        'type' => 'info',
                    ];
                } else {
                    $toasts[] = [
                        'message' => 'Дон проверил игрока номер: '.$donCheck.'. Это НЕ ШЕРИФ!',
                        'type' => 'info',
                    ];
                }
            } else {
                $toasts[] = [
                    'message' => 'В эту ночь Дон никого не проверил',
                    'type' => 'info',
                ];
            }
        } elseif($this->props['phase-code'] == 'LAST-SPEECH-KILLED') {
            $sheriffCheck = $this->props['days']['D'.$this->props['day']]['sheriff-check']['target'] ?? 0;
            if($sheriffCheck > 0) {
                if($this->props['days']['D'.$this->props['day']]['sheriff-check']['mafia'] === 'yes') {
                    $toasts[] = [
                        'message' => 'Шериф проверил игрока номер: '.$sheriffCheck.'. Это МАФИЯ!',
                        'type' => 'info',
                    ];
                } else {
                    $toasts[] = [
                        'message' => 'Шериф проверил игрока номер: '.$sheriffCheck.'. Это мирный!',
                        'type' => 'info',
                    ];
                }
            } else {
                $toasts[] = [
                    'message' => 'В эту ночь Шериф никого не проверил',
                    'type' => 'info',
                ];
            }
        }
        return $toasts;
    }
}
