<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SecurityValidationMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
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
use App\Http\Controllers\SecurityAuditController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemHealthController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\SecurityMonitoringController;
use App\Http\Controllers\ChangePasswordController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Public template download routes (no authentication required)
Route::get('/templates/{module}', [App\Http\Controllers\TemplateController::class, 'downloadTemplate'])
    ->where('module', 'assets|users|computers|departments|vendors|monitors|printers|peripherals|asset_categories')
    ->name('templates.download');

// Public validation endpoint (no authentication required)
Route::post('/validate-csv', [App\Http\Controllers\ValidationController::class, 'validateCsv'])
    ->name('validate.csv');

// Legacy template route for backward compatibility
Route::get('/import-export/template/{module}', [App\Http\Controllers\TemplateController::class, 'downloadTemplate'])
    ->where('module', 'assets|users|computers|departments|vendors|monitors|printers|peripherals|asset_categories')
    ->name('import-export.template');

// Public token-based confirmation routes (no auth required)
Route::get('asset-assignment-confirmations/confirm/{token}', [AssetAssignmentConfirmationController::class, 'confirmByToken'])->name('asset-assignment-confirmations.confirm');
Route::get('asset-assignment-confirmations/decline/{token}', [AssetAssignmentConfirmationController::class, 'declineByToken'])->name('asset-assignment-confirmations.decline');

// Protected routes
Route::middleware('auth')->group(function () {
    // Asset management
    Route::resource('assets', AssetController::class)->middleware('prevent.maintenance.edit');
    Route::post('assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign')->middleware('prevent.maintenance.edit');
    Route::post('assets/{asset}/unassign', [AssetController::class, 'unassign'])->name('assets.unassign')->middleware('prevent.maintenance.edit');
    Route::post('assets/{asset}/reassign', [AssetController::class, 'reassign'])->name('assets.reassign')->middleware('prevent.maintenance.edit');
    Route::get('assets/reports/employee-assets', [AssetController::class, 'printEmployeeAssets'])->name('assets.print-employee-assets');
    Route::get('assets/reports/employee-assets/{user}', [AssetController::class, 'printSingleEmployeeAssets'])->name('assets.print-single-employee-assets');
    Route::post('assets/bulk/print-labels', [AssetController::class, 'bulkPrintLabels'])->name('assets.bulk-print-labels');
    Route::get('assets/print-all-labels', [AssetController::class, 'printAllAssetLabels'])->name('assets.print-all-labels');
    Route::post('assets/generate-tag', [AssetController::class, 'generateUniqueTag'])->name('assets.generate-tag');
    Route::post('assets/check-tag-uniqueness', [AssetController::class, 'checkAssetTagUniqueness'])->name('assets.check-tag-uniqueness');
    Route::get('assets/{asset}/vendor', [AssetController::class, 'getAssetVendor'])->name('assets.get-vendor');
    
    // Asset Categories
    Route::resource('asset-categories', AssetCategoryController::class);
    Route::get('asset-categories/export', [AssetCategoryController::class, 'export'])->name('asset-categories.export');
    Route::get('asset-categories/export/excel', [AssetCategoryController::class, 'exportExcel'])->name('asset-categories.export.excel');
    Route::get('asset-categories/download/template', [AssetCategoryController::class, 'downloadTemplate'])->name('asset-categories.download-template');
    Route::get('asset-categories/import/form', [AssetCategoryController::class, 'importForm'])->name('asset-categories.import-form');
    Route::post('asset-categories/import', [AssetCategoryController::class, 'import'])->name('asset-categories.import');
    
    // Asset Assignments
    Route::resource('asset-assignments', AssetAssignmentController::class)->middleware('prevent.maintenance.edit');
    Route::get('asset-assignments/export', [AssetAssignmentController::class, 'export'])->name('asset-assignments.export');
    Route::get('asset-assignments/export/excel', [AssetAssignmentController::class, 'exportExcel'])->name('asset-assignments.export.excel');
    Route::get('asset-assignments/download/template', [AssetAssignmentController::class, 'downloadTemplate'])->name('asset-assignments.download-template');
    Route::get('asset-assignments/import/form', [AssetAssignmentController::class, 'importForm'])->name('asset-assignments.import-form');
    Route::post('asset-assignments/import', [AssetAssignmentController::class, 'import'])->name('asset-assignments.import');
    Route::post('asset-assignments/{assignment}/return', [AssetAssignmentController::class, 'markAsReturned'])->name('asset-assignments.return')->middleware('prevent.maintenance.edit');
    
    // Asset Assignment Confirmations
    Route::resource('asset-assignment-confirmations', AssetAssignmentConfirmationController::class)->middleware('check.permission:view_assignment_confirmations');
    Route::get('asset-assignment-confirmations/export', [AssetAssignmentConfirmationController::class, 'export'])->name('asset-assignment-confirmations.export')->middleware('check.permission:manage_assignment_confirmations');
    Route::get('asset-assignment-confirmations/export/excel', [AssetAssignmentConfirmationController::class, 'exportExcel'])->name('asset-assignment-confirmations.export.excel')->middleware('check.permission:manage_assignment_confirmations');
    Route::get('asset-assignment-confirmations/download/template', [AssetAssignmentConfirmationController::class, 'downloadTemplate'])->name('asset-assignment-confirmations.download-template')->middleware('check.permission:manage_assignment_confirmations');
    Route::get('asset-assignment-confirmations/import/form', [AssetAssignmentConfirmationController::class, 'importForm'])->name('asset-assignment-confirmations.import-form')->middleware('check.permission:manage_assignment_confirmations');
    Route::post('asset-assignment-confirmations/import', [AssetAssignmentConfirmationController::class, 'import'])->name('asset-assignment-confirmations.import')->middleware('check.permission:manage_assignment_confirmations');
    Route::get('asset-assignment-confirmations/{assetAssignmentConfirmation}/send-reminder', [AssetAssignmentConfirmationController::class, 'sendReminder'])->name('asset-assignment-confirmations.send-reminder')->middleware('check.permission:manage_assignment_confirmations');
    Route::post('asset-assignment-confirmations/send-bulk-reminders', [AssetAssignmentConfirmationController::class, 'sendBulkReminders'])->name('asset-assignment-confirmations.send-bulk-reminders')->middleware('check.permission:manage_assignment_confirmations');
    
    Route::resource('computers', ComputerController::class)->middleware('check.permission:view_computers');
    Route::get('computers/bulk/create', [ComputerController::class, 'bulkCreate'])->name('computers.bulk-create')->middleware('check.permission:view_computers');
    Route::post('computers/bulk/store', [ComputerController::class, 'bulkStore'])->name('computers.bulk-store')->middleware('check.permission:view_computers');
    Route::resource('monitors', MonitorController::class)->middleware('check.permission:view_monitors');
    Route::get('monitors/bulk/create', [MonitorController::class, 'bulkCreate'])->name('monitors.bulk-create')->middleware('check.permission:view_monitors');
    Route::post('monitors/bulk/store', [MonitorController::class, 'bulkStore'])->name('monitors.bulk-store')->middleware('check.permission:view_monitors');
    
    Route::resource('printers', PrinterController::class)->middleware('check.permission:view_printers');
    Route::get('printers/bulk/create', [PrinterController::class, 'bulkCreate'])->name('printers.bulk-create')->middleware('check.permission:view_printers');
    Route::post('printers/bulk/store', [PrinterController::class, 'bulkStore'])->name('printers.bulk-store')->middleware('check.permission:view_printers');
    
    Route::resource('peripherals', PeripheralController::class)->middleware('check.permission:view_peripherals');
    Route::get('peripherals/bulk/create', [PeripheralController::class, 'bulkCreate'])->name('peripherals.bulk-create')->middleware('check.permission:view_peripherals');
    Route::post('peripherals/bulk/store', [PeripheralController::class, 'bulkStore'])->name('peripherals.bulk-store')->middleware('check.permission:view_peripherals');
    
    // User and organization management
    Route::resource('users', UserController::class)->middleware('check.permission:view_users');
    Route::resource('departments', DepartmentController::class)->middleware('check.permission:view_departments');
    Route::resource('vendors', VendorController::class)->middleware('check.permission:view_vendors');
    
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
    Route::resource('roles', RoleController::class)->middleware('check.permission:view_roles');
    Route::resource('permissions', PermissionController::class)->middleware('check.permission:view_permissions');
    
    // Dashboard route
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/asset-movements', [App\Http\Controllers\DashboardController::class, 'assetMovements'])->name('dashboard.asset-movements');
    
    // Import/Export routes - Enhanced with comprehensive security
    require __DIR__.'/import-export.php';
    
    // Activity Logs routes
    Route::prefix('logs')->name('logs.')->middleware('check.permission:view_logs')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/{log}', [LogController::class, 'show'])->name('show');
        Route::get('/export', [LogController::class, 'export'])->name('export');
        Route::post('/clear', [LogController::class, 'clearOldLogs'])->name('clear');
    });
    
    // Asset Timeline routes
    Route::prefix('timeline')->name('timeline.')->middleware('check.permission:view_timeline')->group(function () {
        Route::get('/', [AssetTimelineController::class, 'index'])->name('index');
        Route::get('/create', [AssetTimelineController::class, 'create'])->name('create')->middleware('check.permission:create_timeline');
        Route::post('/', [AssetTimelineController::class, 'store'])->name('store')->middleware('check.permission:create_timeline');
        Route::get('/asset/{asset}', [AssetTimelineController::class, 'show'])->name('show');
    });
    
    // Notification routes
    Route::prefix('notifications')->name('notifications.')->middleware('check.permission:view_notifications')->group(function () {
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/all', [NotificationController::class, 'getAll'])->name('all');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete')->middleware('check.permission:manage_notifications');
    });
    
    // Maintenance routes
    Route::prefix('maintenance')->name('maintenance.')->middleware('check.permission:view_maintenance')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create')->middleware('check.permission:create_maintenance');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store')->middleware('check.permission:create_maintenance');
        Route::get('/bulk/create', [MaintenanceController::class, 'bulkCreate'])->name('bulk-create')->middleware('check.permission:create_maintenance');
        Route::post('/bulk/store', [MaintenanceController::class, 'bulkStore'])->name('bulk-store')->middleware('check.permission:create_maintenance');
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
        Route::get('/bulk/create', [DisposalController::class, 'bulkCreate'])->name('bulk-create')->middleware('check.permission:create_disposal');
        Route::post('/bulk/store', [DisposalController::class, 'bulkStore'])->name('bulk-store')->middleware('check.permission:create_disposal');
        Route::get('/{disposal}', [DisposalController::class, 'show'])->name('show');
        Route::get('/{disposal}/edit', [DisposalController::class, 'edit'])->name('edit')->middleware('check.permission:edit_disposal');
        Route::put('/{disposal}', [DisposalController::class, 'update'])->name('update')->middleware('check.permission:edit_disposal');
        Route::delete('/{disposal}', [DisposalController::class, 'destroy'])->name('destroy')->middleware('check.permission:delete_disposal');
        Route::get('/export/excel', [DisposalController::class, 'exportExcel'])->name('export.excel')->middleware('check.permission:export_disposal');
        Route::get('/export/pdf', [DisposalController::class, 'exportPdf'])->name('export.pdf')->middleware('check.permission:export_disposal');
    });

    // Change Password
    Route::get('/password/change', [ChangePasswordController::class, 'edit'])->name('password.edit');
    Route::post('/password/change', [ChangePasswordController::class, 'update'])->name('password.change');
});

// Accountability Form routes
Route::prefix('accountability')->name('accountability.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\AccountabilityFormController::class, 'index'])->name('index')->middleware('check.permission:view_accountability_forms');
    Route::get('/generate/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'generate'])->name('generate')->middleware('check.permission:generate_accountability_forms');
    Route::get('/print/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'print'])->name('print')->middleware('check.permission:print_accountability_forms');
    Route::post('/mark-printed/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'markAsPrinted'])->name('mark-printed')->middleware('check.permission:print_accountability_forms');
    Route::post('/generate-bulk', [App\Http\Controllers\AccountabilityFormController::class, 'generateBulk'])->name('generate-bulk')->middleware('check.permission:bulk_accountability_forms');
    
        // Signed form functionality
        Route::post('/upload-signed-form/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'uploadSignedForm'])->name('upload-signed-form')->middleware('check.permission:print_accountability_forms');
        Route::post('/send-signed-form-email/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'sendSignedFormEmail'])->name('send-signed-form-email')->middleware('check.permission:print_accountability_forms');
        Route::get('/preview-signed-form/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'previewSignedForm'])->name('preview-signed-form')->middleware('check.permission:print_accountability_forms');
        Route::get('/download-signed-form/{asset}', [App\Http\Controllers\AccountabilityFormController::class, 'downloadSignedForm'])->name('download-signed-form')->middleware('check.permission:print_accountability_forms');
        
        // Bulk operations
        Route::get('/bulk-upload', [App\Http\Controllers\AccountabilityFormController::class, 'showBulkUpload'])->name('bulk-upload')->middleware('check.permission:print_accountability_forms');
        Route::post('/bulk-upload', [App\Http\Controllers\AccountabilityFormController::class, 'bulkUpload'])->name('bulk-upload.store')->middleware('check.permission:print_accountability_forms');
        Route::get('/bulk-email', [App\Http\Controllers\AccountabilityFormController::class, 'showBulkEmail'])->name('bulk-email')->middleware('check.permission:print_accountability_forms');
        Route::post('/bulk-email', [App\Http\Controllers\AccountabilityFormController::class, 'bulkSendEmail'])->name('bulk-email.send')->middleware('check.permission:print_accountability_forms');
        
        // Partial upload functionality
        Route::post('/start-session', [App\Http\Controllers\AccountabilityFormController::class, 'startBulkUploadSession'])->name('start-session')->middleware('check.permission:print_accountability_forms');
        Route::post('/upload-to-session/{sessionId}', [App\Http\Controllers\AccountabilityFormController::class, 'uploadToSession'])->name('upload-to-session')->middleware('check.permission:print_accountability_forms');
        Route::get('/session-status/{sessionId}', [App\Http\Controllers\AccountabilityFormController::class, 'getSessionStatus'])->name('session-status')->middleware('check.permission:print_accountability_forms');
        Route::get('/resume-upload/{sessionId}', [App\Http\Controllers\AccountabilityFormController::class, 'resumeBulkUpload'])->name('resume-upload')->middleware('check.permission:print_accountability_forms');
});

// Asset Confirmation routes (public - no auth required)
Route::prefix('asset-confirmation')->name('asset-confirmation.')->group(function () {
    Route::get('/show/{token}', [AssetConfirmationController::class, 'show'])->name('show');
    Route::get('/confirm/{token}', [AssetConfirmationController::class, 'confirm'])->name('confirm');
    Route::get('/decline/{token}', [AssetConfirmationController::class, 'decline'])->name('decline');
    Route::get('/decline-form/{token}', [AssetConfirmationController::class, 'showDeclineForm'])->name('decline-form');
    Route::post('/decline/{token}', [AssetConfirmationController::class, 'processDecline'])->name('process-decline');
});

// Security Audit routes
Route::prefix('security')->name('security.')->middleware(['auth', 'check.permission:view_security_audit'])->group(function () {
    Route::get('/audit', [SecurityAuditController::class, 'index'])->name('audit.index');
    Route::get('/audit/logs', [SecurityAuditController::class, 'logs'])->name('audit.logs');
    Route::get('/audit/user-activity/{userId}', [SecurityAuditController::class, 'userActivity'])->name('audit.user-activity');
    Route::get('/audit/security-events', [SecurityAuditController::class, 'securityEvents'])->name('audit.security-events');
    Route::get('/audit/export', [SecurityAuditController::class, 'export'])->name('audit.export');
    Route::get('/audit/model-trail/{modelType}/{modelId}', [SecurityAuditController::class, 'modelAuditTrail'])->name('audit.model-trail');
    Route::post('/audit/clear-old-logs', [SecurityAuditController::class, 'clearOldLogs'])->name('audit.clear-old-logs');
});

// Reports Routes
Route::prefix('reports')->name('reports.')->middleware(['auth', 'check.permission:view_reports'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/asset-analytics', [ReportController::class, 'assetAnalytics'])->name('asset-analytics');
    Route::get('/user-activity', [ReportController::class, 'userActivity'])->name('user-activity');
    Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    Route::get('/maintenance', [ReportController::class, 'maintenance'])->name('maintenance');
    Route::get('/security', [SecurityAuditController::class, 'index'])->name('security');
    Route::post('/export', [ReportController::class, 'export'])->name('export');
    });

// System Health Routes
Route::prefix('system')->name('system.')->middleware(['auth', 'check.permission:view_system_health'])->group(function () {
    Route::get('/health', [SystemHealthController::class, 'index'])->name('health');
    Route::get('/health/metrics', [SystemHealthController::class, 'metrics'])->name('health.metrics');
    Route::post('/health/clear-cache', [SystemHealthController::class, 'clearCache'])->name('health.clear-cache');
    Route::post('/health/warm-cache', [SystemHealthController::class, 'warmUpCache'])->name('health.warm-cache');
});

// Health Check Routes (for load balancers)
Route::prefix('health')->group(function () {
    Route::get('/', [HealthCheckController::class, 'index'])->name('health.basic');
    Route::get('/detailed', [HealthCheckController::class, 'detailed'])->name('health.detailed');
    Route::get('/readiness', [HealthCheckController::class, 'readiness'])->name('health.readiness');
    Route::get('/liveness', [HealthCheckController::class, 'liveness'])->name('health.liveness');
    Route::get('/metrics', [HealthCheckController::class, 'metrics'])->name('health.metrics');
});

// Security Monitoring Routes
Route::prefix('security/monitoring')->name('security.monitoring.')->middleware(['auth', 'check.permission:view_security_monitoring'])->group(function () {
    Route::get('/', [SecurityMonitoringController::class, 'index'])->name('index');
    Route::get('/threats', [SecurityMonitoringController::class, 'threats'])->name('threats');
    Route::get('/events', [SecurityMonitoringController::class, 'events'])->name('events');
    Route::get('/statistics', [SecurityMonitoringController::class, 'statistics'])->name('statistics');
    Route::get('/report', [SecurityMonitoringController::class, 'report'])->name('report');
    Route::post('/run', [SecurityMonitoringController::class, 'runMonitoring'])->name('run');
    Route::post('/clear-blocks', [SecurityMonitoringController::class, 'clearBlocks'])->name('clear-blocks');
    Route::get('/check-ip', [SecurityMonitoringController::class, 'checkIP'])->name('check-ip');
    Route::get('/check-user', [SecurityMonitoringController::class, 'checkUser'])->name('check-user');
});
