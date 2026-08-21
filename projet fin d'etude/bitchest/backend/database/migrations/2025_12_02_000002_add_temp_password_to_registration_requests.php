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
        Schema::table('registration_requests', function (Blueprint $table) {
            // Ajouter la colonne temp_password si elle n'existe pas
            if (!Schema::hasColumn('registration_requests', 'temp_password')) {
                $table->string('temp_password')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_requests', function (Blueprint $table) {
            if (Schema::hasColumn('registration_requests', 'temp_password')) {
                $table->dropColumn('temp_password');
            }
        });
    }
};
