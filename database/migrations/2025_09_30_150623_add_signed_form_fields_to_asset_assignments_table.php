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
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->string('signed_form_path')->nullable()->after('accountability_printed_by');
            $table->timestamp('signed_form_uploaded_at')->nullable()->after('signed_form_path');
            $table->unsignedBigInteger('signed_form_uploaded_by')->nullable()->after('signed_form_uploaded_at');
            $table->text('signed_form_description')->nullable()->after('signed_form_uploaded_by');
            $table->string('signed_form_email_subject')->nullable()->after('signed_form_description');
            $table->boolean('signed_form_email_sent')->default(false)->after('signed_form_email_subject');
            $table->timestamp('signed_form_email_sent_at')->nullable()->after('signed_form_email_sent');
            
            $table->foreign('signed_form_uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropForeign(['signed_form_uploaded_by']);
            $table->dropColumn([
                'signed_form_path',
                'signed_form_uploaded_at',
                'signed_form_uploaded_by',
                'signed_form_description',
                'signed_form_email_subject',
                'signed_form_email_sent',
                'signed_form_email_sent_at'
            ]);
        });
    }
};