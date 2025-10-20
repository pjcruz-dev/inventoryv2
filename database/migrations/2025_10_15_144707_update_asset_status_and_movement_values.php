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
        // Update status values to new format
        DB::table('assets')->where('status', 'Available')->update(['status' => 'AVAILABLE']);
        DB::table('assets')->where('status', 'Active')->update(['status' => 'ACTIVE']);
        DB::table('assets')->where('status', 'Inactive')->update(['status' => 'AVAILABLE']); // Map Inactive to AVAILABLE
        DB::table('assets')->where('status', 'Under Maintenance')->update(['status' => 'MAINTENANCE']);
        DB::table('assets')->where('status', 'Issue Reported')->update(['status' => 'MAINTENANCE']); // Map Issue Reported to MAINTENANCE
        DB::table('assets')->where('status', 'Pending Confirmation')->update(['status' => 'PENDING CONFIRMATION']);
        DB::table('assets')->where('status', 'Retired')->update(['status' => 'For Disposal']); // Map Retired to For Disposal
        DB::table('assets')->where('status', 'Damaged')->update(['status' => 'For Disposal']); // Map Damaged to For Disposal
        DB::table('assets')->where('status', 'Disposed')->update(['status' => 'For Disposal']); // Map Disposed to For Disposal
        DB::table('assets')->where('status', 'Assigned')->update(['status' => 'ACTIVE']); // Map Assigned to ACTIVE
        
        // Update movement values to new format
        DB::table('assets')->where('movement', 'New Arrival')->update(['movement' => 'NEW ARRIVAL']);
        DB::table('assets')->where('movement', 'Deployed')->update(['movement' => 'DEPLOYED']);
        DB::table('assets')->where('movement', 'Deployed Tagged')->update(['movement' => 'DEPLOYED']);
        DB::table('assets')->where('movement', 'Returned')->update(['movement' => 'RETURN']);
        DB::table('assets')->where('movement', 'Disposed')->update(['movement' => 'DISPOSED']);
        DB::table('assets')->where('movement', 'Transferred')->update(['movement' => 'DEPLOYED']); // Map Transferred to DEPLOYED
        
        // Update any remaining old status values to default
        DB::table('assets')->whereNotIn('status', ['AVAILABLE', 'MAINTENANCE', 'PENDING CONFIRMATION', 'ACTIVE', 'For Disposal'])
            ->update(['status' => 'AVAILABLE']);
            
        // Update any remaining old movement values to default
        DB::table('assets')->whereNotIn('movement', ['RETURN', 'NEW ARRIVAL', 'DISPOSED', 'DEPLOYED'])
            ->update(['movement' => 'NEW ARRIVAL']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values back to old format
        DB::table('assets')->where('status', 'AVAILABLE')->update(['status' => 'Available']);
        DB::table('assets')->where('status', 'ACTIVE')->update(['status' => 'Active']);
        DB::table('assets')->where('status', 'MAINTENANCE')->update(['status' => 'Under Maintenance']);
        DB::table('assets')->where('status', 'PENDING CONFIRMATION')->update(['status' => 'Pending Confirmation']);
        DB::table('assets')->where('status', 'For Disposal')->update(['status' => 'Disposed']);
        
        // Revert movement values back to old format
        DB::table('assets')->where('movement', 'NEW ARRIVAL')->update(['movement' => 'New Arrival']);
        DB::table('assets')->where('movement', 'DEPLOYED')->update(['movement' => 'Deployed']);
        DB::table('assets')->where('movement', 'RETURN')->update(['movement' => 'Returned']);
        DB::table('assets')->where('movement', 'DISPOSED')->update(['movement' => 'Disposed']);
    }
};
