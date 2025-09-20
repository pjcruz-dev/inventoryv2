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
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            // Add field to track if confirmation was auto-confirmed
            $table->boolean('auto_confirmed')->default(false)->after('status');
            
            // Add field to track the reason for auto-confirmation
            $table->text('auto_confirmation_reason')->nullable()->after('auto_confirmed');
            
            // Add index for better query performance (with custom short name)
            $table->index(['status', 'reminder_count', 'auto_confirmed'], 'aac_status_reminder_auto_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            $table->dropIndex('aac_status_reminder_auto_idx');
            $table->dropColumn(['auto_confirmed', 'auto_confirmation_reason']);
        });
    }
};
