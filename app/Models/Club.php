<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Club extends Model
{
    /** @use HasFactory<\Database\Factories\ClubFactory> */
    use HasFactory, Notifiable;

    const IMG_TYPES = [
        'avatar' => [ 'dir' => 'club/avatar/','width' => 300, 'height' => 300 ],
        'logo' => [ 'dir' => 'club/logo/', 'width' => 500, 'height' => 500 ],
        'banner' => [ 'dir' => 'club/banner/', 'width' => 1500, 'height' => 500 ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'logo',
        'avatar',
        'banner',
        'owner_id',
        'country_id',
        'city_id',
        'description',
        'phone_number',
        'website',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }


    static public function saveImg($request, $type, $club = null)
    {
        // Generate random filename with .png extension
        $filename = Str::random(8).'.png';

        $imgType = self::IMG_TYPES[$type];

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file($type))
            ->resize($imgType['width'], $imgType['height'])
            ->toPng();

        // Save to storage with full path
        Storage::disk('public')->put("/{$imgType['dir']}{$filename}", $image);
        if ($club && $club->avatar) {
            Storage::disk('public')->delete($club->avatar);
        }

        return "{$imgType['dir']}{$filename}";
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'club_members')
            ->using(ClubMember::class)
            ->withTimestamps();
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(ClubRequest::class);
    }

    public function pendingJoinRequests()
    {
        return $this->joinRequests()->where('status', 'pending');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }

    static public function getClubSelector() {
        $clubs = Club::all()->pluck('name', 'id')
            ->prepend('<empty>', 'null');
        return $clubs;
    }


}
