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
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); //debe ir la subcategoria
            $table->enum('transaction', ['sale', 'rental', 'both'])->default('sale');
            $table->float('sale_price', precision: 53, scale: 4)->nullable();
            $table->float('rental_price', precision: 53, scale: 4)->nullable();
            $table->json('bills')->nullable();
            $table->string('m_built')->nullable();
            $table->string('m_usefull')->nullable();
            $table->integer('bathrooms')->nullable();//numero de baños
            $table->integer('number_plants')->nullable();//numero de plantas
            $table->integer('number_habs')->nullable();//numero de plantas
            $table->string('distibutions')->nullable();
            $table->string('state')->nullable(); //estado del inmueble
            $table->string('equipment')->nullable();
            $table->json('ubication')->nullable();
            $table->json('characteristics')->nullable();
            $table->json('preferences')->nullable();
            $table->json('cohabitation')->nullable();
            $table->integer('antique')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('hide_address')->default(0);
            $table->boolean('top_floor')->default(0);
            $table->string('door')->nullable();
            $table->text('description')->nullable();
            $table->text('optionals')->nullable();
            $table->enum('energy_certificate', ['yes', 'process', 'exempt'])->default('exempt');
            $table->json('energy_certificate_yes')->nullable();
            $table->boolean('publish_phone')->default(0);
            $table->string('phone')->nullable();
            $table->enum('phone_characteristics', ['calls', 'whatsapp', 'both'])->default('both');
            $table->enum('status', ['Daft', 'Publish'])->default('Draft');
            $table->text('optionals')->nullable(); //caracteristicas opcionales de la vivienda (habitaciones)
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
