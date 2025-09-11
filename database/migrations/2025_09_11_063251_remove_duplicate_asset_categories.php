<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\AssetCategory;
use App\Models\Asset;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all categories grouped by name
        $duplicateCategories = DB::table('asset_categories')
            ->select('name', DB::raw('MIN(id) as keep_id'), DB::raw('GROUP_CONCAT(id) as all_ids'))
            ->groupBy('name')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        foreach ($duplicateCategories as $category) {
            $allIds = explode(',', $category->all_ids);
            $keepId = $category->keep_id;
            $deleteIds = array_filter($allIds, function($id) use ($keepId) {
                return $id != $keepId;
            });

            if (!empty($deleteIds)) {
                // Update assets to use the kept category
                DB::table('assets')
                    ->whereIn('category_id', $deleteIds)
                    ->update(['category_id' => $keepId]);

                // Delete duplicate categories
                DB::table('asset_categories')
                    ->whereIn('id', $deleteIds)
                    ->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as we've lost the duplicate data
        // Consider creating a backup before running this migration
    }
};
