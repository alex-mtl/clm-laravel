<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Collection;

class TournamentScheduler
{
    private $tournament;
    private $players;
    private $tables;
    private $rounds;
    private $playersPerTable;
    private $forbiddenPairs = [];

    public function __construct(Tournament $tournament, $tables = 3, $rounds = 10)
    {
        $this->tournament = $tournament;
        $this->players = $tournament->participants->pluck('id')->toArray();
        $this->tables = $tables;
        $this->rounds = $rounds;
        $this->playersPerTable = 10;
    }

    public function addForbiddenPair($player1, $player2)
    {
        $this->forbiddenPairs[] = [$player1, $player2];
        return $this;
    }

    public function generateSchedule()
    {
        $schedule = [];

        if(count($this->players) < ($this->tables * $this->playersPerTable)) {
            return [];
        } else {
            for ($round = 1; $round <= $this->rounds; $round++) {
                $schedule[$round] = $this->generateRound($round);
            }
        }

        return $this->mapPlayerIdsToUserData($schedule);
    }

    private function mapPlayerIdsToUserData($schedule)
    {
        // Получаем данные пользователей

        $userData = User::whereIn('id', $this->players)
            ->get(['id', 'name', 'avatar'])
            ->keyBy('id')
            ->toArray();

        // Обходим все раунды и таблицы
        foreach ($schedule as $round => $tables) {
            foreach ($tables as $table => $playerIds) {
                $schedule[$round][$table] = $playerIds->map(function ($playerId) use ($userData) {
                    return $userData[$playerId] ?? ['id' => $playerId, 'name' => 'Unknown', 'avatar' => null];
                });
            }
        }

        return $schedule;
    }
    private function generateRound($round)
    {
        $roundSchedule = [];
        $availablePlayers = collect($this->players);

        for ($table = 1; $table <= $this->tables; $table++) {
            $tablePlayers = $this->selectTablePlayers($availablePlayers, $round, $table);
            $roundSchedule[$table] = $tablePlayers;

            // Удаляем выбранных игроков из доступных для этого раунда
            $availablePlayers = $availablePlayers->diff($tablePlayers);
        }

        return $roundSchedule;
    }

    private function selectTablePlayers(Collection $availablePlayers, $round, $table)
    {
        $selectedPlayers = collect();
        $attempts = 0;
        $maxAttempts = 100;

        while ($selectedPlayers->count() < $this->playersPerTable && $attempts < $maxAttempts) {
            $candidate = $availablePlayers->random();

            if ($this->isValidSelection($selectedPlayers, $candidate, $round, $table)) {
                $selectedPlayers->push($candidate);
                $availablePlayers = $availablePlayers->reject(function ($player) use ($candidate) {
                    return $player == $candidate;
                });
            }

            $attempts++;
        }

        // Если не удалось набрать игроков, добавляем случайных
        if ($selectedPlayers->count() < $this->playersPerTable) {
            $needed = $this->playersPerTable - $selectedPlayers->count();
            $additionalPlayers = $availablePlayers->shuffle()->take($needed);
            $selectedPlayers = $selectedPlayers->merge($additionalPlayers);
        }

        return $selectedPlayers->sort()->values();
    }

    private function isValidSelection(Collection $selectedPlayers, $candidate, $round, $table)
    {
        // Проверка запрещенных пар
        foreach ($selectedPlayers as $player) {
            if ($this->isForbiddenPair($player, $candidate)) {
                return false;
            }
        }

        // Проверка предыдущих встреч (чтобы избежать частых повторений)
        $previousMeetings = $this->countPreviousMeetings($selectedPlayers, $candidate, $round);
        if ($previousMeetings > 3) { // Максимум 2 предыдущие встречи
            return false;
        }

        return true;
    }

    private function isForbiddenPair($player1, $player2)
    {
        foreach ($this->forbiddenPairs as $pair) {
            if (($pair[0] == $player1 && $pair[1] == $player2) ||
                ($pair[0] == $player2 && $pair[1] == $player1)) {
                return true;
            }
        }
        return false;
    }

    private function countPreviousMeetings(Collection $selectedPlayers, $candidate, $currentRound)
    {
        // Здесь можно добавить логику подсчета предыдущих встреч
        // Пока возвращаем 0 для простоты
        return 0;
    }

    public function getScheduleStats($schedule)
    {
        $stats = [];

        foreach ($this->players as $player) {
            $opponents = [];

            foreach ($schedule as $round => $tables) {
                foreach ($tables as $tablePlayers) {
                    if (in_array($player, $tablePlayers->pluck('id')->toArray())) {
                        $opponents = array_merge($opponents, $tablePlayers->reject(function ($p) use ($player) {
                            return $p['id'] == $player;
                        })->pluck('id')->toArray());
                    }
                }
            }

            $opponentCounts = array_count_values($opponents);
            $stats[$player] = [
                'games_played' => count(array_filter($schedule, function ($tables) use ($player) {
                    return collect($tables)->flatten(1)->contains(function ($playerData) use ($player) {
                        return $playerData['id'] == $player;
                    });
                })),
                'unique_opponents' => count($opponentCounts),
                'opponent_distribution' => $opponentCounts
            ];
        }

        return $stats;
    }
}
