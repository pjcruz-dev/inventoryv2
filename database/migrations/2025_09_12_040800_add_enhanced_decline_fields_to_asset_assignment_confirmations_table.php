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
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            $table->string('decline_category')->nullable()->after('notes');
            $table->text('decline_reason')->nullable()->after('decline_category');
            $table->text('decline_comments')->nullable()->after('decline_reason');
            $table->string('contact_preference')->nullable()->after('decline_comments');
            $table->boolean('follow_up_required')->default(false)->after('contact_preference');
            $table->text('follow_up_actions')->nullable()->after('follow_up_required');
            $table->timestamp('follow_up_date')->nullable()->after('follow_up_actions');
            $table->string('decline_severity', 50)->nullable()->after('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignment_confirmations', function (Blueprint $table) {
            $table->dropColumn([
                'decline_category',
                'decline_reason',
                'decline_comments',
                'contact_preference',
                'follow_up_required',
                'follow_up_actions',
                'follow_up_date',
                'decline_severity'
            ]);
        });
    }
};
