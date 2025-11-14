<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    /**
     * Проверка перед всеми методами - супер-админ имеет все права
     */
    public function before(User $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Проверка прав на подсчет результатов турнира
     * Доступ: супер-админ, админ клуба, судьи турнира
     */
    public function calculate_scores(User $user, Tournament $tournament)
    {
        // isTournamentJudge уже проверяет:
        // - судья турнира (из таблицы tournament_judges)
        // - ИЛИ админ клуба (который включает супер-админа)
        return $user->isTournamentJudge($tournament);
    }
}