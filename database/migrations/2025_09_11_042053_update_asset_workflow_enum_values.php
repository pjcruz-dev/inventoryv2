<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No schema changes needed as status and movement are already string fields
        // Just ensure we have all the required values documented
        
        // Status values supported:
        // - Active (for returned/available assets)
        // - Pending Confirmation (for newly assigned assets awaiting user confirmation)
        // - Inactive
        // - Under Maintenance
        // - Issue Reported
        // - Disposed
        
        // Movement values supported:
        // - New Arrival (for newly created/imported assets)
        // - Deployed Tagged (for confirmed assignments)
        // - Returned (for returned assets)
        // - Deployed
        // - Disposed
        
        // Update any assets with old movement values to new standardized ones
        DB::table('assets')->where('movement', 'Deployed')->update(['movement' => 'Deployed Tagged']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert movement values back
        DB::table('assets')->where('movement', 'Deployed Tagged')->update(['movement' => 'Deployed']);
    }
};
