@extends('layouts.app')

@section('title', 'Import/Export Manager')
{{-- Page title removed - using breadcrumbs instead --}}

@section('styles')
<style>
    .step-wizard {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }
    
    .step-wizard::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }
    
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        flex: 1;
        z-index: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        border: 3px solid #e9ecef;
    }
    
    .step-item.active .step-circle {
        background: #007bff;
        color: white;
        border-color: #007bff;
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
    }
    
    .step-item.completed .step-circle {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }
    
    .step-item.error .step-circle {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    
    .step-label {
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        color: #6c757d;
        transition: color 0.3s ease;
    }
    
    .step-item.active .step-label {
        color: #007bff;
        font-weight: 600;
    }
    
    .step-item.completed .step-label {
        color: #28a745;
    }
    
    .step-content {
        display: none;
        animation: fadeIn 0.5s ease-in;
    }
    
    .step-content.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .module-card, .action-card {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }
    
    .module-card:hover, .action-card:hover {
        border-color: #007bff;
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
    }
    
    .module-card.selected, .action-card.selected {
        border-color: #007bff;
        background: #f8f9ff;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    }
    
    .upload-zone {
        border: 3px dashed #dee2e6;
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
        overflow: hidden;
    }
    
    .upload-zone::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(0, 123, 255, 0.05), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }
    
    .upload-zone:hover::before {
        opacity: 1;
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .upload-zone:hover {
        border-color: #007bff;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        transform: scale(1.02);
    }
    
    .upload-zone.dragover {
        border-color: #28a745;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        transform: scale(1.05);
    }
    
    .upload-icon {
        font-size: 48px;
        color: #007bff;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .upload-zone:hover .upload-icon {
        transform: scale(1.1);
        color: #0056b3;
    }
    
    .progress-container {
        display: none;
        margin-top: 30px;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        background: #e9ecef;
    }
    
    .progress-bar {
        transition: width 0.3s ease;
        background: linear-gradient(90deg, #007bff, #0056b3);
    }
    
    .btn-action {
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .btn-action::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
    }
    
    .btn-action:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .feature-highlight {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 20px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-processing {
        background: #cce5ff;
        color: #004085;
    }
    
    .status-completed {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-error {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Card -->
    <div class="summary-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">🚀 Advanced Import/Export Manager</h4>
                <p class="mb-0">Streamlined data management with intelligent validation, bulk operations, and real-time progress tracking.</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end gap-2">
                    <span class="status-badge status-pending">Ready</span>
                    <span class="badge bg-light text-dark">v2.0</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Step 1: Module Selection -->
            <div class="step-content active" id="step-1-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>Step 1: Select Module
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="assets">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-boxes fa-3x text-primary mb-3"></i>
                                        <h6 class="card-title fw-bold">Assets</h6>
                                        <p class="card-text text-muted small mb-3">Manage physical assets and equipment</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="users">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-users fa-3x text-success mb-3"></i>
                                        <h6 class="card-title fw-bold">Users</h6>
                                        <p class="card-text text-muted small mb-3">Manage user accounts and profiles</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="computers">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-desktop fa-3x text-info mb-3"></i>
                                        <h6 class="card-title fw-bold">Computers</h6>
                                        <p class="card-text text-muted small mb-3">Manage computer specifications</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="departments">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-building fa-3x text-warning mb-3"></i>
                                        <h6 class="card-title fw-bold">Departments</h6>
                                        <p class="card-text text-muted small mb-3">Manage organizational departments</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="vendors">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-truck fa-3x text-danger mb-3"></i>
                                        <h6 class="card-title fw-bold">Vendors</h6>
                                        <p class="card-text text-muted small mb-3">Manage vendor and supplier data</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="monitors">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-tv fa-3x text-secondary mb-3"></i>
                                        <h6 class="card-title fw-bold">Monitors</h6>
                                        <p class="card-text text-muted small mb-3">Manage monitor and display assets</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="printers">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-print fa-3x text-dark mb-3"></i>
                                        <h6 class="card-title fw-bold">Printers</h6>
                                        <p class="card-text text-muted small mb-3">Manage printer and scanner assets</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="peripherals">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-mouse fa-3x text-purple mb-3"></i>
                                        <h6 class="card-title fw-bold">Peripherals</h6>
                                        <p class="card-text text-muted small mb-3">Manage peripheral devices and accessories</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="module-card card h-100" data-module="asset_categories">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-tags fa-3x text-info mb-3"></i>
                                        <h6 class="card-title fw-bold">Asset Categories</h6>
                                        <p class="card-text text-muted small mb-3">Manage asset category classifications</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <span class="badge bg-success">Import</span>
                                            <span class="badge bg-info">Export</span>
                                            <span class="badge bg-warning">Template</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button class="btn btn-primary btn-action" id="next-step-1" disabled>
                                Next: Choose Action <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Action Selection -->
            <div class="step-content" id="step-2-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>Step 2: Choose Action
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="action-card card h-100" data-action="template">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-download fa-3x text-success mb-3"></i>
                                        <h6 class="card-title fw-bold">Download Template</h6>
                                        <p class="card-text text-muted small mb-3">Get pre-formatted Excel template with sample data and validation rules</p>
                                        <div class="feature-highlight">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i> Sample data included<br>
                                                <i class="fas fa-check text-success me-1"></i> Validation rules<br>
                                                <i class="fas fa-check text-success me-1"></i> Auto-formatting
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="action-card card h-100" data-action="export">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-file-export fa-3x text-info mb-3"></i>
                                        <h6 class="card-title fw-bold">Export Data</h6>
                                        <p class="card-text text-muted small mb-3">Export existing data with filtering and custom formatting options</p>
                                        <div class="feature-highlight">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i> Advanced filtering<br>
                                                <i class="fas fa-check text-success me-1"></i> Custom columns<br>
                                                <i class="fas fa-check text-success me-1"></i> Multiple formats
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="action-card card h-100" data-action="import">
                                    <div class="card-body text-center p-4">
                                        <i class="fas fa-file-import fa-3x text-warning mb-3"></i>
                                        <h6 class="card-title fw-bold">Import Data</h6>
                                        <p class="card-text text-muted small mb-3">Upload and import data with intelligent validation and error handling</p>
                                        <div class="feature-highlight">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i> Smart validation<br>
                                                <i class="fas fa-check text-success me-1"></i> Error reporting<br>
                                                <i class="fas fa-check text-success me-1"></i> Bulk processing
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-outline-secondary btn-action" id="prev-step-2">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button class="btn btn-success btn-action" id="next-step-2" disabled>
                                Next: Upload/Download <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Upload/Download -->
            <div class="step-content" id="step-3-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Step 3: Upload/Download
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Template Download Section -->
                        <div id="template-section" style="display: none;">
                            <div class="text-center mb-4">
                                <h6 class="text-muted">Download Template for <span id="selected-module-template"></span></h6>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body text-center p-4">
                                            <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                                            <h6>Excel Template</h6>
                                            <p class="text-muted small">Pre-formatted with sample data and validation</p>
                                            <button class="btn btn-success btn-action" id="download-template-btn">
                                                <i class="fas fa-download me-2"></i>Download Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Section -->
                        <div id="export-section" style="display: none;">
                            <div class="text-center mb-4">
                                <h6 class="text-muted">Export <span id="selected-module-export"></span> Data</h6>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-body text-center p-4">
                                            <i class="fas fa-database fa-4x text-info mb-3"></i>
                                            <h6>Export Current Data</h6>
                                            <p class="text-muted small">Export all existing records to Excel</p>
                                            <button class="btn btn-info btn-action" id="export-data-btn">
                                                <i class="fas fa-file-export me-2"></i>Export Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Section -->
                        <div id="import-section" style="display: none;">
                            <div class="text-center mb-4">
                                <h6 class="text-muted">Import <span id="selected-module-import"></span> Data</h6>
                            </div>
                            <form id="import-form" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="import-module" name="module">
                                <div class="upload-zone" id="upload-zone">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h5 class="mb-3">Drop your Excel file here</h5>
                                    <p class="text-muted mb-4">or click to browse and select a file</p>
                                    <input type="file" id="file-input" name="file" accept=".xlsx,.xls,.csv" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary btn-action" id="browse-btn">
                                        <i class="fas fa-folder-open me-2"></i>Browse Files
                                    </button>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Supported formats: Excel (.xlsx, .xls), CSV (.csv)<br>
                                            Maximum file size: 10MB
                                        </small>
                                    </div>
                                </div>
                                <div id="file-info" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-excel fa-2x me-3"></i>
                                            <div class="flex-grow-1">
                                                <strong id="file-name"></strong><br>
                                                <small class="text-muted" id="file-size"></small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="remove-file">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-outline-secondary btn-action" id="prev-step-3">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button class="btn btn-warning btn-action" id="next-step-3" disabled>
                                Next: Validation <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Validation -->
            <div class="step-content" id="step-4-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Step 4: Validation
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="progress-container" id="progress-container">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Processing...</span>
                                <span class="text-muted" id="progress-text">0%</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div id="validation-results" style="display: none;">
                            <div class="alert alert-success" id="success-summary" style="display: none;">
                                <h6><i class="fas fa-check-circle me-2"></i>Validation Successful</h6>
                                <p class="mb-0">All data has been validated successfully and is ready for import.</p>
                            </div>
                            <div class="alert alert-warning" id="warning-summary" style="display: none;">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Validation Warnings</h6>
                                <p class="mb-0">Some issues were found but can be automatically corrected.</p>
                            </div>
                            <div class="alert alert-danger" id="error-summary" style="display: none;">
                                <h6><i class="fas fa-times-circle me-2"></i>Validation Errors</h6>
                                <p class="mb-0">Critical errors found that must be fixed before import.</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-outline-secondary btn-action" id="prev-step-4">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button class="btn btn-warning btn-action" id="next-step-4" disabled>
                                Next: Complete <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Complete -->
            <div class="step-content" id="step-5-content">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-flag-checkered me-2"></i>Step 5: Complete
                        </h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div id="completion-success" style="display: none;">
                            <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                            <h4 class="text-success mb-3">Operation Completed Successfully!</h4>
                            <p class="text-muted mb-4">Your data has been processed successfully.</p>
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <h5 class="text-success mb-1" id="success-count">0</h5>
                                                    <small class="text-muted">Successful</small>
                                                </div>
                                                <div class="col-4">
                                                    <h5 class="text-warning mb-1" id="warning-count">0</h5>
                                                    <small class="text-muted">Warnings</small>
                                                </div>
                                                <div class="col-4">
                                                    <h5 class="text-danger mb-1" id="error-count">0</h5>
                                                    <small class="text-muted">Errors</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary btn-action me-3" id="start-over">
                                <i class="fas fa-redo me-2"></i>Start Over
                            </button>
                            <button class="btn btn-outline-secondary btn-action" id="view-results">
                                <i class="fas fa-eye me-2"></i>View Results
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Quick Guide</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">📋 Step-by-Step Process</h6>
                        <ol class="small text-muted">
                            <li>Choose the data module you want to work with</li>
                            <li>Select whether to import, export, or download template</li>
                            <li>Upload your file or download the template/export</li>
                            <li>Review validation results and fix any errors</li>
                            <li>Complete the process and view results</li>
                        </ol>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-success">✅ Best Practices</h6>
                        <ul class="small text-muted">
                            <li>Always download the template first</li>
                            <li>Follow the exact column format</li>
                            <li>Check for duplicate entries</li>
                            <li>Validate data before importing</li>
                        </ul>
                    </div>
                    <div>
                        <h6 class="text-info">💡 Tips</h6>
                        <ul class="small text-muted">
                            <li>Use Excel for best compatibility</li>
                            <li>Keep file size under 10MB</li>
                            <li>Remove empty rows and columns</li>
                            <li>Use consistent date formats</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <p class="small">No recent activity</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let selectedModule = null;
    let selectedAction = null;
    let currentStep = 1;
    
    // Step navigation
    function goToStep(step) {
        // Hide all step contents
        $('.step-content').removeClass('active');
        $('.step-item').removeClass('active completed error');
        
        // Show current step content
        $(`#step-${step}-content`).addClass('active');
        
        // Update step indicators
        for (let i = 1; i <= 5; i++) {
            if (i < step) {
                $(`.step-item[data-step="${i}"]`).addClass('completed');
            } else if (i === step) {
                $(`.step-item[data-step="${i}"]`).addClass('active');
            }
        }
        
        currentStep = step;
    }
    
    // Module selection
    $('.module-card').click(function() {
        $('.module-card').removeClass('selected');
        $(this).addClass('selected');
        selectedModule = $(this).data('module');
        $('#next-step-1').prop('disabled', false);
    });
    
    // Action selection
    $('.action-card').click(function() {
        $('.action-card').removeClass('selected');
        $(this).addClass('selected');
        selectedAction = $(this).data('action');
        $('#next-step-2').prop('disabled', false);
    });
    
    // Step navigation buttons
    $('#next-step-1').click(function() {
        if (selectedModule) {
            goToStep(2);
        }
    });
    
    $('#prev-step-2, #prev-step-3, #prev-step-4').click(function() {
        const stepNum = parseInt($(this).attr('id').split('-')[2]);
        goToStep(stepNum - 1);
    });
    
    $('#next-step-2').click(function() {
        if (selectedAction) {
            setupStep3();
            goToStep(3);
        }
    });
    
    function setupStep3() {
        // Hide all sections
        $('#template-section, #export-section, #import-section').hide();
        
        // Show relevant section based on action
        if (selectedAction === 'template') {
            $('#template-section').show();
            $('#selected-module-template').text(selectedModule.charAt(0).toUpperCase() + selectedModule.slice(1));
            $('#next-step-3').prop('disabled', false);
        } else if (selectedAction === 'export') {
            $('#export-section').show();
            $('#selected-module-export').text(selectedModule.charAt(0).toUpperCase() + selectedModule.slice(1));
            $('#next-step-3').prop('disabled', false);
        } else if (selectedAction === 'import') {
            $('#import-section').show();
            $('#selected-module-import').text(selectedModule.charAt(0).toUpperCase() + selectedModule.slice(1));
            $('#import-module').val(selectedModule);
        }
    }
    
    // File upload handling
    $('#browse-btn, #upload-zone').click(function() {
        $('#file-input').click();
    });
    
    $('#file-input').change(function() {
        const file = this.files[0];
        if (file) {
            $('#file-name').text(file.name);
            $('#file-size').text((file.size / 1024 / 1024).toFixed(2) + ' MB');
            $('#file-info').show();
            $('#next-step-3').prop('disabled', false);
        }
    });
    
    $('#remove-file').click(function() {
        $('#file-input').val('');
        $('#file-info').hide();
        $('#next-step-3').prop('disabled', true);
    });
    
    // Drag and drop
    $('#upload-zone').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    $('#upload-zone').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    $('#upload-zone').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#file-input')[0].files = files;
            $('#file-input').trigger('change');
        }
    });
    
    // Download template
    $('#download-template-btn').click(function() {
        window.location.href = '/import-export/template/' + selectedModule;
    });
    
    // Export data
    $('#export-data-btn').click(function() {
        window.location.href = '/import-export/export/' + selectedModule;
    });
    
    // Step 3 to 4
    $('#next-step-3').click(function() {
        if (selectedAction === 'import') {
            // Start import process
            performImport();
        } else {
            // For template/export, go directly to completion
            goToStep(5);
            showCompletion();
        }
    });
    
    function performImport() {
        goToStep(4);
        $('#progress-container').show();
        
        const formData = new FormData();
        const fileInput = document.getElementById('file-input');
        
        if (fileInput.files.length === 0) {
            alert('Please select a file to import.');
            return;
        }
        
        formData.append('file', fileInput.files[0]);
        formData.append('module', selectedModule);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: '/import-export/import/' + selectedModule,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = (evt.loaded / evt.total) * 100;
                        $('#progress-bar').css('width', percentComplete + '%');
                        $('#progress-text').text(Math.round(percentComplete) + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                $('#progress-container').hide();
                $('#validation-results').show();
                
                if (response.success) {
                    $('#success-summary').show();
                    $('#success-count').text(response.imported || 0);
                    $('#warning-count').text(response.warnings || 0);
                    $('#error-count').text(response.errors || 0);
                } else {
                    $('#error-summary').show();
                }
                
                $('#next-step-4').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $('#progress-container').hide();
                $('#validation-results').show();
                $('#error-summary').show();
                $('#next-step-4').prop('disabled', false);
                console.error('Import failed:', error);
            }
        });
    }
    
    function startValidation() {
        $('#progress-container').show();
        let progress = 0;
        
        const interval = setInterval(function() {
            progress += Math.random() * 20;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                setTimeout(function() {
                    showValidationResults();
                }, 500);
            }
            
            $('#progress-bar').css('width', progress + '%');
            $('#progress-text').text(Math.round(progress) + '%');
        }, 200);
    }
    
    function showValidationResults() {
        $('#progress-container').hide();
        $('#validation-results').show();
        $('#success-summary').show();
        $('#next-step-4').prop('disabled', false);
    }
    
    $('#next-step-4').click(function() {
        goToStep(5);
        showCompletion();
    });
    
    function showCompletion() {
        $('#completion-success').show();
        $('#success-count').text('150');
        $('#warning-count').text('5');
        $('#error-count').text('0');
    }
    
    $('#start-over').click(function() {
        // Reset everything
        selectedModule = null;
        selectedAction = null;
        $('.module-card, .action-card').removeClass('selected');
        $('#file-input').val('');
        $('#file-info').hide();
        $('.step-content').removeClass('active');
        $('#validation-results, #completion-success').hide();
        goToStep(1);
    });
});
</script>
@endsection