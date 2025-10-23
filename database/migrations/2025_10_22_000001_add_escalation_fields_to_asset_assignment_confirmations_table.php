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
            $table->boolean('escalated')->default(false)->after('follow_up_required');
            $table->timestamp('escalated_at')->nullable()->after('escalated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            $table->dropColumn(['escalated', 'escalated_at']);
        });
    }
};

