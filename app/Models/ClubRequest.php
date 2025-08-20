<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    protected $fillable = ['user_id', 'club_id', 'status', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
