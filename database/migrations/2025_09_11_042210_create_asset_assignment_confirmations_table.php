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
        Schema::create('asset_assignment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('confirmation_token')->unique();
            $table->enum('status', ['pending', 'confirmed', 'declined'])->default('pending');
            $table->timestamp('assigned_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'assigned_at']);
            $table->index(['confirmation_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignment_confirmations');
    }
};
