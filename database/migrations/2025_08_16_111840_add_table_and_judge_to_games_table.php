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
        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('judge_id')
                ->after('description')
                ->nullable()
                ->constrained('users')
                ->onDelete('SET NULL')
            ;
            $table->unsignedInteger('table')->after('judge_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('table');
            $table->dropConstrainedForeignId('judge_id');
        });
    }
};
