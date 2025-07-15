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
        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reaction'); // 'like', 'love', 'laugh', 'angry', etc.
            $table->timestamps();

            // Ensure a user can only react once per message with the same reaction
            $table->unique(['message_id', 'user_id', 'reaction']);
            $table->index(['message_id', 'reaction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_reactions');
    }
};
