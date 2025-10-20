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
        // Update any assets with "Disposed" movement to "Deployed"
        // since disposal is handled by the "For Disposal" status
        DB::table('assets')->where('movement', 'Disposed')->update(['movement' => 'Deployed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed as we're removing a value
        // that shouldn't exist according to the requirements
    }
};
