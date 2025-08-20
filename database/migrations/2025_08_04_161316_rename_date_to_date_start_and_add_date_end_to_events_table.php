<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
// Rename date to date_start
            $table->renameColumn('date', 'date_start');

// Add date_end column
            $table->dateTime('date_end')->nullable()->after('date_start');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
// Reverse the changes
            $table->renameColumn('date_start', 'date');
            $table->dropColumn('date_end');
        });
    }
};
