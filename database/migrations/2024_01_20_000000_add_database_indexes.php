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
        // Add indexes to assets table for better performance (only if table exists)
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                // Only add indexes if columns exist
                if (Schema::hasColumn('assets', 'category_id') && Schema::hasColumn('assets', 'status')) {
                    $table->index(['category_id', 'status']);
                }
                if (Schema::hasColumn('assets', 'vendor_id') && Schema::hasColumn('assets', 'status')) {
                    $table->index(['vendor_id', 'status']);
                }
                if (Schema::hasColumn('assets', 'assigned_to') && Schema::hasColumn('assets', 'status')) {
                    $table->index(['assigned_to', 'status']);
                }
                if (Schema::hasColumn('assets', 'entity') && Schema::hasColumn('assets', 'status')) {
                    $table->index(['entity', 'status']);
                }
                if (Schema::hasColumn('assets', 'movement') && Schema::hasColumn('assets', 'created_at')) {
                    $table->index(['movement', 'created_at']);
                }
                if (Schema::hasColumn('assets', 'purchase_date') && Schema::hasColumn('assets', 'warranty_end')) {
                    $table->index(['purchase_date', 'warranty_end']);
                }
                if (Schema::hasColumn('assets', 'created_at') && Schema::hasColumn('assets', 'updated_at')) {
                    $table->index(['created_at', 'updated_at']);
                }
            });
        }

        // Add indexes to users table (only if table exists)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Only add indexes if columns exist
                if (Schema::hasColumn('users', 'department_id') && Schema::hasColumn('users', 'status')) {
                    $table->index(['department_id', 'status']);
                }
                if (Schema::hasColumn('users', 'entity') && Schema::hasColumn('users', 'status')) {
                    $table->index(['entity', 'status']);
                }
                if (Schema::hasColumn('users', 'created_at') && Schema::hasColumn('users', 'updated_at')) {
                    $table->index(['created_at', 'updated_at']);
                }
            });
        }

        // Add indexes to logs table for better query performance (only if table exists)
        if (Schema::hasTable('logs')) {
            Schema::table('logs', function (Blueprint $table) {
                // Only add indexes if columns exist
                if (Schema::hasColumn('logs', 'user_id') && Schema::hasColumn('logs', 'created_at')) {
                    $table->index(['user_id', 'created_at']);
                }
                if (Schema::hasColumn('logs', 'asset_id') && Schema::hasColumn('logs', 'created_at')) {
                    $table->index(['asset_id', 'created_at']);
                }
                if (Schema::hasColumn('logs', 'event_type') && Schema::hasColumn('logs', 'created_at')) {
                    $table->index(['event_type', 'created_at']);
                }
                if (Schema::hasColumn('logs', 'category') && Schema::hasColumn('logs', 'created_at')) {
                    $table->index(['category', 'created_at']);
                }
            });
        }

        // Add indexes to asset_timeline table (only if table exists)
        if (Schema::hasTable('asset_timeline')) {
            Schema::table('asset_timeline', function (Blueprint $table) {
                // Only add indexes if columns exist
                if (Schema::hasColumn('asset_timeline', 'asset_id') && Schema::hasColumn('asset_timeline', 'performed_at')) {
                    $table->index(['asset_id', 'performed_at']);
                }
                if (Schema::hasColumn('asset_timeline', 'performed_by') && Schema::hasColumn('asset_timeline', 'performed_at')) {
                    $table->index(['performed_by', 'performed_at']);
                }
                if (Schema::hasColumn('asset_timeline', 'action') && Schema::hasColumn('asset_timeline', 'performed_at')) {
                    $table->index(['action', 'performed_at']);
                }
            });
        }

        // Add indexes to asset_assignments table (only if table exists)
        if (Schema::hasTable('asset_assignments')) {
            Schema::table('asset_assignments', function (Blueprint $table) {
                // Only add indexes if columns exist
                if (Schema::hasColumn('asset_assignments', 'asset_id') && Schema::hasColumn('asset_assignments', 'status')) {
                    $table->index(['asset_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignments', 'user_id') && Schema::hasColumn('asset_assignments', 'status')) {
                    $table->index(['user_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignments', 'assigned_date') && Schema::hasColumn('asset_assignments', 'status')) {
                    $table->index(['assigned_date', 'status']);
                }
            });
        }

        // Add indexes to asset_assignment_confirmations table (only if table exists)
        if (Schema::hasTable('asset_assignment_confirmations')) {
            Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
                // Only add indexes if columns exist
                if (Schema::hasColumn('asset_assignment_confirmations', 'asset_id') && Schema::hasColumn('asset_assignment_confirmations', 'status')) {
                    $table->index(['asset_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignment_confirmations', 'user_id') && Schema::hasColumn('asset_assignment_confirmations', 'status')) {
                    $table->index(['user_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignment_confirmations', 'created_at') && Schema::hasColumn('asset_assignment_confirmations', 'status')) {
                    $table->index(['created_at', 'status']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from assets table (only if table exists)
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                // Only drop indexes if columns exist
                if (Schema::hasColumn('assets', 'category_id') && Schema::hasColumn('assets', 'status')) {
                    $table->dropIndex(['category_id', 'status']);
                }
                if (Schema::hasColumn('assets', 'vendor_id') && Schema::hasColumn('assets', 'status')) {
                    $table->dropIndex(['vendor_id', 'status']);
                }
                if (Schema::hasColumn('assets', 'assigned_to') && Schema::hasColumn('assets', 'status')) {
                    $table->dropIndex(['assigned_to', 'status']);
                }
                if (Schema::hasColumn('assets', 'entity') && Schema::hasColumn('assets', 'status')) {
                    $table->dropIndex(['entity', 'status']);
                }
                if (Schema::hasColumn('assets', 'movement') && Schema::hasColumn('assets', 'created_at')) {
                    $table->dropIndex(['movement', 'created_at']);
                }
                if (Schema::hasColumn('assets', 'purchase_date') && Schema::hasColumn('assets', 'warranty_end')) {
                    $table->dropIndex(['purchase_date', 'warranty_end']);
                }
                if (Schema::hasColumn('assets', 'created_at') && Schema::hasColumn('assets', 'updated_at')) {
                    $table->dropIndex(['created_at', 'updated_at']);
                }
            });
        }

        // Remove indexes from users table (only if table exists)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Only drop indexes if columns exist
                if (Schema::hasColumn('users', 'department_id') && Schema::hasColumn('users', 'status')) {
                    $table->dropIndex(['department_id', 'status']);
                }
                if (Schema::hasColumn('users', 'entity') && Schema::hasColumn('users', 'status')) {
                    $table->dropIndex(['entity', 'status']);
                }
                if (Schema::hasColumn('users', 'created_at') && Schema::hasColumn('users', 'updated_at')) {
                    $table->dropIndex(['created_at', 'updated_at']);
                }
            });
        }

        // Remove indexes from logs table (only if table exists)
        if (Schema::hasTable('logs')) {
            Schema::table('logs', function (Blueprint $table) {
                // Only drop indexes if columns exist
                if (Schema::hasColumn('logs', 'user_id') && Schema::hasColumn('logs', 'created_at')) {
                    $table->dropIndex(['user_id', 'created_at']);
                }
                if (Schema::hasColumn('logs', 'asset_id') && Schema::hasColumn('logs', 'created_at')) {
                    $table->dropIndex(['asset_id', 'created_at']);
                }
                if (Schema::hasColumn('logs', 'event_type') && Schema::hasColumn('logs', 'created_at')) {
                    $table->dropIndex(['event_type', 'created_at']);
                }
                if (Schema::hasColumn('logs', 'category') && Schema::hasColumn('logs', 'created_at')) {
                    $table->dropIndex(['category', 'created_at']);
                }
            });
        }

        // Remove indexes from asset_timeline table (only if table exists)
        if (Schema::hasTable('asset_timeline')) {
            Schema::table('asset_timeline', function (Blueprint $table) {
                // Only drop indexes if columns exist
                if (Schema::hasColumn('asset_timeline', 'asset_id') && Schema::hasColumn('asset_timeline', 'performed_at')) {
                    $table->dropIndex(['asset_id', 'performed_at']);
                }
                if (Schema::hasColumn('asset_timeline', 'performed_by') && Schema::hasColumn('asset_timeline', 'performed_at')) {
                    $table->dropIndex(['performed_by', 'performed_at']);
                }
                if (Schema::hasColumn('asset_timeline', 'action') && Schema::hasColumn('asset_timeline', 'performed_at')) {
                    $table->dropIndex(['action', 'performed_at']);
                }
            });
        }

        // Remove indexes from asset_assignments table (only if table exists)
        if (Schema::hasTable('asset_assignments')) {
            Schema::table('asset_assignments', function (Blueprint $table) {
                // Only drop indexes if columns exist
                if (Schema::hasColumn('asset_assignments', 'asset_id') && Schema::hasColumn('asset_assignments', 'status')) {
                    $table->dropIndex(['asset_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignments', 'user_id') && Schema::hasColumn('asset_assignments', 'status')) {
                    $table->dropIndex(['user_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignments', 'assigned_date') && Schema::hasColumn('asset_assignments', 'status')) {
                    $table->dropIndex(['assigned_date', 'status']);
                }
            });
        }

        // Remove indexes from asset_assignment_confirmations table (only if table exists)
        if (Schema::hasTable('asset_assignment_confirmations')) {
            Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
                // Only drop indexes if columns exist
                if (Schema::hasColumn('asset_assignment_confirmations', 'asset_id') && Schema::hasColumn('asset_assignment_confirmations', 'status')) {
                    $table->dropIndex(['asset_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignment_confirmations', 'user_id') && Schema::hasColumn('asset_assignment_confirmations', 'status')) {
                    $table->dropIndex(['user_id', 'status']);
                }
                if (Schema::hasColumn('asset_assignment_confirmations', 'created_at') && Schema::hasColumn('asset_assignment_confirmations', 'status')) {
                    $table->dropIndex(['created_at', 'status']);
                }
            });
        }
    }
};
