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
            $table->decimal('score_5', 10, 4)->nullable()->default(0);
            $table->string('mark', 10)->nullable()->default('zero');
            $table->tinyInteger('mark_number')->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropColumn([
                'score_5',
                'mark',
                'mark_number'
            ]);
        });
    }
};
