<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentJudges extends Model
{
    use HasFactory;

    const types = [
        'judge' => 'Судья',
        'principal_judge' => 'Главный судья',
        'side_judge' => 'Боковой судья'
    ];

    protected $fillable = [
        'tournament_id',
        'user_id',
        'type'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
