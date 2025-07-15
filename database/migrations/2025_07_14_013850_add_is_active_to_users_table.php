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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->boolean('is_online')->default(false)->after('is_active');
            $table->timestamp('last_seen_at')->nullable()->after('is_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active','is_online', 'last_seen_at']);
        });
    }
};
