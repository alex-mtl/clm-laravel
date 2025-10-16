<?php
// app/Policies/ClubPolicy.php
namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function before(User $user, $ability)
    {
        // Супер-админ имеет все права
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    public function __call($ability, $arguments)
    {
        // Универсальная проверка через permissions
        $user = $arguments[0];
        $club = $arguments[1] ?? null;

        // Проверяем есть ли у пользователя нужный permission
        return $user->hasPermission($ability, $club?->id);
    }

    public function manage_club(User $user, Club $club) {
        return $user->isClubOwner($club->id) || $user->isAdmin();
    }
    public function create_tournament(User $user, Club $club) {
        return $user->isTournamentOrganizer($club->id);
    }

}
