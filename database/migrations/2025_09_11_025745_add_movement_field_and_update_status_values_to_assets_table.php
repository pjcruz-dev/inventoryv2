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
        Schema::table('assets', function (Blueprint $table) {
            // Add movement field to track lifecycle events
            $table->string('movement', 50)->default('New Arrival')->after('status');
        });
        
        // Update existing status values to new format
        DB::table('assets')->where('status', 'deployed')->update(['status' => 'Active', 'movement' => 'Deployed']);
        DB::table('assets')->where('status', 'returned')->update(['status' => 'Inactive', 'movement' => 'Returned']);
        DB::table('assets')->where('status', 'new_arrived')->update(['status' => 'Pending Confirmation', 'movement' => 'New Arrival']);
        DB::table('assets')->where('status', 'problematic')->update(['status' => 'Issue Reported', 'movement' => 'Deployed']);
        DB::table('assets')->where('status', 'disposed')->update(['status' => 'Disposed', 'movement' => 'Disposed']);
        DB::table('assets')->where('status', 'active')->update(['status' => 'Active', 'movement' => 'Deployed']);
        DB::table('assets')->where('status', 'inactive')->update(['status' => 'Inactive', 'movement' => 'Returned']);
        DB::table('assets')->where('status', 'maintenance')->update(['status' => 'Under Maintenance', 'movement' => 'Deployed']);
        
        // Update any remaining statuses to default values
        DB::table('assets')->whereNotIn('status', ['Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'])
            ->update(['status' => 'Pending Confirmation', 'movement' => 'New Arrival']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Remove movement field
            $table->dropColumn('movement');
        });
        
        // Revert status values back to previous format
        DB::table('assets')->where('status', 'Active')->update(['status' => 'deployed']);
        DB::table('assets')->where('status', 'Inactive')->update(['status' => 'returned']);
        DB::table('assets')->where('status', 'Under Maintenance')->update(['status' => 'maintenance']);
        DB::table('assets')->where('status', 'Issue Reported')->update(['status' => 'problematic']);
        DB::table('assets')->where('status', 'Pending Confirmation')->update(['status' => 'new_arrived']);
        DB::table('assets')->where('status', 'Disposed')->update(['status' => 'disposed']);
    }
};
