<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Создаём STORED generated column из JSON поля
        // Это устраняет необходимость парсить JSON в каждом запросе
        DB::statement("
            ALTER TABLE games
            ADD COLUMN first_kill_slot VARCHAR(10)
            AS (JSON_UNQUOTE(JSON_EXTRACT(props, '$.\"first-kill\"'))) STORED
        ");

        // Добавляем индекс на новое поле
        Schema::table('games', function ($table) {
            $table->index('first_kill_slot', 'idx_first_kill_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function ($table) {
            $table->dropIndex('idx_first_kill_slot');
        });

        DB::statement("ALTER TABLE games DROP COLUMN first_kill_slot");
    }
};
