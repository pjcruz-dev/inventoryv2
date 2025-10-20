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
        // Update status values to proper case
        DB::table('assets')->where('status', 'AVAILABLE')->update(['status' => 'Available']);
        DB::table('assets')->where('status', 'MAINTENANCE')->update(['status' => 'Maintenance']);
        DB::table('assets')->where('status', 'PENDING CONFIRMATION')->update(['status' => 'Pending Confirmation']);
        DB::table('assets')->where('status', 'ACTIVE')->update(['status' => 'Active']);
        // 'For Disposal' is already in proper case
        
        // Update movement values to proper case
        DB::table('assets')->where('movement', 'RETURN')->update(['movement' => 'Return']);
        DB::table('assets')->where('movement', 'NEW ARRIVAL')->update(['movement' => 'New Arrival']);
        DB::table('assets')->where('movement', 'DISPOSED')->update(['movement' => 'Disposed']);
        DB::table('assets')->where('movement', 'DEPLOYED')->update(['movement' => 'Deployed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values back to uppercase
        DB::table('assets')->where('status', 'Available')->update(['status' => 'AVAILABLE']);
        DB::table('assets')->where('status', 'Maintenance')->update(['status' => 'MAINTENANCE']);
        DB::table('assets')->where('status', 'Pending Confirmation')->update(['status' => 'PENDING CONFIRMATION']);
        DB::table('assets')->where('status', 'Active')->update(['status' => 'ACTIVE']);
        
        // Revert movement values back to uppercase
        DB::table('assets')->where('movement', 'Return')->update(['movement' => 'RETURN']);
        DB::table('assets')->where('movement', 'New Arrival')->update(['movement' => 'NEW ARRIVAL']);
        DB::table('assets')->where('movement', 'Disposed')->update(['movement' => 'DISPOSED']);
        DB::table('assets')->where('movement', 'Deployed')->update(['movement' => 'DEPLOYED']);
    }
};
