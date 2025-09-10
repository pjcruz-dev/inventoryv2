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
        Schema::create('asset_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('action'); // 'assigned', 'unassigned', 'transferred', 'created', 'updated'
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('old_values')->nullable(); // Store previous asset state
            $table->json('new_values')->nullable(); // Store new asset state
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('performed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_timeline');
    }
};
