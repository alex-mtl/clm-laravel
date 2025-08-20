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
        Schema::create('game_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role', 16)->nullable();
            $table->integer('slot')->nullable();
            $table->string('status', 16)->nullable();
            $table->timestamps();

            $table->unique(['game_id', 'user_id']);
        });

        // Add foreign key constraint in a separate statement
        Schema::table('game_participants', function (Blueprint $table) {
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_participants_tabl');
    }
};
