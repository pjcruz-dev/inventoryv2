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
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->boolean('accountability_printed')->default(false);
            $table->timestamp('accountability_printed_at')->nullable();
            $table->unsignedBigInteger('accountability_printed_by')->nullable();
            
            $table->foreign('accountability_printed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropForeign(['accountability_printed_by']);
            $table->dropColumn(['accountability_printed', 'accountability_printed_at', 'accountability_printed_by']);
        });
    }
};