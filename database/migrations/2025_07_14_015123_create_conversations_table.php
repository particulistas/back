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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('properties_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Property owner
            $table->foreignId('participant_id')->constrained('users')->onDelete('cascade'); // Other participant
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            // Ensure unique conversation between users for a property
            $table->unique(['properties_id', 'user_id', 'participant_id']);
            $table->index(['user_id', 'is_active']);
            $table->index(['participant_id', 'is_active']);
            $table->index(['properties_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
