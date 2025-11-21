<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_participants', function (Blueprint $table) {
            // Covering index содержит все поля, используемые в запросе статистики
            // Это позволяет MySQL получать все данные из индекса без обращения к основной таблице
            $table->index([
                'game_id',       // для JOIN с games
                'user_id',       // для JOIN с tournament_participants
                'role',          // для фильтров CASE WHEN role = ...
                'score_base',    // для подсчёта побед
                'slot',          // для сравнения с first_kill_slot
                'score_1',       // для суммирования очков
                'score_2',
                'score_3',
                'score_4',
                'score_5',
                'score_total'    // для ORDER BY
            ], 'idx_covering_stats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropIndex('idx_covering_stats');
        });
    }
};