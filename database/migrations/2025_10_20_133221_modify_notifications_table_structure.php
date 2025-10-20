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
            // Make user_id nullable since we're using notifiable_type/notifiable_id
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Drop the foreign key constraint first
            $table->dropForeign(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Restore the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Make user_id not nullable again
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};