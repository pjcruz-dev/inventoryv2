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
            // Update status column to support new lifecycle statuses
            $table->string('status', 50)->default('new_arrived')->change();
        });
        
        // Update existing records to use new status values
        DB::table('assets')->where('status', 'active')->update(['status' => 'deployed']);
        DB::table('assets')->where('status', 'inactive')->update(['status' => 'returned']);
        DB::table('assets')->where('status', 'maintenance')->update(['status' => 'problematic']);
        DB::table('assets')->where('status', 'disposed')->update(['status' => 'disposed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Revert status column back to original values
            $table->string('status', 50)->default('Available')->change();
        });
        
        // Revert status values back to original
        DB::table('assets')->where('status', 'deployed')->update(['status' => 'active']);
        DB::table('assets')->where('status', 'returned')->update(['status' => 'inactive']);
        DB::table('assets')->where('status', 'problematic')->update(['status' => 'maintenance']);
        DB::table('assets')->where('status', 'disposed')->update(['status' => 'disposed']);
    }
};
