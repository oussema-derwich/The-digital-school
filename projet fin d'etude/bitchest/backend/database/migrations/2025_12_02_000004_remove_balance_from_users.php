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
            // Enlever balance_eur s'il existe
            if (Schema::hasColumn('users', 'balance_eur')) {
                $table->dropColumn('balance_eur');
            }
            
            // Ajouter la colonne photo si elle n'existe pas
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't rollback - balance_eur should never exist in users table
        // Balance belongs only in wallet table
    }
};
