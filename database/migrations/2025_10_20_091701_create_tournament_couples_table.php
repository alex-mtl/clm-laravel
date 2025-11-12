<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tournament_couples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained();
            $table->foreignId('user1_id')->constrained('users');
            $table->foreignId('user2_id')->constrained('users');
            $table->text('reason')->nullable(); // Причина запрета (опционально)
            $table->timestamps();

            // Уникальность: одна пара в турнире
            $table->unique(['tournament_id', 'user1_id', 'user2_id']);

            // Индексы для быстрого поиска
            $table->index(['tournament_id', 'user1_id']);
            $table->index(['tournament_id', 'user2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_couples');
    }
};
