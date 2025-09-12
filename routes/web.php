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
use App\Http\Controllers\AssetConfirmationController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return redirect()->route('login');
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
    Route::get('assets/reports/employee-assets', [AssetController::class, 'printEmployeeAssets'])->name('assets.print-employee-assets');
    Route::get('assets/reports/employee-assets/{user}', [AssetController::class, 'printSingleEmployeeAssets'])->name('assets.print-single-employee-assets');
    Route::resource('computers', ComputerController::class);
    Route::resource('monitors', MonitorController::class);
    Route::resource('printers', PrinterController::class);
    Route::resource('peripherals', PeripheralController::class);
    
    // User and organization management
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('vendors', VendorController::class);
    
    // User role management routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/role-management', [UserController::class, 'roleManagement'])
            ->name('role-management')
            ->middleware('check.permission:manage_user_roles');
        Route::post('/{user}/assign-role', [UserController::class, 'assignRole'])
            ->name('assign-role')
            ->middleware('check.permission:assign_roles');
        Route::post('/{user}/remove-role', [UserController::class, 'removeRole'])
            ->name('remove-role')
            ->middleware('check.permission:remove_roles');
        Route::post('/bulk-assign-roles', [UserController::class, 'bulkAssignRoles'])
            ->name('bulk-assign-roles')
            ->middleware('check.permission:bulk_assign_roles');
        Route::get('/{user}/roles', [UserController::class, 'getUserRoles'])
             ->name('get-roles')
             ->middleware('check.permission:view_user_roles');
     });
    
    // Roles and permissions management
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Dashboard route
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Import/Export routes - Enhanced with comprehensive security
    require __DIR__.'/import-export.php';
    
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
    
    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/all', [NotificationController::class, 'getAll'])->name('all');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
    });
});

// Asset Confirmation routes (public - no auth required)
Route::prefix('asset-confirmation')->name('asset-confirmation.')->group(function () {
    Route::get('/show/{token}', [AssetConfirmationController::class, 'show'])->name('show');
    Route::get('/confirm/{token}', [AssetConfirmationController::class, 'confirm'])->name('confirm');
    Route::get('/decline/{token}', [AssetConfirmationController::class, 'decline'])->name('decline');
    Route::get('/decline-form/{token}', [AssetConfirmationController::class, 'showDeclineForm'])->name('decline-form');
    Route::post('/decline/{token}', [AssetConfirmationController::class, 'processDecline'])->name('process-decline');
});
