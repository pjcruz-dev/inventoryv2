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
        // Add indexes to assets table for better performance
        Schema::table('assets', function (Blueprint $table) {
            $table->index(['category_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['entity', 'status']);
            $table->index(['movement', 'created_at']);
            $table->index(['purchase_date', 'warranty_end']);
            $table->index(['created_at', 'updated_at']);
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['department_id', 'status']);
            $table->index(['entity', 'status']);
            $table->index(['created_at', 'updated_at']);
        });

        // Add indexes to logs table for better query performance
        Schema::table('logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['asset_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['category', 'created_at']);
        });

        // Add indexes to asset_timeline table
        Schema::table('asset_timeline', function (Blueprint $table) {
            $table->index(['asset_id', 'performed_at']);
            $table->index(['performed_by', 'performed_at']);
            $table->index(['action', 'performed_at']);
        });

        // Add indexes to asset_assignments table
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->index(['asset_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['assigned_date', 'status']);
        });

        // Add indexes to asset_assignment_confirmations table
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            $table->index(['asset_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['vendor_id', 'status']);
            $table->dropIndex(['assigned_to', 'status']);
            $table->dropIndex(['entity', 'status']);
            $table->dropIndex(['movement', 'created_at']);
            $table->dropIndex(['purchase_date', 'warranty_end']);
            $table->dropIndex(['created_at', 'updated_at']);
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['department_id', 'status']);
            $table->dropIndex(['entity', 'status']);
            $table->dropIndex(['created_at', 'updated_at']);
        });

        // Remove indexes from logs table
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['asset_id', 'created_at']);
            $table->dropIndex(['event_type', 'created_at']);
            $table->dropIndex(['category', 'created_at']);
        });

        // Remove indexes from asset_timeline table
        Schema::table('asset_timeline', function (Blueprint $table) {
            $table->dropIndex(['asset_id', 'performed_at']);
            $table->dropIndex(['performed_by', 'performed_at']);
            $table->dropIndex(['action', 'performed_at']);
        });

        // Remove indexes from asset_assignments table
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropIndex(['asset_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['assigned_date', 'status']);
        });

        // Remove indexes from asset_assignment_confirmations table
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            $table->dropIndex(['asset_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at', 'status']);
        });
    }
};
