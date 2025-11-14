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
            $table->enum('score_count_flag', ['0', '1'])->default('0')->after('warns');
        });
    }

    public function down()
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropColumn('score_count_flag');
        });
    }
};
