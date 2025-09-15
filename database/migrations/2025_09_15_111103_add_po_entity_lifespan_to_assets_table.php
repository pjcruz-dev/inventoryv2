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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('po_number', 100)->nullable()->after('cost');
            $table->enum('entity', ['MIDC', 'PHILTOWER', 'PRIMUS'])->nullable()->after('po_number');
            $table->integer('lifespan')->nullable()->comment('Lifespan in years')->after('entity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['po_number', 'entity', 'lifespan']);
        });
    }
};
