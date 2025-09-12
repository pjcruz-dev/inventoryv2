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
        // First, add a temporary column
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('status_temp')->default(1);
        });
        
        // Update the temporary column with integer values
        DB::table('users')->where('status', 'active')->update(['status_temp' => 1]);
        DB::table('users')->where('status', 'inactive')->update(['status_temp' => 0]);
        
        // Drop the old status column and rename the temp column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('status_temp', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add temporary enum column
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status_temp', ['active', 'inactive'])->default('active');
        });
        
        // Update the temporary column with string values
        DB::table('users')->where('status', 1)->update(['status_temp' => 'active']);
        DB::table('users')->where('status', 0)->update(['status_temp' => 'inactive']);
        
        // Drop the integer status column and rename temp column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('status_temp', 'status');
        });
    }
};
