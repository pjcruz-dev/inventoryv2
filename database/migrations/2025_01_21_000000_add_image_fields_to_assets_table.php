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
            $table->string('image_path')->nullable()->after('notes');
            $table->string('image_alt')->nullable()->after('image_path');
            $table->integer('image_size')->nullable()->after('image_alt');
            $table->string('image_mime_type')->nullable()->after('image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_alt', 'image_size', 'image_mime_type']);
        });
    }
};

