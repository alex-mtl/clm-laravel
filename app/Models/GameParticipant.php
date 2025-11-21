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
        'status',
        'win',
        'score_base',
        'score_1',
        'score_2',
        'score_3',
        'score_4',
        'score_5',
        'score_total',
        'score_count_flag',
        'mark',
        'mark_number',
        'warns'
    ];

    protected $casts = [
        'win' => 'integer',
        'score_base' => 'decimal:0',
        'score_1' => 'decimal:2',
        'score_2' => 'decimal:2',
        'score_3' => 'decimal:2',
        'score_4' => 'decimal:2',
        'score_5' => 'decimal:2',
        'mark_number' => 'decimal:0',
        'score_total' => 'decimal:2',
        'score_count_flag' => 'string'
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
