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
        Schema::table('logs', function (Blueprint $table) {
            // Add polymorphic columns for ActivityLoggable trait
            $table->string('loggable_type')->nullable()->after('id');
            $table->unsignedBigInteger('loggable_id')->nullable()->after('loggable_type');
            
            // Add activity logging specific columns
            $table->string('description')->nullable()->after('event_type');
            $table->json('old_values')->nullable()->after('description');
            $table->json('new_values')->nullable()->after('old_values');
            
            // Add index for polymorphic relationship
            $table->index(['loggable_type', 'loggable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['loggable_type', 'loggable_id']);
            $table->dropColumn(['loggable_type', 'loggable_id', 'description', 'old_values', 'new_values']);
        });
    }
};
