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
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('location')->after('name')->nullable();
            $table->unsignedInteger('duration')->after('date_end')->default(1);
            $table->unsignedInteger('players_quota')->after('quota')->nullable();
            $table->unsignedInteger('games_quota')->after('players_quota')->nullable();
            $table->decimal('participation_fee', 10, 2)->after('prize')->nullable();
            $table->string('phase')->after('participation_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'duration',
                'players_quota',
                'games_quota',
                'participation_fee',
                'phase'
            ]);
        });
    }
};
