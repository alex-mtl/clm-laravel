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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('glob_score', 10, 4)->default(0)->after('is_active');
            $table->integer('glob_games')->default(0)->after('glob_score');
            $table->integer('glob_tournaments')->default(0)->after('glob_games');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'glob_score',
                'glob_games',
                'glob_tournaments',
            ]);
        });
    }
};
