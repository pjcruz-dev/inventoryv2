<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssetApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes with authentication
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    
    // Asset Management API
    Route::apiResource('assets', AssetApiController::class)->names([
        'index' => 'api.assets.index',
        'store' => 'api.assets.store',
        'show' => 'api.assets.show',
        'update' => 'api.assets.update',
        'destroy' => 'api.assets.destroy'
    ]);
    Route::get('assets/statistics/overview', [AssetApiController::class, 'statistics'])->name('api.assets.statistics');
    
    // Additional API endpoints can be added here
    Route::get('dashboard/stats', function (Request $request) {
        return response()->json([
            'total_assets' => \App\Models\Asset::count(),
            'total_users' => \App\Models\User::count(),
            'assigned_assets' => \App\Models\Asset::whereNotNull('assigned_to')->count(),
            'available_assets' => \App\Models\Asset::whereNull('assigned_to')->count(),
        ]);
    })->name('api.dashboard.stats');
});