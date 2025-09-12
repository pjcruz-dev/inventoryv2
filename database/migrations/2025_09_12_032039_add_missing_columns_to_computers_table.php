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
        Schema::table('computers', function (Blueprint $table) {
            // Rename existing columns to match controller expectations
            $table->renameColumn('ram', 'memory');
            $table->renameColumn('os', 'operating_system');
            
            // Add new columns
            $table->string('graphics_card', 100)->nullable()->after('storage');
            $table->enum('computer_type', ['Desktop', 'Laptop', 'Server', 'Workstation'])->after('operating_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('computers', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['graphics_card', 'computer_type']);
            
            // Rename columns back to original names
            $table->renameColumn('memory', 'ram');
            $table->renameColumn('operating_system', 'os');
        });
    }
};
