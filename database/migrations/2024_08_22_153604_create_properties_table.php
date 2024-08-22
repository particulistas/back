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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->enum('transaction', ['sale', 'rental', 'both']);
            $table->float('sale_price', precision: 53, scale: 4);
            $table->float('rental_price', precision: 53, scale: 4);
            $table->string('m_built');
            $table->string('m_usefull');
            $table->integer('bathrooms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
