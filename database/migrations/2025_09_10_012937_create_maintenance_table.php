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
        Schema::create('maintenance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('vendor_id');
            $table->text('issue_reported')->nullable();
            $table->text('repair_action')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->string('status', 50);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
