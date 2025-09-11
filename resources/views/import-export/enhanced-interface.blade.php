@extends('layouts.app')

@section('title', 'Import/Export Manager')
@section('page-title', 'Import/Export Manager')

@section('styles')
<style>
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #f8f9fa;
    }
    
    .upload-zone:hover {
        border-color: #007bff;
        background: #e3f2fd;
    }
    
    .upload-zone.dragover {
        border-color: #28a745;
        background: #d4edda;
    }
    
    .upload-zone.error {
        border-color: #dc3545;
        background: #f8d7da;
    }
    
    .validation-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
    }
    
    .validation-feedback.valid {
        color: #28a745;
    }
    
    .validation-feedback.invalid {
        color: #dc3545;
    }
    
    .progress-container {
        display: none;
        margin-top: 20px;
    }
    
    .error-row {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }
    
    .warning-row {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    
    .success-row {
        background-color: #d1e7dd;
        border-left: 4px solid #28a745;
    }
    
    .error-highlight {
        background-color: #f8d7da !important;
        border: 1px solid #dc3545 !important;
    }
    
    .warning-highlight {
        background-color: #fff3cd !important;
        border: 1px solid #ffc107 !important;
    }
    
    .field-error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .field-warning {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    }
    
    .field-success {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }
    
    .template-preview {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .serial-suggestion {
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 4px;
        margin: 2px;
        background: #e9ecef;
        border: 1px solid #ced4da;
        transition: all 0.2s;
    }
    
    .serial-suggestion:hover {
        background: #007bff;
        color: white;
    }
    
    .validation-summary {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    
    .step {
        flex: 1;
        text-align: center;
        position: relative;
    }
    
    .step::after {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: -1;
    }
    
    .step:last-child::after {
        display: none;
    }
    
    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .step.active .step-circle {
        background: #007bff;
        color: white;
    }
    
    .step.completed .step-circle {
        background: #28a745;
        color: white;
    }
    
    .step.error .step-circle {
        background: #dc3545;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" id="step-1">
            <div class="step-circle">1</div>
            <div class="step-label">Select Module</div>
        </div>
        <div class="step" id="step-2">
            <div class="step-circle">2</div>
            <div class="step-label">Choose Action</div>
        </div>
        <div class="step" id="step-3">
            <div class="step-circle">3</div>
            <div class="step-label">Upload/Download</div>
        </div>
        <div class="step" id="step-4">
            <div class="step-circle">4</div>
            <div class="step-label">Validation</div>
        </div>
        <div class="step" id="step-5">
            <div class="step-circle">5</div>
            <div class="step-label">Complete</div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Module Selection -->
            <div class="card mb-4" id="module-selection">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>Select Module
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach(['assets', 'users', 'computers', 'departments', 'vendors'] as $module)
                        <div class="col-md-4">
                            <div class="card module-card h-100" data-module="{{ $module }}" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body text-center">
                                    <i class="fas fa-{{ $module === 'assets' ? 'boxes' : ($module === 'users' ? 'users' : ($module === 'computers' ? 'desktop' : ($module === 'departments' ? 'building' : 'truck'))) }} fa-2x text-primary mb-3"></i>
                                    <h6 class="card-title">{{ ucfirst($module) }}</h6>
                                    <p class="card-text text-muted small">Import/Export {{ $module }} data</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Selection -->
            <div class="card mb-4" id="action-selection" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>Choose Action
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card action-card h-100" data-action="template" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-2x text-success mb-3"></i>
                                    <h6 class="card-title">Download Template</h6>
                                    <p class="card-text text-muted small">Get template with sample data</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card action-card h-100" data-action="export" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-export fa-2x text-info mb-3"></i>
                                    <h6 class="card-title">Export Data</h6>
                                    <p class="card-text text-muted small">Export existing data</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card action-card h-100" data-action="import" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-import fa-2x text-warning mb-3"></i>
                                    <h6 class="card-title">Import Data</h6>
                                    <p class="card-text text-muted small">Upload and import data</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Download Section -->
            <div class="card mb-4" id="template-section" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>Download Template
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Template Options</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeSampleData" checked>
                                <label class="form-check-label" for="includeSampleData">
                                    Include sample data
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeValidationRules" checked>
                                <label class="form-check-label" for="includeValidationRules">
                                    Include validation rules as comments
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="autoPopulateSerials">
                                <label class="form-check-label" for="autoPopulateSerials">
                                    Auto-populate serial numbers
                                </label>
                            </div>
                            <button class="btn btn-success mt-3" id="downloadTemplateBtn">
                                <i class="fas fa-download me-2"></i>Download Template
                            </button>
                        </div>
                        <div class="col-md-6">
                            <h6>Template Preview</h6>
                            <div class="template-preview" id="templatePreview">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-file-csv fa-2x mb-2"></i>
                                    <p>Template preview will appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Section -->
            <div class="card mb-4" id="import-section" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-import me-2"></i>Import Data
                    </h5>
                </div>
                <div class="card-body">
                    <!-- File Upload Zone -->
                    <div class="upload-zone" id="uploadZone">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h6>Drag and drop your file here</h6>
                        <p class="text-muted">or click to browse</p>
                        <input type="file" id="fileInput" accept=".csv,.xlsx,.xls" style="display: none;">
                        <button class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-folder-open me-2"></i>Browse Files
                        </button>
                    </div>

                    <!-- File Info -->
                    <div id="fileInfo" style="display: none;" class="mt-3">
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i>
                                    <span id="fileName"></span>
                                    <small class="text-muted ms-2" id="fileSize"></small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" id="removeFile">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Import Options -->
                    <div id="importOptions" style="display: none;" class="mt-3">
                        <h6>Import Options</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="validateOnly">
                                    <label class="form-check-label" for="validateOnly">
                                        Validate only (don't import)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="skipDuplicates" checked>
                                    <label class="form-check-label" for="skipDuplicates">
                                        Skip duplicate entries
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autoGenerateSerials">
                                    <label class="form-check-label" for="autoGenerateSerials">
                                        Auto-generate missing serial numbers
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="batchSize" class="form-label">Batch Size</label>
                                    <select class="form-select" id="batchSize">
                                        <option value="50">50 records</option>
                                        <option value="100" selected>100 records</option>
                                        <option value="250">250 records</option>
                                        <option value="500">500 records</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-warning mt-3" id="startImportBtn">
                            <i class="fas fa-play me-2"></i>Start Import
                        </button>
                    </div>

                    <!-- Progress Section -->
                    <div class="progress-container" id="progressContainer">
                        <h6>Import Progress</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small id="progressText">Preparing import...</small>
                            <small id="progressPercent">0%</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Results -->
            <div class="card mb-4" id="validation-results" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Validation Results
                    </h5>
                </div>
                <div class="card-body">
                    <div id="validationSummary"></div>
                    <div id="validationDetails" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Validation Summary -->
            <div class="card validation-summary mb-4" id="validationSummaryCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Validation Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div id="summaryContent">
                        <div class="text-center text-muted">
                            <i class="fas fa-hourglass-half fa-2x mb-2"></i>
                            <p>Waiting for validation...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Serial Number Generator -->
            <div class="card mb-4" id="serialGeneratorCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-hashtag me-2"></i>Serial Number Generator
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="serialPrefix" class="form-label">Prefix</label>
                        <input type="text" class="form-control" id="serialPrefix" value="SN" maxlength="5">
                    </div>
                    <div class="form-group mb-3">
                        <label for="serialFormat" class="form-label">Format</label>
                        <select class="form-select" id="serialFormat">
                            <option value="alphanumeric">Alphanumeric</option>
                            <option value="numeric">Numeric</option>
                            <option value="alphabetic">Alphabetic</option>
                            <option value="sequential">Sequential</option>
                            <option value="timestamp">Timestamp</option>
                        </select>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" id="generateSerialsBtn">
                        <i class="fas fa-magic me-2"></i>Generate Suggestions
                    </button>
                    <div id="serialSuggestions" class="mt-3"></div>
                </div>
            </div>

            <!-- Help & Guidelines -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Help & Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div id="helpContent">
                        <h6>Getting Started</h6>
                        <ol class="small">
                            <li>Select the module you want to work with</li>
                            <li>Choose your action (template, export, or import)</li>
                            <li>Follow the step-by-step process</li>
                        </ol>
                        
                        <h6 class="mt-3">File Requirements</h6>
                        <ul class="small">
                            <li>Supported formats: CSV, Excel (.xlsx, .xls)</li>
                            <li>Maximum file size: 10MB</li>
                            <li>First row must contain headers</li>
                            <li>Required fields must not be empty</li>
                        </ul>
                        
                        <h6 class="mt-3">Common Issues</h6>
                        <ul class="small">
                            <li><strong>Duplicate entries:</strong> Check serial numbers and asset tags</li>
                            <li><strong>Format errors:</strong> Ensure dates are in YYYY-MM-DD format</li>
                            <li><strong>Missing references:</strong> Create categories/vendors first</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Details Modal -->
<div class="modal fade" id="errorDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadErrorReport">
                    <i class="fas fa-download me-2"></i>Download Error Report
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Enhanced Import/Export Interface JavaScript
class ImportExportManager {
    constructor() {
        this.currentModule = null;
        this.currentAction = null;
        this.currentStep = 1;
        this.validationResults = null;
        this.uploadedFile = null;
        
        this.initializeEventListeners();
    }
    
    initializeEventListeners() {
        // Module selection
        document.querySelectorAll('.module-card').forEach(card => {
            card.addEventListener('click', (e) => {
                this.selectModule(card.dataset.module);
            });
        });
        
        // Action selection
        document.querySelectorAll('.action-card').forEach(card => {
            card.addEventListener('click', (e) => {
                this.selectAction(card.dataset.action);
            });
        });
        
        // File upload
        this.initializeFileUpload();
        
        // Template download
        document.getElementById('downloadTemplateBtn')?.addEventListener('click', () => {
            this.downloadTemplate();
        });
        
        // Import start
        document.getElementById('startImportBtn')?.addEventListener('click', () => {
            this.startImport();
        });
        
        // Serial number generator
        document.getElementById('generateSerialsBtn')?.addEventListener('click', () => {
            this.generateSerialSuggestions();
        });
        
        // Remove file
        document.getElementById('removeFile')?.addEventListener('click', () => {
            this.removeFile();
        });
    }
    
    selectModule(module) {
        this.currentModule = module;
        
        // Update UI
        document.querySelectorAll('.module-card').forEach(card => {
            card.classList.remove('border-primary');
        });
        document.querySelector(`[data-module="${module}"]`).classList.add('border-primary');
        
        // Show action selection
        document.getElementById('action-selection').style.display = 'block';
        this.updateStep(2);
        
        // Update help content
        this.updateHelpContent(module);
    }
    
    selectAction(action) {
        this.currentAction = action;
        
        // Update UI
        document.querySelectorAll('.action-card').forEach(card => {
            card.classList.remove('border-primary');
        });
        document.querySelector(`[data-action="${action}"]`).classList.add('border-primary');
        
        // Show appropriate section
        this.hideAllSections();
        
        switch(action) {
            case 'template':
                document.getElementById('template-section').style.display = 'block';
                this.loadTemplatePreview();
                break;
            case 'export':
                this.performExport();
                break;
            case 'import':
                document.getElementById('import-section').style.display = 'block';
                document.getElementById('serialGeneratorCard').style.display = 'block';
                break;
        }
        
        this.updateStep(3);
    }
    
    hideAllSections() {
        document.getElementById('template-section').style.display = 'none';
        document.getElementById('import-section').style.display = 'none';
        document.getElementById('validation-results').style.display = 'none';
    }
    
    initializeFileUpload() {
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('fileInput');
        
        // Drag and drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileSelect(files[0]);
            }
        });
        
        // Click to upload
        uploadZone.addEventListener('click', () => {
            fileInput.click();
        });
        
        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFileSelect(e.target.files[0]);
            }
        });
    }
    
    handleFileSelect(file) {
        // Validate file
        const validTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!validTypes.includes(file.type) && !file.name.match(/\.(csv|xlsx|xls)$/i)) {
            this.showError('Invalid file type. Please upload a CSV or Excel file.');
            return;
        }
        
        if (file.size > maxSize) {
            this.showError('File size too large. Maximum size is 10MB.');
            return;
        }
        
        this.uploadedFile = file;
        
        // Update UI
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = this.formatFileSize(file.size);
        document.getElementById('fileInfo').style.display = 'block';
        document.getElementById('importOptions').style.display = 'block';
        
        // Validate file content
        this.validateFileContent(file);
    }
    
    removeFile() {
        this.uploadedFile = null;
        document.getElementById('fileInfo').style.display = 'none';
        document.getElementById('importOptions').style.display = 'none';
        document.getElementById('fileInput').value = '';
        this.hideValidationResults();
    }
    
    async validateFileContent(file) {
        try {
            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('module', this.currentModule);
            formData.append('validate_only', 'true');
            
            const response = await fetch(`/import-export/import/${this.currentModule}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const result = await response.json();
            this.displayValidationResults(result);
            
        } catch (error) {
            console.error('Validation error:', error);
            this.showError('Failed to validate file. Please try again.');
        }
    }
    
    displayValidationResults(results) {
        this.validationResults = results;
        
        // Show validation summary card
        document.getElementById('validationSummaryCard').style.display = 'block';
        
        const summaryContent = document.getElementById('summaryContent');
        const hasErrors = results.errors && results.errors.length > 0;
        const hasWarnings = results.warnings && results.warnings.length > 0;
        
        let summaryHtml = `
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                        <div class="fw-bold">${results.valid_rows || 0}</div>
                        <small>Valid Rows</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                        <div class="fw-bold">${results.error_count || 0}</div>
                        <small>Errors</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-warning">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                        <div class="fw-bold">${results.warning_count || 0}</div>
                        <small>Warnings</small>
                    </div>
                </div>
            </div>
        `;
        
        if (hasErrors) {
            summaryHtml += `
                <div class="alert alert-danger mt-3">
                    <h6><i class="fas fa-exclamation-circle me-2"></i>Errors Found</h6>
                    <p class="mb-2">Please fix the following issues before importing:</p>
                    <ul class="mb-0">
                        ${results.errors.slice(0, 3).map(error => `<li>${error.message}</li>`).join('')}
                        ${results.errors.length > 3 ? `<li>... and ${results.errors.length - 3} more</li>` : ''}
                    </ul>
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="importManager.showErrorDetails()">
                        View All Errors
                    </button>
                </div>
            `;
        }
        
        if (hasWarnings && !hasErrors) {
            summaryHtml += `
                <div class="alert alert-warning mt-3">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Warnings</h6>
                    <p class="mb-0">Import can proceed, but please review warnings.</p>
                </div>
            `;
        }
        
        if (!hasErrors && !hasWarnings) {
            summaryHtml += `
                <div class="alert alert-success mt-3">
                    <h6><i class="fas fa-check-circle me-2"></i>Ready to Import</h6>
                    <p class="mb-0">All data validated successfully!</p>
                </div>
            `;
        }
        
        summaryContent.innerHTML = summaryHtml;
        
        // Update step indicator
        if (hasErrors) {
            this.updateStep(4, 'error');
        } else {
            this.updateStep(4, 'completed');
        }
    }
    
    showErrorDetails() {
        if (!this.validationResults) return;
        
        const modal = new bootstrap.Modal(document.getElementById('errorDetailsModal'));
        const content = document.getElementById('errorDetailsContent');
        
        let errorHtml = '';
        
        if (this.validationResults.errors) {
            errorHtml += '<h6>Errors</h6>';
            errorHtml += '<div class="table-responsive">';
            errorHtml += '<table class="table table-sm">';
            errorHtml += '<thead><tr><th>Row</th><th>Field</th><th>Message</th><th>Suggestions</th></tr></thead>';
            errorHtml += '<tbody>';
            
            this.validationResults.errors.forEach(error => {
                errorHtml += `
                    <tr class="error-row">
                        <td>${error.row || 'N/A'}</td>
                        <td>${error.field || 'N/A'}</td>
                        <td>${error.message}</td>
                        <td>${error.suggestions ? error.suggestions.join(', ') : ''}</td>
                    </tr>
                `;
            });
            
            errorHtml += '</tbody></table></div>';
        }
        
        if (this.validationResults.warnings) {
            errorHtml += '<h6 class="mt-4">Warnings</h6>';
            errorHtml += '<div class="table-responsive">';
            errorHtml += '<table class="table table-sm">';
            errorHtml += '<thead><tr><th>Row</th><th>Field</th><th>Message</th></tr></thead>';
            errorHtml += '<tbody>';
            
            this.validationResults.warnings.forEach(warning => {
                errorHtml += `
                    <tr class="warning-row">
                        <td>${warning.row || 'N/A'}</td>
                        <td>${warning.field || 'N/A'}</td>
                        <td>${warning.message}</td>
                    </tr>
                `;
            });
            
            errorHtml += '</tbody></table></div>';
        }
        
        content.innerHTML = errorHtml;
        modal.show();
    }
    
    async startImport() {
        if (!this.uploadedFile) {
            this.showError('Please select a file first.');
            return;
        }
        
        if (this.validationResults && this.validationResults.error_count > 0) {
            if (!confirm('There are validation errors. Do you want to proceed anyway?')) {
                return;
            }
        }
        
        // Show progress
        document.getElementById('progressContainer').style.display = 'block';
        
        try {
            const formData = new FormData();
            formData.append('csv_file', this.uploadedFile);
            formData.append('module', this.currentModule);
            formData.append('validate_only', document.getElementById('validateOnly').checked);
            formData.append('skip_duplicates', document.getElementById('skipDuplicates').checked);
            formData.append('auto_generate_serials', document.getElementById('autoGenerateSerials').checked);
            formData.append('batch_size', document.getElementById('batchSize').value);
            
            const response = await fetch(`/import-export/import/${this.currentModule}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showImportSuccess(result);
                this.updateStep(5, 'completed');
            } else {
                this.showImportError(result);
                this.updateStep(5, 'error');
            }
            
        } catch (error) {
            console.error('Import error:', error);
            this.showError('Import failed. Please try again.');
            this.updateStep(5, 'error');
        }
    }
    
    showImportSuccess(result) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success';
        alert.innerHTML = `
            <h6><i class="fas fa-check-circle me-2"></i>Import Completed Successfully!</h6>
            <p>Imported ${result.imported_count} records successfully.</p>
            ${result.skipped_count ? `<p>Skipped ${result.skipped_count} duplicate records.</p>` : ''}
        `;
        
        document.getElementById('progressContainer').appendChild(alert);
    }
    
    showImportError(result) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger';
        alert.innerHTML = `
            <h6><i class="fas fa-exclamation-circle me-2"></i>Import Failed</h6>
            <p>${result.message || 'An error occurred during import.'}</p>
        `;
        
        document.getElementById('progressContainer').appendChild(alert);
    }
    
    async downloadTemplate() {
        const options = {
            include_sample: document.getElementById('includeSampleData').checked,
            include_validation: document.getElementById('includeValidationRules').checked,
            auto_populate_serials: document.getElementById('autoPopulateSerials').checked
        };
        
        const params = new URLSearchParams(options);
        window.location.href = `/import-export/template/${this.currentModule}?${params.toString()}`;
    }
    
    async performExport() {
        window.location.href = `/import-export/export/${this.currentModule}`;
    }
    
    async generateSerialSuggestions() {
        const prefix = document.getElementById('serialPrefix').value;
        const format = document.getElementById('serialFormat').value;
        
        try {
            const response = await fetch('/api/serial-suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ prefix, format, count: 5 })
            });
            
            const suggestions = await response.json();
            this.displaySerialSuggestions(suggestions);
            
        } catch (error) {
            console.error('Failed to generate serial suggestions:', error);
        }
    }
    
    displaySerialSuggestions(suggestions) {
        const container = document.getElementById('serialSuggestions');
        
        if (suggestions.length === 0) {
            container.innerHTML = '<p class="text-muted small">No suggestions available</p>';
            return;
        }
        
        let html = '<h6 class="small">Suggestions:</h6>';
        suggestions.forEach(suggestion => {
            html += `<span class="serial-suggestion" onclick="navigator.clipboard.writeText('${suggestion}')">${suggestion}</span>`;
        });
        html += '<p class="text-muted small mt-2">Click to copy to clipboard</p>';
        
        container.innerHTML = html;
    }
    
    updateStep(step, status = 'active') {
        // Reset all steps
        for (let i = 1; i <= 5; i++) {
            const stepEl = document.getElementById(`step-${i}`);
            stepEl.classList.remove('active', 'completed', 'error');
            
            if (i < step) {
                stepEl.classList.add('completed');
            } else if (i === step) {
                stepEl.classList.add(status);
            }
        }
        
        this.currentStep = step;
    }
    
    updateHelpContent(module) {
        const helpContent = document.getElementById('helpContent');
        
        const moduleHelp = {
            assets: {
                fields: 'asset_tag, name, category_name, vendor_name, serial_number, purchase_date, warranty_end, cost, status',
                tips: ['Asset tags must be unique', 'Categories and vendors must exist', 'Use YYYY-MM-DD for dates']
            },
            users: {
                fields: 'first_name, last_name, email, employee_id, department_name, phone, hire_date',
                tips: ['Email addresses must be unique', 'Department must exist', 'Employee IDs should be unique']
            },
            computers: {
                fields: 'asset_tag, name, serial_number, model, cpu, ram, storage, os',
                tips: ['Link to existing asset if applicable', 'Serial numbers should be unique', 'Specify RAM in GB']
            }
        };
        
        const help = moduleHelp[module] || moduleHelp.assets;
        
        helpContent.innerHTML = `
            <h6>${module.charAt(0).toUpperCase() + module.slice(1)} Import</h6>
            <p class="small"><strong>Required fields:</strong> ${help.fields}</p>
            <h6 class="mt-3">Tips</h6>
            <ul class="small">
                ${help.tips.map(tip => `<li>${tip}</li>`).join('')}
            </ul>
        `;
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    showError(message) {
        // Create and show error toast or alert
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
    
    hideValidationResults() {
        document.getElementById('validationSummaryCard').style.display = 'none';
        document.getElementById('validation-results').style.display = 'none';
    }
    
    async loadTemplatePreview() {
        try {
            const response = await fetch(`/api/template-preview/${this.currentModule}`);
            const preview = await response.json();
            
            const previewContainer = document.getElementById('templatePreview');
            
            if (preview.headers && preview.sample_data) {
                let html = '<table class="table table-sm">';
                html += '<thead><tr>';
                preview.headers.forEach(header => {
                    html += `<th class="small">${header}</th>`;
                });
                html += '</tr></thead>';
                
                html += '<tbody>';
                preview.sample_data.slice(0, 3).forEach(row => {
                    html += '<tr>';
                    Object.values(row).forEach(value => {
                        html += `<td class="small text-muted">${value}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody></table>';
                
                previewContainer.innerHTML = html;
            }
        } catch (error) {
            console.error('Failed to load template preview:', error);
        }
    }
}

// Initialize the manager when the page loads
let importManager;
document.addEventListener('DOMContentLoaded', function() {
    importManager = new ImportExportManager();
});
</script>
@endsection