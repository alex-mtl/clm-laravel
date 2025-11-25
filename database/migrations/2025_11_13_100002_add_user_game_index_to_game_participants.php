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
            // Композитный индекс для оптимизации JOIN условия
            // ON game_participants.user_id = tournament_participants.user_id
            // AND game_participants.game_id = games.id
            $table->index(['user_id', 'game_id'], 'idx_user_game');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropIndex('idx_user_game');
        });
    }
};
