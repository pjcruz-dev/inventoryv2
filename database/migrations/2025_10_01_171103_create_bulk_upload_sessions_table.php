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
        Schema::create('bulk_upload_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('asset_ids'); // Array of asset IDs for this session
            $table->json('uploaded_files'); // Array of uploaded file info
            $table->json('pending_files'); // Array of pending file info
            $table->integer('total_files');
            $table->integer('uploaded_count')->default(0);
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_upload_sessions');
    }
};
