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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag', 50)->unique();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_end')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('status', 50)->default('Available'); // Available, Assigned, In Repair, Retired, Disposed
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('asset_categories');
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
