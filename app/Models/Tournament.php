<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;
    const phases = [
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


}
