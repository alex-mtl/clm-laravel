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
            $table->smallInteger('win')->nullable()->default(0);
            $table->decimal('score_base', 10, 4)->nullable()->default(0);
            $table->decimal('score_1', 10, 4)->nullable()->default(0);
            $table->decimal('score_2', 10, 4)->nullable()->default(0);
            $table->decimal('score_3', 10, 4)->nullable()->default(0);
            $table->decimal('score_4', 10, 4)->nullable()->default(0);
            $table->decimal('score_total', 10, 4)->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropColumn([
                'win',
                'score_base',
                'score_1',
                'score_2',
                'score_3',
                'score_4',
                'score_total'
            ]);
        });
    }
};
