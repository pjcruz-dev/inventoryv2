<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportExportController;

/*
|--------------------------------------------------------------------------
| Import/Export Routes
|--------------------------------------------------------------------------
|
| These routes handle all import/export functionality with comprehensive
| security measures including authentication, authorization, CSRF protection,
| and rate limiting.
|
*/

// Group all import/export routes with common middleware
Route::middleware([
    'auth',
    'verified',
    'validate.csrf',
    'throttle:import-export'
])->prefix('import-export')->name('import-export.')->group(function () {
    
    // Enhanced Interface Route
    Route::get('/interface', [ImportExportController::class, 'interface'])
        ->middleware('check.permission:import_export_access')
        ->name('interface');
    Route::get('/enhanced-interface', [ImportExportController::class, 'enhancedInterface'])
        ->middleware('check.permission:import_export_access')
        ->name('enhanced-interface');
    
    // Template Download Routes
    Route::get('/template/{module}', [ImportExportController::class, 'downloadTemplate'])
        ->middleware([
            'check.permission:template_download',
            'throttle:template-download'
        ])
        ->where('module', 'assets|users|computers|departments|vendors|monitors')
        ->name('template');
        
    // Legacy route name for backward compatibility
    Route::get('/template-download/{module}', [ImportExportController::class, 'downloadTemplate'])
        ->middleware([
            'check.permission:template_download',
            'throttle:template-download'
        ])
        ->where('module', 'assets|users|computers|departments|vendors|monitors')
        ->name('template.download');
    
    // Template Preview API
    Route::get('/api/template-preview/{module}', [ImportExportController::class, 'getTemplatePreview'])
        ->middleware('check.permission:template_preview')
        ->where('module', 'assets|users|computers|departments|vendors|monitors')
        ->name('template.preview');
    
    // Export Routes
    Route::get('/export/{module}', [ImportExportController::class, 'export'])
        ->middleware([
            'check.permission:data_export',
            'throttle:data-export'
        ])
        ->where('module', 'assets|users|computers|departments|vendors|monitors')
        ->name('export');
    
    // Bulk Export Route (multiple modules)
    Route::post('/export/bulk', [ImportExportController::class, 'bulkExport'])
        ->middleware([
            'check.permission:bulk_export',
            'throttle:bulk-export'
        ])
        ->name('export.bulk');
    
    // Import Routes
    Route::post('/import/{module}', [ImportExportController::class, 'import'])
        ->middleware([
            'check.permission:data_import',
            'throttle:data-import',
            'validate.file.upload:10240' // 10MB limit
        ])
        ->where('module', 'assets|users|computers|departments|vendors|monitors')
        ->name('import');
    
    // Import Validation Only Route
    Route::post('/validate/{module}', [ImportExportController::class, 'validateImport'])
        ->middleware([
            'check.permission:import_validation',
            'throttle:validation'
        ])
        ->where('module', 'assets|users|computers|departments|vendors|monitors')
        ->name('import.validate');
    
    // Batch Import Status Route
    Route::get('/import/status/{batchId}', [ImportExportController::class, 'getImportStatus'])
        ->middleware('check.permission:import_status')
        ->name('import.status');
    
    // Import Results Route
    Route::get('/results', [ImportExportController::class, 'showResults'])
        ->middleware('check.permission:import_export_access')
        ->name('results');

    // Import History Routes
    Route::get('/history', [ImportExportController::class, 'getImportHistory'])
        ->middleware('check.permission:import_history')
        ->name('history');

    Route::get('/history/{id}', [ImportExportController::class, 'getImportDetails'])
        ->middleware('check.permission:import_details')
        ->name('history.details');
    
    // Error Report Routes
    Route::get('/errors/{importId}', [ImportExportController::class, 'getErrorReport'])
        ->middleware('check.permission:error_reports')
        ->name('errors.report');
    
    Route::get('/errors/{importId}/download', [ImportExportController::class, 'downloadErrorReport'])
        ->middleware([
            'check.permission:error_download',
            'throttle:error-download'
        ])
        ->name('errors.download');
});

// API Routes for AJAX calls (separate throttling)
Route::middleware([
    'auth',
    'api',
    'throttle:api'
])->prefix('api')->name('api.')->group(function () {
    
    // Serial Number Management API
    Route::post('/serial-suggestions', [ImportExportController::class, 'generateSerialSuggestions'])
        ->middleware('check.permission:serial_generation')
        ->name('serial.suggestions');
    
    Route::post('/serial-validate', [ImportExportController::class, 'validateSerialNumbers'])
        ->middleware('check.permission:serial_validation')
        ->name('serial.validate');
    
    Route::get('/serial-stats/{module}', [ImportExportController::class, 'getSerialStats'])
        ->middleware('check.permission:serial_stats')
        ->name('serial.stats');
    
    // Field Validation API
    Route::post('/validate-field', [ImportExportController::class, 'validateField'])
        ->middleware('check.permission:field_validation')
        ->name('field.validate');
    
    // Data Lookup API
    Route::get('/lookup/{type}', [ImportExportController::class, 'dataLookup'])
        ->middleware('check.permission:data_lookup')
        ->where('type', 'categories|vendors|departments|locations|users')
        ->name('data.lookup');
    
    // Template Field Mapping API
    Route::get('/field-mapping/{module}', [ImportExportController::class, 'getFieldMapping'])
        ->middleware('check.permission:field_mapping')
        ->name('field.mapping');
    
    // Import Progress API
    Route::get('/import-progress/{sessionId}', [ImportExportController::class, 'getImportProgress'])
        ->middleware('check.permission:import_progress')
        ->name('import.progress');
});

// Admin-only routes for system management
Route::middleware([
    'auth',
    'check.permission:admin_access',
    'throttle:admin'
])->prefix('admin/import-export')->name('admin.import-export.')->group(function () {
    
    // System Configuration
    Route::get('/config', [ImportExportController::class, 'getSystemConfig'])
        ->name('config');
    
    Route::post('/config', [ImportExportController::class, 'updateSystemConfig'])
        ->name('config.update');
    
    // Audit Trail Management
    Route::get('/audit', [ImportExportController::class, 'getAuditTrail'])
        ->name('audit');
    
    Route::delete('/audit/{id}', [ImportExportController::class, 'deleteAuditRecord'])
        ->name('audit.delete');
    
    // System Cleanup
    Route::post('/cleanup/temp-files', [ImportExportController::class, 'cleanupTempFiles'])
        ->name('cleanup.temp');
    
    Route::post('/cleanup/old-imports', [ImportExportController::class, 'cleanupOldImports'])
        ->name('cleanup.imports');
    
    // Performance Monitoring
    Route::get('/performance', [ImportExportController::class, 'getPerformanceMetrics'])
        ->name('performance');
    
    // Bulk Operations
    Route::post('/bulk/rollback', [ImportExportController::class, 'bulkRollback'])
        ->name('bulk.rollback');
    
    Route::post('/bulk/retry', [ImportExportController::class, 'bulkRetry'])
        ->name('bulk.retry');
});

// Webhook routes for external integrations (if needed)
Route::middleware([
    'throttle:webhook',
    'validate.webhook.signature'
])->prefix('webhooks/import-export')->name('webhooks.import-export.')->group(function () {
    
    Route::post('/completion', [ImportExportController::class, 'handleImportCompletion'])
        ->name('completion');
    
    Route::post('/error', [ImportExportController::class, 'handleImportError'])
        ->name('error');
});

// Public routes (no authentication required)
Route::prefix('public/import-export')->name('public.import-export.')->group(function () {
    
    // Public template downloads (if enabled in config)
    Route::get('/template/{module}/public', [ImportExportController::class, 'downloadPublicTemplate'])
        ->middleware('throttle:public-template')
        ->where('module', 'assets|users|computers|departments|vendors')
        ->name('template.public');
    
    // System status endpoint
    Route::get('/status', [ImportExportController::class, 'getSystemStatus'])
        ->middleware('throttle:status')
        ->name('status');
});