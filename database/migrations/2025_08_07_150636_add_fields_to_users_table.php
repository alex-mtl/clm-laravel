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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('name')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('phone_number')->after('last_name')->nullable();
            $table->foreignId('club_id')->after('phone_number')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->string('telegram')->after('club_id')->nullable();

            // Optional: Drop the original name column if you're replacing it
            // $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone_number',
                'club_id',
                'telegram'
            ]);

            // If you dropped name, recreate it
            // $table->string('name')->after('id');
        });
    }
};
