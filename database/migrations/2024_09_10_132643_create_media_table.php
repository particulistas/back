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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('properties_id')->constrained()->onDelete('cascade'); //debe ir la subcategoria
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('type')->nullable();
            $table->string('object')->nullable();
            $table->integer('postition')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
