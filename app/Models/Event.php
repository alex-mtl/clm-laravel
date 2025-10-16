<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $attributes = [
        'tables' => 1
    ];

    protected $fillable = [
        'club_id',
        'tournament_id',
        'name',
        'date',
        'description',
        'logo',
        'games_quota',
        'date_start',
        'date_end',
        'tables'
    ];

    protected $casts = [
        'date_start' => 'date:Y-m-d',
        'date_end' => 'date:Y-m-d',
    ];

    public function getDateStartDisplayAttribute()
    {
        return $this->date_start?->format('Y-m-d'); // or ->toDateString()
    }

    public function getDateEndDisplayAttribute()
    {
        return $this->date_end?->format('Y-m-d'); // or ->toDateString()
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }


}
