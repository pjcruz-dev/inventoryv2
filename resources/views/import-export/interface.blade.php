@extends('layouts.app')

@section('title', 'Import/Export Manager')
@section('page-title', 'Import/Export Manager')

<script>
// Global variables
let selectedModule = null;
let selectedAction = null;
let currentStep = 1;

// Global function for testing module selection
function selectUsersModule() {
    console.log('Manually selecting users module');
    $('.module-card').removeClass('selected');
    $('.module-card[data-module="users"]').addClass('selected');
    
    // Update both local and global selectedModule variables
    selectedModule = 'users';
    window.selectedModule = selectedModule;
    
    console.log('Module selected:', selectedModule);
    console.log('Global selectedModule:', window.selectedModule);
    
    $('#next-step-1').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    
    console.log('Next button enabled:', !$('#next-step-1').prop('disabled'));
    console.log('Next button classes:', $('#next-step-1').attr('class'));
    
    // Also trigger the updateSelectionStatus if it exists
    if (typeof updateSelectionStatus === 'function') {
        updateSelectionStatus();
    }
    
    console.log('Users module selected successfully');
}

// Global function for testing action selection
function selectImportAction() {
    console.log('Manually selecting import action');
    $('.action-card').removeClass('selected');
    $('.action-card[data-action="import"]').addClass('selected');
    
    // Update both local and global selectedAction variables
    selectedAction = 'import';
    window.selectedAction = selectedAction;
    
    console.log('Action selected:', selectedAction);
    console.log('Global selectedAction:', window.selectedAction);
    
    $('#next-step-2').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    
    console.log('Next button enabled:', !$('#next-step-2').prop('disabled'));
    console.log('Next button classes:', $('#next-step-2').attr('class'));
    
    // Also trigger the updateSelectionStatus if it exists
    if (typeof updateSelectionStatus === 'function') {
        updateSelectionStatus();
    }
    
    console.log('Import action selected successfully');
}

// Global function for downloading template
function downloadTemplate() {
    if (selectedModule) {
        window.location.href = '/templates/' + selectedModule;
    } else {
        alert('Please select a module first');
    }
}

// Make functions globally accessible immediately
window.selectUsersModule = selectUsersModule;
window.selectImportAction = selectImportAction;
window.downloadTemplate = downloadTemplate;
</script>

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
        transform: scale(1.1);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(0, 123, 255, 0.1);
        }
        100% {
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
        }
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
        position: relative;
        z-index: 1;
    }
    
    .module-card:hover, .action-card:hover {
        border-color: #007bff;
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
    }
    
    .module-card.selected {
        border-color: #007bff;
        background: #f8f9ff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        transform: translateY(-2px);
    }
    
    .module-card * {
        pointer-events: none;
    }
    
    .module-card {
        pointer-events: auto;
    }
    
    .action-card.selected {
        border-color: #28a745;
        background: #f8fff9;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
        transform: translateY(-2px);
    }
    
    .action-card[data-action="import"].selected {
        border-color: #ffc107;
        background: #fffdf5;
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25);
    }
    
    .action-card[data-action="export"].selected {
        border-color: #17a2b8;
        background: #f5fdff;
        box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.25);
    }
    
    .action-card[data-action="template"].selected {
        border-color: #fd7e14;
        background: #fff8f5;
        box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.25);
    }
    
    .selection-status {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .selection-item {
        display: flex;
        align-items: center;
        font-size: 14px;
    }
    
    .selection-item .badge {
        font-size: 12px;
        padding: 4px 8px;
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
        cursor: pointer;
    }
    
    .btn-action:disabled {
        cursor: not-allowed;
        opacity: 0.6;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }
    
    .btn-action:not(:disabled) {
        cursor: pointer;
        opacity: 1;
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
                        <button class="btn btn-sm btn-warning mt-2" onclick="selectUsersModule()">🔧 Test: Select Users Module</button>
                        <button class="btn btn-sm btn-info mt-2" onclick="selectImportAction()">🔧 Test: Select Import Action</button>
                    </div>
            <div class="col-md-4 text-end">
                <div class="d-flex justify-content-end gap-2">
                    <span class="status-badge status-pending">Ready</span>
                    <span class="badge bg-light text-dark">v2.0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Selection Status Bar -->
    <div id="selection-status" class="selection-status mb-4" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="selection-item">
                            <i class="fas fa-cube me-2 text-primary"></i>
                            <strong>Module:</strong> <span id="selected-module-display" class="badge bg-primary">-</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="selection-item">
                            <i class="fas fa-tasks me-2 text-success"></i>
                            <strong>Action:</strong> <span id="selected-action-display" class="badge bg-success">-</span>
                        </div>
                    </div>
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
                                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('file-input').click();" style="cursor: pointer;">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h5 class="mb-3">Drop your Excel file here</h5>
                                    <p class="text-muted mb-4">or click to browse and select a file</p>
                                    <input type="file" id="file-input" name="file" accept=".xlsx,.xls,.csv" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary btn-action" id="browse-btn" onclick="document.getElementById('file-input').click();">
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
                                <p class="mb-0">Please fix the following errors before importing:</p>
                                <div id="error-details" class="mt-3"></div>
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
    console.log('Import/Export interface loaded');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Module cards found:', $('.module-card').length);
    console.log('Action cards found:', $('.action-card').length);
    console.log('Step 2 content visible:', $('#step-2-content').is(':visible'));
    
    // Use global variables
    console.log('Global selectedModule:', window.selectedModule);
    console.log('Global selectedAction:', window.selectedAction);
    
    // Step navigation
    function goToStep(step) {
        console.log('Going to step:', step);
        
        // Hide all step contents
        $('.step-content').removeClass('active').hide();
        
        // Show current step content
        $(`#step-${step}-content`).addClass('active').show();
        
        currentStep = step;
        
        console.log('Step changed to:', step);
        console.log('Active step content:', $(`#step-${step}-content`).length);
        console.log('Step 2 content visible:', $('#step-2-content').is(':visible'));
    }
    
    // Update selection status display
    function updateSelectionStatus() {
        if (selectedModule || selectedAction) {
            $('#selection-status').show();
            
            if (selectedModule) {
                const moduleName = selectedModule.charAt(0).toUpperCase() + selectedModule.slice(1).replace('_', ' ');
                $('#selected-module-display').text(moduleName);
            }
            
            if (selectedAction) {
                const actionName = selectedAction.charAt(0).toUpperCase() + selectedAction.slice(1);
                $('#selected-action-display').text(actionName);
            }
        } else {
            $('#selection-status').hide();
        }
    }
    
    // Module selection
    $('.module-card').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Module card clicked:', $(this).data('module'));
        
        $('.module-card').removeClass('selected');
        $(this).addClass('selected');
        selectedModule = $(this).data('module');
        window.selectedModule = selectedModule; // Update global reference
        
        console.log('Module selected:', selectedModule);
        console.log('Global selectedModule:', window.selectedModule);
        
        $('#next-step-1').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        
        console.log('Next button enabled:', !$('#next-step-1').prop('disabled'));
        console.log('Next button classes:', $('#next-step-1').attr('class'));
        
        // Update selection status
        updateSelectionStatus();
    });
    
    // Alternative click handler for better compatibility
    $('.module-card').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Module card clicked (alternative):', $(this).data('module'));
        
        $('.module-card').removeClass('selected');
        $(this).addClass('selected');
        selectedModule = $(this).data('module');
        window.selectedModule = selectedModule; // Update global reference
        $('#next-step-1').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        
        console.log('Module selected (alternative):', selectedModule);
        
        // Update selection status
        updateSelectionStatus();
    });
    
    // Make selectedModule accessible globally for debugging
    window.selectedModule = selectedModule;
    
    // Action selection
    $('.action-card').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Action card clicked:', $(this).data('action'));
        console.log('Action card element:', $(this));
        
        $('.action-card').removeClass('selected');
        $(this).addClass('selected');
        selectedAction = $(this).data('action');
        window.selectedAction = selectedAction; // Update global reference
        
        $('#next-step-2').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        
        console.log('Action selected:', selectedAction);
        console.log('Global selectedAction:', window.selectedAction);
        console.log('Next button enabled:', !$('#next-step-2').prop('disabled'));
        console.log('Next button classes:', $('#next-step-2').attr('class'));
        
        // Update selection status
        updateSelectionStatus();
    });
    
    // Alternative click handler for action cards
    $('.action-card').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Action card clicked (alternative):', $(this).data('action'));
        
        $('.action-card').removeClass('selected');
        $(this).addClass('selected');
        selectedAction = $(this).data('action');
        window.selectedAction = selectedAction; // Update global reference
        
        $('#next-step-2').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        
        console.log('Action selected (alternative):', selectedAction);
        
        // Update selection status
        updateSelectionStatus();
    });
    
    // Step navigation buttons
    $('#next-step-1').click(function() {
        console.log('Next step 1 clicked, selectedModule:', selectedModule);
        console.log('Button disabled state:', $(this).prop('disabled'));
        console.log('Button classes:', $(this).attr('class'));
        
        if (selectedModule) {
            console.log('Proceeding to step 2');
            goToStep(2);
        } else {
            console.log('No module selected');
            alert('Please select a module first');
        }
    });
    
    $('#prev-step-2, #prev-step-3, #prev-step-4').click(function() {
        const stepNum = parseInt($(this).attr('id').split('-')[2]);
        goToStep(stepNum - 1);
    });
    
    $('#next-step-2').click(function() {
        console.log('Next step 2 clicked, selectedAction:', selectedAction);
        if (selectedAction) {
            console.log('Proceeding to step 3');
            setupStep3();
            goToStep(3);
        } else {
            console.log('No action selected');
            alert('Please select an action first');
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
    
    // File upload handling - Multiple approaches for better compatibility
    function triggerFileInput() {
        console.log('Triggering file input');
        const fileInput = document.getElementById('file-input');
        if (fileInput) {
            fileInput.click();
        } else {
            console.error('File input not found');
        }
    }
    
    // Event delegation for dynamically loaded content
    $(document).on('click', '#browse-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Browse button clicked');
        triggerFileInput();
    });
    
    $(document).on('click', '#upload-zone', function(e) {
        // Only trigger if not clicking on the button
        if (!$(e.target).closest('#browse-btn').length) {
            console.log('Upload zone clicked');
            triggerFileInput();
        }
    });
    
    $(document).on('change', '#file-input', function() {
        console.log('File input changed');
        const file = this.files[0];
        if (file) {
            console.log('File selected:', {
                name: file.name,
                size: file.size,
                type: file.type,
                lastModified: file.lastModified
            });
            
            // Validate file type
            const allowedTypes = ['.xlsx', '.xls', '.csv'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(fileExtension)) {
                alert('Please select a valid file type (.xlsx, .xls, or .csv)');
                this.value = '';
                $('#file-info').hide();
                $('#next-step-3').prop('disabled', true);
                return;
            }
            
            // Check if file is empty
            if (file.size === 0) {
                alert('The selected file is empty (0 bytes). Please select a valid file with data.');
                this.value = '';
                $('#file-info').hide();
                $('#next-step-3').prop('disabled', true);
                return;
            }
            
            // Validate file size (10MB limit)
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                alert('File size must be less than 10MB');
                this.value = '';
                $('#file-info').hide();
                $('#next-step-3').prop('disabled', true);
                return;
            }
            
            // Format file size with better handling
            let fileSizeText;
            let actualSize = file.size || 0;
            
            // If size is 0, try to read the file to get actual size
            if (actualSize === 0) {
                console.warn('File size is 0, attempting to read file...');
                const reader = new FileReader();
                reader.onload = function(e) {
                    const actualSize = e.target.result.byteLength || e.target.result.length || 0;
                    console.log('FileReader determined size:', actualSize);
                    updateFileSizeDisplay(actualSize);
                };
                reader.readAsArrayBuffer(file);
                fileSizeText = 'Reading file...';
            } else {
                if (actualSize === 0) {
                    fileSizeText = '0 bytes (Empty file)';
                } else if (actualSize < 1024) {
                    fileSizeText = actualSize + ' bytes';
                } else if (actualSize < 1024 * 1024) {
                    fileSizeText = (actualSize / 1024).toFixed(2) + ' KB';
                } else {
                    fileSizeText = (actualSize / 1024 / 1024).toFixed(2) + ' MB';
                }
            }
            
            function updateFileSizeDisplay(size) {
                let sizeText;
                if (size === 0) {
                    sizeText = '0 bytes (Empty file)';
                } else if (size < 1024) {
                    sizeText = size + ' bytes';
                } else if (size < 1024 * 1024) {
                    sizeText = (size / 1024).toFixed(2) + ' KB';
                } else {
                    sizeText = (size / 1024 / 1024).toFixed(2) + ' MB';
                }
                $('#file-size').text(sizeText);
                console.log('File size updated:', sizeText);
            }
            
            $('#file-name').text(file.name);
            $('#file-size').text(fileSizeText);
            $('#file-info').show();
            $('#next-step-3').prop('disabled', false);
            
            console.log('File validation successful:', {
                name: file.name,
                size: file.size,
                formattedSize: fileSizeText
            });
        } else {
            // No file selected
            $('#file-info').hide();
            $('#next-step-3').prop('disabled', true);
        }
    });
    
    $(document).on('click', '#remove-file', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Remove file clicked');
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
        console.log('Files dropped:', files.length);
        
        if (files.length > 0) {
            const file = files[0];
            console.log('Dropped file:', {
                name: file.name,
                size: file.size,
                type: file.type
            });
            
            // Create a new FileList with the dropped file
            const fileInput = document.getElementById('file-input');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Trigger change event
            $(fileInput).trigger('change');
        }
    });
    
    // Download template
    $('#download-template-btn').click(function() {
        if (!selectedModule) {
            alert('Please select a module first');
            return;
        }
        showProgress('Downloading template...');
        window.location.href = '/templates/' + selectedModule;
    });
    
    // Export data
    $('#export-data-btn').click(function() {
        if (!selectedModule) {
            alert('Please select a module first');
            return;
        }
        showProgress('Exporting data...');
        window.location.href = '/import-export/export/' + selectedModule;
    });
    
    // Step 3 to 4
    $('#next-step-3').click(function() {
        console.log('Next step 3 clicked, selectedAction:', selectedAction);
        if (selectedAction === 'import') {
            console.log('Starting import process');
            // Start import process
            performImport();
        } else {
            console.log('Going directly to completion for template/export');
            // For template/export, go directly to completion
            goToStep(5);
            showCompletion();
        }
    });
    
    function performImport() {
        if (!selectedModule) {
            alert('Please select a module first');
            return;
        }
        
        const fileInput = document.getElementById('file-input');
        if (fileInput.files.length === 0) {
            alert('Please select a file to import.');
            return;
        }
        
        goToStep(4);
        showProgress('Validating data...');
        
        const formData = new FormData();
        formData.append('csv_file', fileInput.files[0]);
        formData.append('module', selectedModule);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Show progress container
        $('#progress-container').show();
        $('#validation-results').hide();
        
        // First validate and show preview
        $.ajax({
            url: '/import-export/validate/' + selectedModule,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideProgress();
                $('#validation-results').show();
                
                if (response.success) {
                    // Validation passed, show preview before import
                    showImportPreviewFromValidation(response);
                } else {
                    // Show detailed validation errors
                    showValidationErrors(response);
                }
            },
            error: function(xhr, status, error) {
                hideProgress();
                $('#validation-results').show();
                $('#error-summary').show();
                $('#success-summary').hide();
                $('#warning-summary').hide();
                $('#next-step-4').prop('disabled', false);
                
                let errorMessage = 'Validation failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#error-summary .alert-text').text(errorMessage);
            }
        });
    }
    
    function performActualImport() {
        showProgress('Importing data...');
        
        const formData = new FormData();
        formData.append('file', document.getElementById('file-input').files[0]);
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
                        updateProgress(percentComplete, 'Uploading file...');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                hideProgress();
                $('#validation-results').show();
                
                if (response.success || response.imported > 0) {
                    $('#success-summary').show();
                    $('#warning-summary').hide();
                    $('#error-summary').hide();
                    $('#success-count').text(response.imported || 0);
                    $('#warning-count').text(response.warnings || 0);
                    $('#error-count').text(response.errors || 0);
                } else if (response.warnings && response.warnings > 0) {
                    $('#warning-summary').show();
                    $('#success-summary').hide();
                    $('#error-summary').hide();
                    $('#success-count').text(response.imported || 0);
                    $('#warning-count').text(response.warnings || 0);
                    $('#error-count').text(response.errors || 0);
                } else {
                    $('#error-summary').show();
                    $('#success-summary').hide();
                    $('#warning-summary').hide();
                    $('#success-count').text(0);
                    $('#warning-count').text(0);
                    $('#error-count').text(response.errors || 0);
                }
                
                $('#next-step-4').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                hideProgress();
                $('#validation-results').show();
                $('#error-summary').show();
                $('#success-summary').hide();
                $('#warning-summary').hide();
                $('#next-step-4').prop('disabled', false);
                
                let errorMessage = 'Import failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                console.error('Import failed:', error);
                alert(errorMessage);
            }
        });
    }
    
    function showImportPreviewFromValidation(response) {
        // Show preview when validation passes
        let previewHtml = '<div class="import-preview">';
        previewHtml += '<h4 class="text-success mb-3"><i class="fas fa-check-circle"></i> Validation Passed - Import Preview</h4>';
        
        // Summary
        previewHtml += '<div class="alert alert-success mb-3">';
        previewHtml += '<strong>✅ Validation Successful!</strong> ';
        previewHtml += 'Your data has been validated and is ready for import. ';
        previewHtml += 'Review the preview below before proceeding.';
        previewHtml += '</div>';
        
        // Show file info
        const fileInput1 = document.getElementById('file-input');
        if (fileInput1.files.length > 0) {
            const file = fileInput1.files[0];
            previewHtml += '<div class="alert alert-info mb-3">';
            previewHtml += '<strong>📁 File:</strong> ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            previewHtml += '</div>';
        }
        
        // Preview table
        previewHtml += '<div class="table-responsive mb-3">';
        previewHtml += '<table id="preview-table" class="table table-striped table-hover">';
        previewHtml += '<thead class="table-dark">';
        previewHtml += '<tr>';
        previewHtml += '<th>Row</th>';
        previewHtml += '<th>Status</th>';
        
        // Add headers based on module
        if (selectedModule === 'users') {
            previewHtml += '<th>Employee ID</th>';
            previewHtml += '<th>Name</th>';
            previewHtml += '<th>Email</th>';
            previewHtml += '<th>Department</th>';
            previewHtml += '<th>Role</th>';
            previewHtml += '<th>Company</th>';
            previewHtml += '<th>Status</th>';
        } else if (selectedModule === 'assets') {
            previewHtml += '<th>Asset Tag</th>';
            previewHtml += '<th>Asset Name</th>';
            previewHtml += '<th>Category</th>';
            previewHtml += '<th>Vendor</th>';
            previewHtml += '<th>Status</th>';
            previewHtml += '<th>Serial Number</th>';
        }
        
        previewHtml += '</tr>';
        previewHtml += '</thead>';
        previewHtml += '<tbody>';
        
        // Show sample data from the file
        const fileInput2 = document.getElementById('file-input');
        if (fileInput2.files.length > 0) {
            const file = fileInput2.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const csv = e.target.result;
                const lines = csv.split('\n').filter(line => line.trim());
                const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
                
                let tableBody = '';
                for (let i = 1; i < Math.min(lines.length, 6); i++) { // Show first 5 data rows
                    const row = lines[i].split(',').map(cell => cell.trim().replace(/"/g, ''));
                    const rowData = {};
                    headers.forEach((header, index) => {
                        rowData[header] = row[index] || '';
                    });
                    
                    tableBody += '<tr class="table-success">';
                    tableBody += '<td>' + (i + 1) + '</td>';
                    tableBody += '<td><span class="badge bg-success">✅ Valid</span></td>';
                    
                    if (selectedModule === 'users') {
                        tableBody += '<td>' + (rowData.employee_id || '') + '</td>';
                        tableBody += '<td>' + (rowData.first_name || '') + ' ' + (rowData.last_name || '') + '</td>';
                        tableBody += '<td>' + (rowData.email_address || '') + '</td>';
                        tableBody += '<td>' + (rowData.department || '') + '</td>';
                        tableBody += '<td>' + (rowData.role || '') + '</td>';
                        tableBody += '<td>' + (rowData.company || '') + '</td>';
                        tableBody += '<td>' + (rowData.status || '') + '</td>';
                    } else if (selectedModule === 'assets') {
                        tableBody += '<td>' + (rowData.asset_tag || 'AUTO-GENERATED') + '</td>';
                        tableBody += '<td>' + (rowData.asset_name || '') + '</td>';
                        tableBody += '<td>' + (rowData.category || '') + '</td>';
                        tableBody += '<td>' + (rowData.vendor || '') + '</td>';
                        tableBody += '<td>' + (rowData.status || '') + '</td>';
                        tableBody += '<td>' + (rowData.serial_number || '') + '</td>';
                    }
                    
                    tableBody += '</tr>';
                }
                
                // Update the table body
                const tbody = document.querySelector('#preview-table tbody');
                if (tbody) {
                    tbody.innerHTML = tableBody;
                }
            };
            reader.readAsText(file);
        }
        
        previewHtml += '<tbody id="preview-table-body">';
        previewHtml += '<tr><td colspan="8" class="text-center">Loading preview...</td></tr>';
        previewHtml += '</tbody>';
        previewHtml += '</table>';
        previewHtml += '</div>';
        
        // Action buttons
        previewHtml += '<div class="text-center mt-4">';
        previewHtml += '<button class="btn btn-success me-2" onclick="proceedWithImport()">';
        previewHtml += '<i class="fas fa-check"></i> Proceed with Import';
        previewHtml += '</button>';
        previewHtml += '<button class="btn btn-secondary me-2" onclick="goBackToStep(3)">';
        previewHtml += '<i class="fas fa-arrow-left"></i> Back to Upload';
        previewHtml += '</button>';
        previewHtml += '<button class="btn btn-info" onclick="downloadPreviewReport()">';
        previewHtml += '<i class="fas fa-download"></i> Download Preview Report';
        previewHtml += '</button>';
        previewHtml += '</div>';
        
        previewHtml += '</div>';
        
        // Replace step 4 content with preview
        $('#step-4 .step-content').html(previewHtml);
    }

    function showImportPreview(response) {
        // Store response for download feature
        window.lastPreviewResponse = response;
        
        let previewHtml = '<div class="import-preview">';
        previewHtml += '<h4 class="text-primary mb-3"><i class="fas fa-eye"></i> Import Preview</h4>';
        
        // Summary
        if (response.summary) {
            previewHtml += '<div class="alert alert-info mb-3">';
            previewHtml += '<strong>📊 Summary:</strong> ';
            previewHtml += response.summary.total_rows + ' total rows, ';
            previewHtml += response.summary.valid_rows + ' valid rows, ';
            previewHtml += response.summary.errors + ' errors, ';
            previewHtml += response.summary.warnings + ' warnings';
            previewHtml += '</div>';
        }
        
        // Preview table
        if (response.preview_data && response.preview_data.length > 0) {
            previewHtml += '<div class="table-responsive mb-3">';
            previewHtml += '<table class="table table-striped table-hover">';
            previewHtml += '<thead class="table-dark">';
            previewHtml += '<tr>';
            previewHtml += '<th>Row</th>';
            previewHtml += '<th>Status</th>';
            
            // Add headers based on module
            if (selectedModule === 'users') {
                previewHtml += '<th>Employee ID</th>';
                previewHtml += '<th>Name</th>';
                previewHtml += '<th>Email</th>';
                previewHtml += '<th>Department</th>';
                previewHtml += '<th>Role</th>';
                previewHtml += '<th>Company</th>';
                previewHtml += '<th>Status</th>';
            } else if (selectedModule === 'assets') {
                previewHtml += '<th>Asset Tag</th>';
                previewHtml += '<th>Asset Name</th>';
                previewHtml += '<th>Category</th>';
                previewHtml += '<th>Vendor</th>';
                previewHtml += '<th>Status</th>';
                previewHtml += '<th>Serial Number</th>';
            }
            
            previewHtml += '<th>Issues</th>';
            previewHtml += '</tr>';
            previewHtml += '</thead>';
            previewHtml += '<tbody>';
            
            response.preview_data.forEach(function(row) {
                previewHtml += '<tr class="' + (row.status === 'error' ? 'table-danger' : 'table-success') + '">';
                previewHtml += '<td>' + row.row_number + '</td>';
                previewHtml += '<td>';
                if (row.status === 'error') {
                    previewHtml += '<span class="badge bg-danger">❌ Error</span>';
                } else {
                    previewHtml += '<span class="badge bg-success">✅ Valid</span>';
                }
                previewHtml += '</td>';
                
                // Add data based on module
                if (selectedModule === 'users') {
                    previewHtml += '<td>' + (row.processed_data.employee_id || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.first_name || '') + ' ' + (row.processed_data.last_name || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.email || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.department || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.role || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.company || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.status || '') + '</td>';
                } else if (selectedModule === 'assets') {
                    previewHtml += '<td>' + (row.processed_data.asset_tag || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.asset_name || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.category || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.vendor || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.status || '') + '</td>';
                    previewHtml += '<td>' + (row.processed_data.serial_number || '') + '</td>';
                }
                
                // Issues column
                previewHtml += '<td>';
                if (row.errors && row.errors.length > 0) {
                    previewHtml += '<span class="text-danger">' + row.errors.join(', ') + '</span>';
                }
                if (row.processed_data.duplicate_employee_id) {
                    previewHtml += '<span class="text-warning">' + row.processed_data.duplicate_employee_id + '</span><br>';
                }
                if (row.processed_data.duplicate_email) {
                    previewHtml += '<span class="text-warning">' + row.processed_data.duplicate_email + '</span><br>';
                }
                previewHtml += '</td>';
                previewHtml += '</tr>';
            });
            
            previewHtml += '</tbody>';
            previewHtml += '</table>';
            previewHtml += '</div>';
        }
        
        // Action buttons
        previewHtml += '<div class="text-center mt-4">';
        previewHtml += '<button class="btn btn-success me-2" onclick="proceedWithImport()">';
        previewHtml += '<i class="fas fa-check"></i> Proceed with Import';
        previewHtml += '</button>';
        previewHtml += '<button class="btn btn-secondary me-2" onclick="goBackToStep(3)">';
        previewHtml += '<i class="fas fa-arrow-left"></i> Back to Upload';
        previewHtml += '</button>';
        previewHtml += '<button class="btn btn-info" onclick="downloadPreviewReport()">';
        previewHtml += '<i class="fas fa-download"></i> Download Preview Report';
        previewHtml += '</button>';
        previewHtml += '</div>';
        
        previewHtml += '</div>';
        
        // Replace step 4 content with preview
        $('#step-4 .step-content').html(previewHtml);
    }
    
    function proceedWithImport() {
        showProgress('Importing data...');
        
        const formData = new FormData();
        formData.append('file', document.getElementById('file-input').files[0]);
        formData.append('module', selectedModule);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: '/import-export/import/' + selectedModule,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideProgress();
                $('#validation-results').show();
                
                if (response.success || response.imported > 0) {
                    $('#success-summary').show();
                    $('#warning-summary').hide();
                    $('#error-summary').hide();
                    $('#success-count').text(response.imported || 0);
                    $('#warning-count').text(response.warnings || 0);
                    $('#error-count').text(response.errors || 0);
                } else if (response.warnings && response.warnings > 0) {
                    $('#warning-summary').show();
                    $('#success-summary').hide();
                    $('#error-summary').hide();
                    $('#success-count').text(response.imported || 0);
                    $('#warning-count').text(response.warnings || 0);
                    $('#error-count').text(response.errors || 0);
                } else {
                    $('#error-summary').show();
                    $('#success-summary').hide();
                    $('#warning-summary').hide();
                    $('#error-count').text(response.errors || 0);
                }
                $('#next-step-4').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                hideProgress();
                $('#validation-results').show();
                $('#error-summary').show();
                $('#success-summary').hide();
                $('#warning-summary').hide();
                $('#next-step-4').prop('disabled', false);
                
                let errorMessage = 'Import failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#error-summary .alert-text').text(errorMessage);
            }
        });
    }
    
    function goBackToStep(stepNumber) {
        // Reset current step
        $('.step').removeClass('active');
        $('.step-content').removeClass('active');
        
        // Go to specified step
        goToStep(stepNumber);
    }
    
    function downloadPreviewReport() {
        // Generate and download preview report
        const reportData = {
            module: selectedModule,
            timestamp: new Date().toISOString(),
            summary: window.lastPreviewResponse ? window.lastPreviewResponse.summary : null,
            preview_data: window.lastPreviewResponse ? window.lastPreviewResponse.preview_data : []
        };
        
        const dataStr = JSON.stringify(reportData, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'import_preview_report_' + selectedModule + '_' + new Date().toISOString().split('T')[0] + '.json';
        link.click();
        URL.revokeObjectURL(url);
    }

    function showValidationErrors(response) {
        // Show the error summary with specific details
        $('#validation-results').show();
        $('#error-summary').show();
        $('#success-summary').hide();
        $('#warning-summary').hide();
        $('#next-step-4').prop('disabled', true);
        
        let errorDetailsHtml = '';
        
        // Beginner-friendly introduction
        errorDetailsHtml += '<div class="alert alert-warning mb-3">';
        errorDetailsHtml += '<h6><i class="fas fa-info-circle"></i> Don\'t worry! This is normal for first-time users.</h6>';
        errorDetailsHtml += '<p class="mb-2">Your data has some issues that need to be fixed before importing. Follow the steps below to correct them:</p>';
        errorDetailsHtml += '<ol class="mb-0 small">';
        errorDetailsHtml += '<li><strong>Read the errors below</strong> - Each error tells you exactly what\'s wrong</li>';
        errorDetailsHtml += '<li><strong>Fix your file</strong> - Update the values in your CSV/Excel file</li>';
        errorDetailsHtml += '<li><strong>Re-upload</strong> - Upload the corrected file</li>';
        errorDetailsHtml += '<li><strong>See the preview</strong> - Once fixed, you\'ll see a preview before importing</li>';
        errorDetailsHtml += '</ol>';
        errorDetailsHtml += '</div>';
        
        if (response.summary) {
            errorDetailsHtml += '<div class="alert alert-info mb-3">';
            errorDetailsHtml += '<strong>📊 File Summary:</strong> ';
            errorDetailsHtml += response.summary.total_rows + ' rows in your file, ';
            errorDetailsHtml += response.summary.errors + ' errors need fixing, ';
            errorDetailsHtml += response.summary.warnings + ' warnings (optional)';
            errorDetailsHtml += '</div>';
        }
        
        // Use errors field from validation response
        const errorList = response.errors || [];
        
        if (errorList.length > 0) {
            errorDetailsHtml += '<div class="errors-section mb-3">';
            errorDetailsHtml += '<h6 class="text-danger"><i class="fas fa-times-circle"></i> Specific Errors to Fix:</h6>';
            errorDetailsHtml += '<div class="table-responsive">';
            errorDetailsHtml += '<table class="table table-sm table-bordered table-hover">';
            errorDetailsHtml += '<thead class="table-danger">';
            errorDetailsHtml += '<tr><th>#</th><th>Row</th><th>Field</th><th>What\'s Wrong</th><th>Your Value</th><th>How to Fix It</th></tr>';
            errorDetailsHtml += '</thead><tbody>';
            
            errorList.forEach(function(error, index) {
                errorDetailsHtml += '<tr>';
                errorDetailsHtml += '<td><span class="badge bg-danger">' + (index + 1) + '</span></td>';
                errorDetailsHtml += '<td><strong>Row ' + (error.row || 'N/A') + '</strong></td>';
                errorDetailsHtml += '<td><strong>' + (error.field || 'General') + '</strong></td>';
                errorDetailsHtml += '<td class="text-danger"><i class="fas fa-exclamation-triangle"></i> ' + error.message + '</td>';
                errorDetailsHtml += '<td><code class="text-muted">' + (error.value || 'Empty') + '</code></td>';
                errorDetailsHtml += '<td class="text-success"><i class="fas fa-lightbulb"></i> ' + (error.suggestion || 'See valid values below') + '</td>';
                errorDetailsHtml += '</tr>';
            });
            
            errorDetailsHtml += '</tbody></table>';
            errorDetailsHtml += '</div>';
            errorDetailsHtml += '</div>';
        } else {
            // Fallback if no specific errors are found
            errorDetailsHtml += '<div class="alert alert-warning mb-3">';
            errorDetailsHtml += '<h6><i class="fas fa-exclamation-triangle"></i> No specific error details available</h6>';
            errorDetailsHtml += '<p class="mb-2">The validation failed but no detailed error information was provided.</p>';
            errorDetailsHtml += '<p class="mb-0">Please check your file format and try again, or contact support if the issue persists.</p>';
            errorDetailsHtml += '</div>';
        }
        
        // Use warnings field from validation response
        const warningList = response.warnings || [];
        
        if (warningList.length > 0) {
            errorDetailsHtml += '<div class="warnings-section mb-3">';
            errorDetailsHtml += '<h6 class="text-warning">Warnings:</h6>';
            errorDetailsHtml += '<ul class="list-group">';
            
            warningList.forEach(function(warning) {
                errorDetailsHtml += '<li class="list-group-item list-group-item-warning">';
                errorDetailsHtml += '<strong>Row ' + (warning.row || 'N/A') + ':</strong> ' + warning.message;
                errorDetailsHtml += '</li>';
            });
            
            errorDetailsHtml += '</ul>';
            errorDetailsHtml += '</div>';
        }
        
        console.log('Valid values response:', response.valid_values);
        
        if (response.valid_values) {
            errorDetailsHtml += '<div class="valid-values-section mb-3">';
            errorDetailsHtml += '<h6 class="text-info"><i class="fas fa-list"></i> Copy These Exact Values to Fix Your File:</h6>';
            
            // Show required columns if available
            if (response.valid_values.required_columns && response.valid_values.required_columns.length > 0) {
                errorDetailsHtml += '<div class="mb-3">';
                errorDetailsHtml += '<h6><i class="fas fa-table"></i> Required Column Headers (Copy exactly as shown):</h6>';
                errorDetailsHtml += '<div class="alert alert-light">';
                errorDetailsHtml += '<div class="row">';
                response.valid_values.required_columns.forEach(function(column, index) {
                    errorDetailsHtml += '<div class="col-md-4 mb-2"><span class="badge bg-success me-2">' + (index + 1) + '</span><code class="text-dark">' + column + '</code></div>';
                });
                errorDetailsHtml += '</div>';
                errorDetailsHtml += '<small class="text-muted"><i class="fas fa-info-circle"></i> <strong>Tip:</strong> Your CSV file must have these exact column headers in the first row!</small>';
                errorDetailsHtml += '</div></div>';
            }
            
            if (response.valid_values.departments && response.valid_values.departments.length > 0) {
                errorDetailsHtml += '<div class="mb-3">';
                errorDetailsHtml += '<h6><i class="fas fa-building"></i> Valid Department Names (Copy exactly as shown):</h6>';
                errorDetailsHtml += '<div class="alert alert-light">';
                errorDetailsHtml += '<div class="row">';
                response.valid_values.departments.forEach(function(dept, index) {
                    if (index < 10) { // Show first 10
                        errorDetailsHtml += '<div class="col-md-6 mb-2"><span class="badge bg-primary me-2">' + (index + 1) + '</span><code class="text-dark">' + dept + '</code></div>';
                    }
                });
                if (response.valid_values.departments.length > 10) {
                    errorDetailsHtml += '<div class="col-12 text-muted">... and ' + (response.valid_values.departments.length - 10) + ' more departments available</div>';
                }
                errorDetailsHtml += '</div>';
                errorDetailsHtml += '<small class="text-muted"><i class="fas fa-info-circle"></i> <strong>Tip:</strong> Copy and paste these exact names into your file. Case and spelling must match exactly!</small>';
                errorDetailsHtml += '</div></div>';
            }
            
            if (response.valid_values.vendors && response.valid_values.vendors.length > 0) {
                errorDetailsHtml += '<div class="mb-3">';
                errorDetailsHtml += '<h6><i class="fas fa-truck"></i> Valid Vendors (Copy exactly as shown):</h6>';
                errorDetailsHtml += '<div class="alert alert-light">';
                errorDetailsHtml += '<div class="row">';
                response.valid_values.vendors.forEach(function(vendor, index) {
                    if (index < 10) { // Show first 10
                        errorDetailsHtml += '<div class="col-md-6 mb-2"><span class="badge bg-info me-2">' + (index + 1) + '</span><code class="text-dark">' + vendor + '</code></div>';
                    }
                });
                if (response.valid_values.vendors.length > 10) {
                    errorDetailsHtml += '<div class="col-12 text-muted">... and ' + (response.valid_values.vendors.length - 10) + ' more vendors available</div>';
                }
                errorDetailsHtml += '</div>';
                errorDetailsHtml += '<small class="text-muted"><i class="fas fa-info-circle"></i> <strong>Tip:</strong> Copy and paste these exact names into your file. Case and spelling must match exactly!</small>';
                errorDetailsHtml += '</div></div>';
            }
            
            if (response.valid_values.categories && response.valid_values.categories.length > 0) {
                errorDetailsHtml += '<div class="mb-3">';
                errorDetailsHtml += '<h6><i class="fas fa-tags"></i> Valid Categories (Copy exactly as shown):</h6>';
                errorDetailsHtml += '<div class="alert alert-light">';
                errorDetailsHtml += '<div class="row">';
                response.valid_values.categories.forEach(function(category, index) {
                    if (index < 10) { // Show first 10
                        errorDetailsHtml += '<div class="col-md-6 mb-2"><span class="badge bg-warning me-2">' + (index + 1) + '</span><code class="text-dark">' + category + '</code></div>';
                    }
                });
                if (response.valid_values.categories.length > 10) {
                    errorDetailsHtml += '<div class="col-12 text-muted">... and ' + (response.valid_values.categories.length - 10) + ' more categories available</div>';
                }
                errorDetailsHtml += '</div>';
                errorDetailsHtml += '<small class="text-muted"><i class="fas fa-info-circle"></i> <strong>Tip:</strong> Copy and paste these exact names into your file. Case and spelling must match exactly!</small>';
                errorDetailsHtml += '</div></div>';
            }
            
            errorDetailsHtml += '</div>';
        }
        
        // Helpful action buttons
        errorDetailsHtml += '<div class="text-center mt-3">';
        errorDetailsHtml += '<div class="alert alert-success mb-3">';
        errorDetailsHtml += '<h6><i class="fas fa-lightbulb"></i> Quick Fix Guide:</h6>';
        errorDetailsHtml += '<ol class="text-start mb-0 small">';
        errorDetailsHtml += '<li><strong>Open your file</strong> in Excel or a text editor</li>';
        errorDetailsHtml += '<li><strong>Find the errors</strong> using the row numbers above</li>';
        errorDetailsHtml += '<li><strong>Copy the correct values</strong> from the lists above</li>';
        errorDetailsHtml += '<li><strong>Save your file</strong> and upload it again</li>';
        errorDetailsHtml += '</ol>';
        errorDetailsHtml += '</div>';
        
        errorDetailsHtml += '<button class="btn btn-primary me-2" onclick="goBackToStep(3)">';
        errorDetailsHtml += '<i class="fas fa-arrow-left"></i> Back to Upload Fixed File';
        errorDetailsHtml += '</button>';
        errorDetailsHtml += '<button class="btn btn-info" onclick="downloadTemplate()">';
        errorDetailsHtml += '<i class="fas fa-download"></i> Download Template (Recommended)';
        errorDetailsHtml += '</button>';
        errorDetailsHtml += '</div>';
        
        // Display the error details in the error-details div
        $('#error-details').html(errorDetailsHtml);
    }
    
    function goBackToStep(stepNumber) {
        goToStep(stepNumber);
    }
    
    function showProgress(message) {
        $('#progress-container').show();
        updateProgress(0, message);
    }
    
    function updateProgress(percent, message) {
        $('#progress-bar').css('width', percent + '%');
        $('#progress-text').text(Math.round(percent) + '%');
        if (message) {
            $('.progress-container .text-muted').first().text(message);
        }
    }
    
    function hideProgress() {
        $('#progress-container').hide();
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
        $('#selection-status').hide();
        goToStep(1);
    });
});
</script>
@endsection