<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class RequestType extends Model
{
    protected $fillable = ['slug', 'name', 'config'];
    protected $casts = ['config' => 'array'];

    // Predefined types with config
    const TYPES = [
        'friend' => [
            'name' => 'Friend Request',
            'target_model' => User::class
        ],
        'event_join' => [
            'name' => 'Event Participation',
            'target_model' => Event::class
        ],
        'tournament_join' => [
            'name' => 'Tournament Participation',
            'target_model' => Tournament::class
        ]
    ];

    public static function boot()
    {
        parent::boot();
        static::created(function ($type) {
            // Clear cached types
            Cache::forget('request_types');
        });
    }

    public static function getAvailableTypes()
    {
        return collect(self::TYPES)->mapWithKeys(function ($config, $slug) {
            return [$slug => $config['name']];
        });
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'type_id');
    }
}
