<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TournamentScoreService
{
    private $logger;

    public function __construct()
    {
        $this->logger = Log::channel('count_score');
    }

    /**
     * Подсчет и обновление очков игроков для турнира с применением градации по местам
     */
    public function calculateScoresForTournament(Tournament $tournament): array
    {
        $this->logger->info("=== Начало подсчета очков для турнира ID: {$tournament->id}, название: {$tournament->name} ===");

        // Проверяем завершенность всех игр турнира
        $completionStatus = $this->checkTournamentCompletion($tournament);
        if (!$completionStatus['all_finished']) {
            $errorMessage = "Для подсчета очков игроков за турнир, нужно чтобы все игры турнира завершились. Турнир еще не завершен. Завершено игр: {$completionStatus['finished_count']} из {$completionStatus['total_count']}";
            $this->logger->warning($errorMessage);
            return [
                'processed' => 0,
                'success' => 0,
                'errors' => 1,
                'error_details' => [$errorMessage],
                'incomplete_tournament' => true
            ];
        }

        // Определяем сетку турнира на основе players_quota
        $grid = $this->determineGrid($tournament->players_quota);
        $this->logger->info("Квота игроков: {$tournament->players_quota}, выбрана сетка: '{$grid}'");

        // Получаем всех игроков с их суммарными очками (только с score_count_flag='0')
        $players = $this->getTournamentPlayersWithScores($tournament);
        $this->logger->info("Найдено уникальных игроков: " . $players->count());

        if ($players->isEmpty()) {
            $this->logger->warning("Нет игроков для обработки или все записи уже обработаны");
            return [
                'processed' => 0,
                'success' => 0,
                'errors' => 0,
                'error_details' => []
            ];
        }

        // Сортируем игроков по убыванию суммарных очков (больше очков = выше место)
        // При равных очках сортируем по user_id для стабильности
        $sortedPlayers = $players->sortByDesc(function ($player) {
            return [$player->total_score, -$player->user_id];
        })->values();

        $results = [
            'processed' => 0,
            'success' => 0,
            'errors' => 0,
            'error_details' => []
        ];

        // Присваиваем места и начисляем очки
        foreach ($sortedPlayers as $index => $player) {
            $place = $index + 1; // Место в турнире (1-е, 2-е, 3-е...)
            $results['processed']++;

            try {
                $this->processPlayerScore($player, $tournament, $place, $grid);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors']++;
                $errorMsg = "User ID: {$player->user_id}, Place: {$place} - " . $e->getMessage();
                $results['error_details'][] = $errorMsg;
                $this->logger->error("FAILED - {$errorMsg}");
            }
        }

        $this->logger->info("=== Завершение подсчета очков ===");
        $this->logger->info("Обработано: {$results['processed']}, Успешно: {$results['success']}, Ошибок: {$results['errors']}");

        return $results;
    }

    /**
     * Определение сетки турнира на основе квоты игроков
     * Округление к ближайшей сетке: 1-15 → '10', 16-25 → '20', 26-35 → '30', 36+ → '40'
     */
    private function determineGrid(int $playersQuota): string
    {
        if ($playersQuota <= 15) {
            return '10';
        } elseif ($playersQuota <= 25) {
            return '20';
        } elseif ($playersQuota <= 35) {
            return '30';
        } else {
            return '40';
        }
    }

    /**
     * Проверка завершенности всех игр турнира
     */
    private function checkTournamentCompletion(Tournament $tournament): array
    {
        $totalGames = DB::table('games')
            ->join('events', 'games.event_id', '=', 'events.id')
            ->where('events.tournament_id', $tournament->id)
            ->count();

        $finishedGames = DB::table('games')
            ->join('events', 'games.event_id', '=', 'events.id')
            ->where('events.tournament_id', $tournament->id)
            ->where('games.is_finished', 1)
            ->count();

        $allFinished = ($totalGames > 0 && $totalGames === $finishedGames);

        $this->logger->info("Статистика игр турнира: всего {$totalGames}, завершено {$finishedGames}");

        return [
            'total_count' => $totalGames,
            'finished_count' => $finishedGames,
            'all_finished' => $allFinished
        ];
    }

    /**
     * Получение всех игроков турнира с суммой их очков
     * Возвращает коллекцию объектов с полями: user_id, total_score
     */
    private function getTournamentPlayersWithScores(Tournament $tournament)
    {
        return DB::table('game_participants')
            ->join('games', 'game_participants.game_id', '=', 'games.id')
            ->join('events', 'games.event_id', '=', 'events.id')
            ->where('events.tournament_id', $tournament->id)
            ->where('games.is_finished', 1)
            ->where('game_participants.score_count_flag', '0')
            ->whereNotNull('game_participants.user_id')
            ->select([
                'game_participants.user_id',
                DB::raw('SUM(game_participants.score_total) as total_score')
            ])
            ->groupBy('game_participants.user_id')
            ->get();
    }

    /**
     * Обработка очков для одного игрока в транзакции
     */
    private function processPlayerScore(object $player, Tournament $tournament, int $place, string $grid): void
    {
        DB::transaction(function () use ($player, $tournament, $place, $grid) {
            $user = User::findOrFail($player->user_id);

            // Получаем очки за место из сетки градации
            $pointsForPlace = config("tournament_points.grid.{$grid}.{$place}", 0);

            // Подсчет количества игр и турниров
            $stats = $this->calculateUserGamesAndTournaments($player->user_id, $tournament);

            // ДОБАВЛЯЕМ значения к существующим
            $user->glob_score = ($user->glob_score ?? 0) + $pointsForPlace;
            $user->glob_games = ($user->glob_games ?? 0) + $stats['games'];
            $user->glob_tournaments = ($user->glob_tournaments ?? 0) + $stats['tournaments'];
            $user->save();

            // Устанавливаем флаг для обработанных записей
            $this->markRecordsAsProcessed($player->user_id, $tournament);

            $this->logger->info(
                "SUCCESS - User ID: {$player->user_id}, " .
                "Место: {$place}, " .
                "Сумма очков в турнире: {$player->total_score}, " .
                "Очки за место (сетка {$grid}): +{$pointsForPlace}, " .
                "Games: +{$stats['games']}, " .
                "Tournaments: +{$stats['tournaments']}"
            );
        });
    }

    /**
     * Подсчет количества игр и турниров игрока (без score_total)
     */
    private function calculateUserGamesAndTournaments(int $userId, Tournament $tournament): array
    {
        $stats = DB::table('game_participants')
            ->join('games', 'game_participants.game_id', '=', 'games.id')
            ->join('events', 'games.event_id', '=', 'events.id')
            ->where('events.tournament_id', $tournament->id)
            ->where('games.is_finished', 1)
            ->where('game_participants.user_id', $userId)
            ->where('game_participants.score_count_flag', '0')
            ->select([
                DB::raw('COUNT(DISTINCT game_participants.game_id) as total_games'),
                DB::raw('COUNT(DISTINCT events.tournament_id) as total_tournaments')
            ])
            ->first();

        return [
            'games' => (int) ($stats->total_games ?? 0),
            'tournaments' => (int) ($stats->total_tournaments ?? 0),
        ];
    }

    /**
     * Установка флага обработки для записей игрока
     */
    private function markRecordsAsProcessed(int $userId, Tournament $tournament): void
    {
        DB::table('game_participants')
            ->join('games', 'game_participants.game_id', '=', 'games.id')
            ->join('events', 'games.event_id', '=', 'events.id')
            ->where('events.tournament_id', $tournament->id)
            ->where('games.is_finished', 1)
            ->where('game_participants.user_id', $userId)
            ->where('game_participants.score_count_flag', '0')
            ->update(['game_participants.score_count_flag' => '1']);
    }
}