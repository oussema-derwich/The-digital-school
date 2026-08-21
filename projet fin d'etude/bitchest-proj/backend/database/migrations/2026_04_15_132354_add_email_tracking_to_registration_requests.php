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
            // Ajouter le tracking d'email d'approbation
            $table->boolean('approval_email_sent')->default(false)->after('is_approved');
            $table->timestamp('approval_email_sent_at')->nullable()->after('approval_email_sent');
            $table->text('approval_email_error')->nullable()->after('approval_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_requests', function (Blueprint $table) {
            $table->dropColumn(['approval_email_sent', 'approval_email_sent_at', 'approval_email_error']);
        });
    }
};
