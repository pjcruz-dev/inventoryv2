@extends('layouts.app')

@section('title', 'Import Asset Categories')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-import me-2"></i>Import Asset Categories
                    </h3>
                    <a href="{{ route('asset-categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Categories
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-categories.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="file" class="form-label">
                                        Select Excel File <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('file') is-invalid @enderror" 
                                           id="file" 
                                           name="file" 
                                           accept=".xlsx,.xls,.csv"
                                           required>
                                    @error('file')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        Supported formats: Excel (.xlsx, .xls) and CSV (.csv). Maximum file size: 2MB.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Preview -->
                        <div id="filePreview" class="mb-4" style="display: none;">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-file-excel me-2"></i>Selected File</h6>
                                <div id="fileInfo"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('asset-categories.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="importButton" disabled>
                                        <i class="fas fa-upload me-1"></i>Import Categories
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Template Download Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-download me-2"></i>Download Template
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Download the Excel template to ensure your data is formatted correctly for import.
                    </p>
                    <a href="{{ route('asset-categories.download-template') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-download me-1"></i>Download Template
                    </a>
                </div>
            </div>
            
            <!-- Import Instructions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Import Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-list-ol text-primary me-2"></i>Required Columns</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i><strong>name</strong> - Category name (required, max 100 chars)</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i><strong>description</strong> - Category description (optional, max 1000 chars)</li>
                            </ul>
                            
                            <h6 class="mt-4"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Important Notes</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Category names must be unique</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Existing categories will be updated</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Empty rows will be skipped</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-2"></i>Best Practices</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Use the provided template</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Keep category names descriptive</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Add meaningful descriptions</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Review data before importing</li>
                            </ul>
                            
                            <h6 class="mt-4"><i class="fas fa-file-excel text-info me-2"></i>Example Data</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>name</th>
                                            <th>description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Computers</td>
                                            <td>Desktop and laptop computers</td>
                                        </tr>
                                        <tr>
                                            <td>Network Equipment</td>
                                            <td>Routers, switches, and network devices</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Importing Categories...</h5>
                <p class="text-muted mb-0">Please wait while we process your file.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // File input handling
    document.getElementById('file').addEventListener('change', function() {
        const file = this.files[0];
        const importButton = document.getElementById('importButton');
        const filePreview = document.getElementById('filePreview');
        const fileInfo = document.getElementById('fileInfo');
        
        if (file) {
            // Validate file type
            const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                                'application/vnd.ms-excel', 
                                'text/csv'];
            
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid Excel or CSV file.');
                this.value = '';
                importButton.disabled = true;
                filePreview.style.display = 'none';
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB.');
                this.value = '';
                importButton.disabled = true;
                filePreview.style.display = 'none';
                return;
            }
            
            // Show file info
            fileInfo.innerHTML = `
                <strong>Name:</strong> ${file.name}<br>
                <strong>Size:</strong> ${(file.size / 1024).toFixed(2)} KB<br>
                <strong>Type:</strong> ${file.type || 'Unknown'}
            `;
            
            filePreview.style.display = 'block';
            importButton.disabled = false;
        } else {
            importButton.disabled = true;
            filePreview.style.display = 'none';
        }
    });
    
    // Form submission
    document.getElementById('importForm').addEventListener('submit', function() {
        // Show loading modal
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        loadingModal.show();
        
        // Disable form elements
        const formElements = this.querySelectorAll('input, button');
        formElements.forEach(element => {
            element.disabled = true;
        });
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush