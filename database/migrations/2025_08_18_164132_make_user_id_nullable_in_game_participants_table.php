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
        Schema::table('game_participants', function (Blueprint $table) {
            // Drop existing foreign key constraint first
            $table->dropForeign(['user_id']);

            // Change column to nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Re-add foreign key constraint (will still allow nulls)
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_participants', function (Blueprint $table) {
            //
        });
    }
};
