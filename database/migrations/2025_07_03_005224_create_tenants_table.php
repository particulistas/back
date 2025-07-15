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
         Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('room')->nullable();
            $table->json('pets')->nullable();
            $table->boolean('accept_no_smoking')->nullable();
            $table->boolean('can_provide_documentation')->nullable();
            $table->boolean('can_provide_references')->nullable();
            $table->boolean('no_credit_issues')->nullable();
            $table->boolean('not_real_estate_professional')->nullable();
            $table->text('additional_info')->nullable();
            $table->string('income_percentage', 20)->nullable();
            $table->string('minimum_stay', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
