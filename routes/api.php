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

// Public API routes
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Protected API routes with Sanctum authentication
Route::middleware('auth:sanctum')->group(function () {
    // Asset verification and retrieval for specific user
    Route::get('/users/{user}/assets/verify', [AssetApiController::class, 'verifyUserAssets'])
        ->name('api.users.assets.verify');
});