<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'password',
        'country_id',
        'city_id',
        'first_name',
        'last_name',
        'phone_number',
        'club_id',
        'telegram',
        'is_active',
        'email_verified_at',
        'email_verification_token',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $isSuperAdmin = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clubMemberships()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function clubJoinRequests()
    {
        return $this->hasMany(ClubRequest::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')->withPivot('club_id');
    }

    // In User.php model
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_members')
            ->using(ClubMember::class);
    }

    public function hasRole($roleSlug, $clubId = null)
    {
        return $this->roles()
            ->where('slug', $roleSlug)
            ->when($clubId, function($query) use ($clubId) {
                $query->where('club_id', $clubId);
            })
            ->exists();
    }

    public function hasGlobalRole($roleSlug)
    {
        return $this->roles()
            ->where('slug', $roleSlug)
            ->where('scope', 'global')
            ->exists();
    }

    public function hasPermission($permissionSlug, $clubId = null)
    {
        // Супер-админ имеет все права
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Проверяем permissions через роли
        return $this->roles()
            ->whereHas('permissions', function($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->when($clubId, function($query) use ($clubId) {
                // Для клубных ролей проверяем привязку к клубу
                $query->where(function($q) use ($clubId) {
                    $q->where('roles.scope', 'global')
                        ->orWhere('roles.club_id', $clubId);
                });
            })
            ->exists();
    }

    // Проверка на супер-админа
    public function isSuperAdmin()
    {
        if ($this->isSuperAdmin === null) {
            $this->isSuperAdmin = $this->hasGlobalRole('super_admin');
        }

        return $this->isSuperAdmin;
    }

    // Проверка на администратора
    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->isSuperAdmin();
    }

    public function isLeagueDirector()
    {
        return $this->hasRole('league_director') || $this->isAdmin();
    }

    // Проверка на владельца клуба
    public function isClubOwner($clubId)
    {
        return $this->hasRole('club_owner', $clubId) || $this->isLeagueDirector();
    }

    // Проверка на администратора клуба
    public function isClubAdmin($clubId)
    {
        return $this->hasRole('club_admin', $clubId) || $this->isClubOwner($clubId);
    }

    // Проверка на ведущего клуба
    public function isClubHost($clubId)
    {
        return $this->hasRole('club_host', $clubId) || $this->isClubAdmin($clubId);
    }

    // Проверка на организатора игры
    public function isGameOrganizer($clubId)
    {
        return $this->hasRole('game_organizer', $clubId) || $this->isClubHost($clubId);
    }

    // Проверка на организатора турнира
    public function isTournamentOrganizer($clubId)
    {
        return $this->hasRole('tournament_organizer', $clubId) || $this->isClubHost($clubId);
    }
    static public function saveAvatar($request, $user = null)
    {
        // Generate random filename with .png extension
        $filename = Str::random(8).'.png';

        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('avatar'))
            ->resize(500, 500)
            ->toPng();

        // Save to storage with full path
        Storage::disk('public')->put("avatars/{$filename}", $image);
        if ($user && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return "avatars/{$filename}";
    }

    public function getClubSelector() {
        $clubs = $this->clubs()->pluck('clubs.name', 'clubs.id');
        $clubs->prepend('<empty>', 0);

//        dd($clubs);
        return $clubs;
    }

    public function judgedGames(): HasMany
    {
        return $this->hasMany(Game::class, 'judge_id');
    }

    public function getNavLinks() {
        return [
            'self' => ['label' =>'Профиль', 'link' => '/users/profile'],
            'dashboard' => ['label'=>'Управление', 'link'=>'/dashboard'],
            'logout' => ['label' => 'Выйти', 'link' => '/logout'],
        ];
    }

}
