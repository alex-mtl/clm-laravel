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
        Schema::table('clubs', function (Blueprint $table) {
            // Add description if it doesn't exist
            if (!Schema::hasColumn('clubs', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            $table->string('phone_number')->nullable()->after('description');
            $table->string('website')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            //
        });
    }
};
