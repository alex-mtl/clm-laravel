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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('request_types');
            $table->foreignId('applicant_id')->constrained('users');

            // Polymorphic target (can be user, event, tournament, etc)
            $table->morphs('target'); // target_id + target_type

            // Current state
            $table->enum('status', ['pending', 'accepted', 'declined', 'canceled', 'expired'])
                ->default('pending');

            // Decision info
            $table->foreignId('responder_id')->nullable()->constrained('users');
            $table->timestamp('responded_at')->nullable();
            $table->text('response_note')->nullable();

            // Additional data storage
            $table->json('data')->nullable(); // Custom fields per request type

            $table->timestamps();
            $table->softDeletes(); // For canceled requests
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
