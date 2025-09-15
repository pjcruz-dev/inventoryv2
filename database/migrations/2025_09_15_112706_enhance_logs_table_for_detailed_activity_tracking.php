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
            // Add enhanced fields for detailed activity tracking
            $table->json('action_details')->nullable()->after('new_values')->comment('Detailed information about the specific action performed');
            $table->json('affected_fields')->nullable()->after('action_details')->comment('List of fields that were changed with before/after values');
            $table->json('metadata')->nullable()->after('affected_fields')->comment('Additional context data like request parameters, user agent details, etc.');
            $table->string('session_id', 100)->nullable()->after('metadata')->comment('Session ID for tracking user sessions');
            $table->string('request_method', 10)->nullable()->after('session_id')->comment('HTTP request method (GET, POST, PUT, DELETE)');
            $table->text('request_url')->nullable()->after('request_method')->comment('Full URL of the request');
            $table->json('request_parameters')->nullable()->after('request_url')->comment('Request parameters and form data');
            $table->string('browser_name', 100)->nullable()->after('request_parameters')->comment('Browser name extracted from user agent');
            $table->string('operating_system', 100)->nullable()->after('browser_name')->comment('Operating system extracted from user agent');
            $table->timestamp('action_timestamp')->nullable()->after('operating_system')->comment('Precise timestamp when action was performed');
            
            // Add indexes for better query performance
            $table->index('session_id');
            $table->index('action_timestamp');
            $table->index(['user_id', 'action_timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'action_timestamp']);
            $table->dropIndex('action_timestamp');
            $table->dropIndex('session_id');
            
            $table->dropColumn([
                'action_details',
                'affected_fields', 
                'metadata',
                'session_id',
                'request_method',
                'request_url',
                'request_parameters',
                'browser_name',
                'operating_system',
                'action_timestamp'
            ]);
        });
    }
};
