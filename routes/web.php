<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetAssignmentConfirmationController;
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
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DisposalController;

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
    Route::post('assets/bulk/print-labels', [AssetController::class, 'bulkPrintLabels'])->name('assets.bulk-print-labels');
    Route::get('assets/print-all-labels', [AssetController::class, 'printAllAssetLabels'])->name('assets.print-all-labels');
    
    // Asset Categories
    Route::resource('asset-categories', AssetCategoryController::class);
    Route::get('asset-categories/export', [AssetCategoryController::class, 'export'])->name('asset-categories.export');
    Route::get('asset-categories/export/excel', [AssetCategoryController::class, 'exportExcel'])->name('asset-categories.export.excel');
    Route::get('asset-categories/download/template', [AssetCategoryController::class, 'downloadTemplate'])->name('asset-categories.download-template');
    Route::get('asset-categories/import/form', [AssetCategoryController::class, 'importForm'])->name('asset-categories.import-form');
    Route::post('asset-categories/import', [AssetCategoryController::class, 'import'])->name('asset-categories.import');
    
    // Asset Assignments
    Route::resource('asset-assignments', AssetAssignmentController::class);
    Route::get('asset-assignments/export', [AssetAssignmentController::class, 'export'])->name('asset-assignments.export');
    Route::get('asset-assignments/export/excel', [AssetAssignmentController::class, 'exportExcel'])->name('asset-assignments.export.excel');
    Route::get('asset-assignments/download/template', [AssetAssignmentController::class, 'downloadTemplate'])->name('asset-assignments.download-template');
    Route::get('asset-assignments/import/form', [AssetAssignmentController::class, 'importForm'])->name('asset-assignments.import-form');
    Route::post('asset-assignments/import', [AssetAssignmentController::class, 'import'])->name('asset-assignments.import');
    Route::post('asset-assignments/{assignment}/return', [AssetAssignmentController::class, 'markAsReturned'])->name('asset-assignments.return');
    
    // Asset Assignment Confirmations
    Route::resource('asset-assignment-confirmations', AssetAssignmentConfirmationController::class);
    Route::get('asset-assignment-confirmations/export', [AssetAssignmentConfirmationController::class, 'export'])->name('asset-assignment-confirmations.export');
    Route::get('asset-assignment-confirmations/export/excel', [AssetAssignmentConfirmationController::class, 'exportExcel'])->name('asset-assignment-confirmations.export.excel');
    Route::get('asset-assignment-confirmations/download/template', [AssetAssignmentConfirmationController::class, 'downloadTemplate'])->name('asset-assignment-confirmations.download-template');
    Route::get('asset-assignment-confirmations/import/form', [AssetAssignmentConfirmationController::class, 'importForm'])->name('asset-assignment-confirmations.import-form');
    Route::post('asset-assignment-confirmations/import', [AssetAssignmentConfirmationController::class, 'import'])->name('asset-assignment-confirmations.import');
    Route::get('asset-assignment-confirmations/{confirmation}/send-reminder', [AssetAssignmentConfirmationController::class, 'sendReminder'])->name('asset-assignment-confirmations.send-reminder');
    Route::get('asset-assignment-confirmations/confirm/{token}', [AssetAssignmentConfirmationController::class, 'confirmByToken'])->name('asset-assignment-confirmations.confirm');
    Route::get('asset-assignment-confirmations/decline/{token}', [AssetAssignmentConfirmationController::class, 'declineByToken'])->name('asset-assignment-confirmations.decline');
    
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
    
    // Maintenance routes
    Route::prefix('maintenance')->name('maintenance.')->middleware('check.permission:view_maintenance')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create')->middleware('check.permission:create_maintenance');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store')->middleware('check.permission:create_maintenance');
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit')->middleware('check.permission:edit_maintenance');
        Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update')->middleware('check.permission:edit_maintenance');
        Route::delete('/{maintenance}', [MaintenanceController::class, 'destroy'])->name('destroy')->middleware('check.permission:delete_maintenance');
        Route::get('/export/excel', [MaintenanceController::class, 'exportExcel'])->name('export.excel')->middleware('check.permission:export_maintenance');
        Route::get('/export/pdf', [MaintenanceController::class, 'exportPdf'])->name('export.pdf')->middleware('check.permission:export_maintenance');
    });
    
    // Disposal routes
    Route::prefix('disposal')->name('disposal.')->middleware('check.permission:view_disposal')->group(function () {
        Route::get('/', [DisposalController::class, 'index'])->name('index');
        Route::get('/create', [DisposalController::class, 'create'])->name('create')->middleware('check.permission:create_disposal');
        Route::post('/', [DisposalController::class, 'store'])->name('store')->middleware('check.permission:create_disposal');
        Route::get('/{disposal}', [DisposalController::class, 'show'])->name('show');
        Route::get('/{disposal}/edit', [DisposalController::class, 'edit'])->name('edit')->middleware('check.permission:edit_disposal');
        Route::put('/{disposal}', [DisposalController::class, 'update'])->name('update')->middleware('check.permission:edit_disposal');
        Route::delete('/{disposal}', [DisposalController::class, 'destroy'])->name('destroy')->middleware('check.permission:delete_disposal');
        Route::get('/export/excel', [DisposalController::class, 'exportExcel'])->name('export.excel')->middleware('check.permission:export_disposal');
        Route::get('/export/pdf', [DisposalController::class, 'exportPdf'])->name('export.pdf')->middleware('check.permission:export_disposal');
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
