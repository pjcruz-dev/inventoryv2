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
        // Check if the table exists and has the old structure
        if (Schema::hasTable('notifications')) {
            // Check if the table has user_id column (old structure)
            if (Schema::hasColumn('notifications', 'user_id')) {
                // Drop the old foreign key and column
                Schema::table('notifications', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
            
            // Check if notifiable_type column doesn't exist
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                Schema::table('notifications', function (Blueprint $table) {
                    // Add the polymorphic relationship columns
                    $table->string('notifiable_type');
                    $table->unsignedBigInteger('notifiable_id');
                    
                    // Add new columns for enhanced notifications
                    $table->timestamp('expires_at')->nullable();
                    $table->boolean('is_urgent')->default(false);
                    $table->string('action_url')->nullable(); // URL for action button
                    $table->string('action_text')->nullable(); // Text for action button
                    
                    // Add new indexes for performance
                    $table->index(['notifiable_type', 'notifiable_id']);
                    $table->index(['type', 'created_at']);
                    $table->index(['read_at', 'created_at']);
                    $table->index('is_urgent');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                // Drop the new columns if they exist
                if (Schema::hasColumn('notifications', 'notifiable_type')) {
                    $table->dropIndex(['notifiable_type', 'notifiable_id']);
                    $table->dropIndex(['type', 'created_at']);
                    $table->dropIndex(['read_at', 'created_at']);
                    $table->dropIndex('is_urgent');
                    
                    $table->dropColumn([
                        'notifiable_type',
                        'notifiable_id',
                        'expires_at',
                        'is_urgent',
                        'action_url',
                        'action_text'
                    ]);
                }
                
                // Restore the original user_id column
                if (!Schema::hasColumn('notifications', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->index(['user_id', 'read_at']);
                }
            });
        }
    }
};