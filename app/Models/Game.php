<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Game extends Model
{
    use HasFactory;

    const roles = [
        'mafia' => 'Мафия',
        'sheriff' => 'Шериф',
        'citizen' => 'Гражданин',
        'don' => 'Дон',
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
    ];

    protected $casts = [
        'date' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
        'props' => 'json',
        'protocol' => 'json',
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

    public function getBackPhase()
    {
        $phase = 'shuffle-slots';
        $title = 'Рассадка';
        $day = 0;

        if ($this->props['phase'] === 'night' && ($this->props['day'] ?? 0) === 0) {
            $phase = 'shuffle-roles';
            $title = 'Раздача ролей';
            $day = 0;
        }
        return [
            $phase,
            $day,
            $title,
        ];
    }

}
