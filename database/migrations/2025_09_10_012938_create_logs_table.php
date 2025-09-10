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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50); // System, Asset
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('event_type', 100);
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('remarks');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
