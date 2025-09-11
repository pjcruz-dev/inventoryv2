<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\PeripheralController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AssetTimelineController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protected routes
Route::middleware('auth')->group(function () {
    // Asset management
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/{asset}/unassign', [AssetController::class, 'unassign'])->name('assets.unassign');
    Route::post('assets/{asset}/reassign', [AssetController::class, 'reassign'])->name('assets.reassign');
    Route::resource('computers', ComputerController::class);
    Route::resource('monitors', MonitorController::class);
    Route::resource('printers', PrinterController::class);
    Route::resource('peripherals', PeripheralController::class);
    
    // User and organization management
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('vendors', VendorController::class);
    
    // Roles and permissions management
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Dashboard route
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Import/Export routes
    Route::prefix('import-export')->name('import-export.')->group(function () {
        // Template downloads
        Route::get('template/{module}', [ImportExportController::class, 'downloadTemplate'])->name('template');
        
        // Data imports
        Route::post('import/{module}', [ImportExportController::class, 'import'])->name('import');
        
        // Data exports
        Route::get('export/{module}', [ImportExportController::class, 'export'])->name('export');
        
        // Import results page
        Route::get('results', [ImportExportController::class, 'showResults'])->name('results');
    });
    
    // Activity Logs routes
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/{log}', [LogController::class, 'show'])->name('show');
        Route::get('/export', [LogController::class, 'export'])->name('export');
        Route::post('/clear', [LogController::class, 'clearOldLogs'])->name('clear');
    });
    
    // Asset Timeline routes
    Route::prefix('timeline')->name('timeline.')->group(function () {
        Route::get('/', [AssetTimelineController::class, 'index'])->name('index');
        Route::get('/create', [AssetTimelineController::class, 'create'])->name('create');
        Route::post('/', [AssetTimelineController::class, 'store'])->name('store');
        Route::get('/asset/{asset}', [AssetTimelineController::class, 'show'])->name('show');
    });
});
