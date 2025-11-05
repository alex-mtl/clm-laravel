<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentCouple extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'user1_id',
        'user2_id',
        'reason'
    ];

    // Отношение к турниру
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    // Отношение к первому пользователю
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    // Отношение ко второму пользователю
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    // Проверка содержит ли пара пользователя
    public function containsUser($userId)
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    // Получить другого пользователя в паре
    public function getOtherUser($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2_id;
        } elseif ($this->user2_id == $userId) {
            return $this->user1_id;
        }
        return null;
    }
}
