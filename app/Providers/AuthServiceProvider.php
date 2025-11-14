<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\ClubRequest;
use App\Policies\ClubRequestPolicy;
use App\Models\Club;
use App\Policies\ClubPolicy;
use App\Models\Role;
use App\Policies\RolePolicy;
use App\Models\User;
use App\Models\Tournament;
use App\Policies\TournamentPolicy;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ClubRequest::class => ClubRequestPolicy::class,
        Club::class => ClubPolicy::class,
        Role::class => RolePolicy::class,
        Tournament::class => TournamentPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
        Gate::define('manage_tournament', function (User $user, Tournament $tournament) {
            // Для завершённых турниров только admin и super_admin
            if ($tournament->phase === 'finished') {
                return $user->isAdmin(); // isAdmin() проверяет admin ИЛИ super_admin
            }

            // Для остальных фаз - обычная проверка
            return $user->isTournamentOrganizer($tournament->club->id);
        });

        Gate::define('host_game', function (User $user, Tournament $tournament) {
            return $user->isTournamentJudge($tournament);
        });

//        Gate::define('manage_club', function (User $user, Club $club) {
//            dd('test manage_club');
//            return $user->isClubAdmin($club->id);
//        });



        // Optional: Define additional gates here if needed
    }
}
