<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['name', 'code'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    static public function getCountrySelector() {
        $countries = Country::all()->pluck('name', 'id');
        return $countries;
    }
}
