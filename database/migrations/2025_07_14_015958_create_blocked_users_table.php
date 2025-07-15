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
       Schema::create('blocked_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who blocks
            $table->foreignId('blocked_user_id')->constrained('users')->onDelete('cascade'); // User being blocked
            $table->string('reason')->nullable(); // Optional reason for blocking
            $table->timestamps();

            // Ensure a user can only block another user once
            $table->unique(['user_id', 'blocked_user_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['blocked_user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_users');
    }
};
