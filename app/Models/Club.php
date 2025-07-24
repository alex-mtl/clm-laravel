<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Club extends Model
{
    /** @use HasFactory<\Database\Factories\ClubFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'owner_id'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }


}
