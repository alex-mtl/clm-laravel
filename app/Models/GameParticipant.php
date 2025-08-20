<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GameParticipant extends Pivot
{
    use HasFactory;

    protected $table = 'game_participants';

    protected $fillable = [
        'game_id',
        'user_id',
        'role',
        'slot',
        'status'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
