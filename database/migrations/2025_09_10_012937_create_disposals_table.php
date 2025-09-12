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
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->datetime('disposal_date');
            $table->string('disposal_type', 50);
            $table->decimal('disposal_value', 12, 2)->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposals');
    }
};
