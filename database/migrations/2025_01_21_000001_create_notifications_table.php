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
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the old user_id column and foreign key
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the new columns
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
            
            // Restore the original user_id column
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'read_at']);
        });
    }
};
