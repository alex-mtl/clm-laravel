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
            // Индекс для быстрой фильтрации по ролям (citizen, mafia, don, sheriff)
            $table->index('role', 'idx_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropIndex('idx_role');
        });
    }
};
