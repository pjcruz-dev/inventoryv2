@extends('layouts.app')

@section('title', 'Export Assets')
@section('page-title', 'Export Assets')

@section('styles')
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 0.5rem;
        pointer-events: none;
    }
    
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 0.3rem;
        outline: 0;
    }
    
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }
    
    .modal-backdrop.fade {
        opacity: 0;
    }
    
    .modal-backdrop.show {
        opacity: 0.5;
    }
    
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
    }
    
    @media (min-width: 992px) {
        .modal-dialog-xl {
            max-width: 1140px;
        }
    }
</style>
@endsection

@section('page-actions')
    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Back to Assets
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Export Options -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>
                        Export Options
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Export {{ $assets->count() }} assets in your preferred format. 
                        You can customize the export options below.
                    </p>
                    
                    <form id="exportForm" method="GET">
                        <!-- Export Format -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Export Format</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="format" id="formatExcel" value="excel" checked>
                                    <label class="btn btn-outline-success" for="formatExcel">
                                        <i class="fas fa-file-excel me-2"></i>Excel
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="format" id="formatPdf" value="pdf">
                                    <label class="btn btn-outline-danger" for="formatPdf">
                                        <i class="fas fa-file-pdf me-2"></i>PDF
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="format" id="formatCsv" value="csv">
                                    <label class="btn btn-outline-primary" for="formatCsv">
                                        <i class="fas fa-file-csv me-2"></i>CSV
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Export Options -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeImages" name="include_images">
                                    <label class="form-check-label" for="includeImages">
                                        Include Asset Images
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeQrCodes" name="include_qr_codes">
                                    <label class="form-check-label" for="includeQrCodes">
                                        Include QR Codes
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Template Selection (for PDF) -->
                        <div class="row mb-4" id="templateSection" style="display: none;">
                            <div class="col-md-6">
                                <label for="template" class="form-label">PDF Template</label>
                                <select class="form-select" id="template" name="template">
                                    <option value="default">Default Template</option>
                                    <option value="detailed">Detailed Template</option>
                                    <option value="minimal">Minimal Template</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Export Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="exportBtn">
                                <i class="fas fa-download me-2"></i>Export Assets
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="previewExport()">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Export Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Export Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-primary">{{ $assets->count() }}</div>
                                <small class="text-muted">Total Assets</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-success">{{ $assets->where('status', 'Active')->count() }}</div>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ $assets->where('status', 'Under Maintenance')->count() }}</div>
                                <small class="text-muted">Maintenance</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-danger">{{ $assets->where('status', 'Issue Reported')->count() }}</div>
                                <small class="text-muted">Issues</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('assets.export.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-2"></i>Quick Excel Export
                        </a>
                        <a href="{{ route('assets.export.pdf') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf me-2"></i>Quick PDF Export
                        </a>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Asset Preview Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Asset Preview ({{ $assets->count() }} assets)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tag</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets->take(10) as $asset)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $asset->tag }}</span>
                                    </td>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $asset->status === 'Active' ? 'success' : ($asset->status === 'Issue Reported' ? 'danger' : 'warning') }}">
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                    <td>{{ $asset->assignedUser->name ?? 'Unassigned' }}</td>
                                    <td>{{ $asset->location ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox me-2"></i>No assets found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($assets->count() > 10)
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing first 10 of {{ $assets->count() }} assets. 
                            All assets will be included in the export.
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatRadios = document.querySelectorAll('input[name="format"]');
    const templateSection = document.getElementById('templateSection');
    const exportForm = document.getElementById('exportForm');
    const exportBtn = document.getElementById('exportBtn');
    
    // Show/hide template section based on format
    formatRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'pdf') {
                templateSection.style.display = 'block';
            } else {
                templateSection.style.display = 'none';
            }
        });
    });
    
    // Handle form submission
    exportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedFormat = document.querySelector('input[name="format"]:checked').value;
        const formData = new FormData(this);
        
        // Build the export URL
        let exportUrl = '';
        switch(selectedFormat) {
            case 'excel':
                exportUrl = '{{ route("assets.export.excel") }}';
                break;
            case 'pdf':
                exportUrl = '{{ route("assets.export.pdf") }}';
                break;
            case 'csv':
                exportUrl = '{{ route("assets.export.csv") }}';
                break;
        }
        
        // Add form parameters to URL
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            if (key !== 'format') {
                params.append(key, value);
            }
        }
        
        if (params.toString()) {
            exportUrl += '?' + params.toString();
        }
        
        // Show loading state
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
        exportBtn.disabled = true;
        
        // Redirect to export URL
        window.location.href = exportUrl;
    });
});

function previewExport() {
    // Show a preview modal with sample data
    const selectedFormat = document.querySelector('input[name="format"]:checked').value;
    const includeImages = document.getElementById('includeImages').checked;
    const includeQrCodes = document.getElementById('includeQrCodes').checked;
    
    // Create preview modal
    const modalHtml = `
        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">
                            <i class="fas fa-eye me-2"></i>Export Preview - ${selectedFormat.toUpperCase()}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Export Format:</strong> ${selectedFormat.toUpperCase()}
                            </div>
                            <div class="col-md-6">
                                <strong>Total Assets:</strong> {{ $assets->count() }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Include Images:</strong> ${includeImages ? 'Yes' : 'No'}
                            </div>
                            <div class="col-md-6">
                                <strong>Include QR Codes:</strong> ${includeQrCodes ? 'Yes' : 'No'}
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6>Sample Data (First 5 Assets):</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tag</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Location</th>
                                        ${includeImages ? '<th>Image</th>' : ''}
                                        ${includeQrCodes ? '<th>QR Code</th>' : ''}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assets->take(5) as $asset)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $asset->tag }}</span></td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status === 'Active' ? 'success' : ($asset->status === 'Issue Reported' ? 'danger' : 'warning') }}">
                                                {{ $asset->status }}
                                            </span>
                                        </td>
                                        <td>{{ $asset->assignedUser->name ?? 'Unassigned' }}</td>
                                        <td>{{ $asset->location ?? 'N/A' }}</td>
                                        ${includeImages ? '<td>' + ({{ $asset->hasImage() ? 'true' : 'false' }} ? '<i class="fas fa-image text-success"></i>' : '<i class="fas fa-times text-muted"></i>') + '</td>' : ''}
                                        ${includeQrCodes ? '<td><i class="fas fa-qrcode text-primary"></i></td>' : ''}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($assets->count() > 5)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Showing first 5 of {{ $assets->count() }} assets. All assets will be included in the export.
                        </div>
                        @endif
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> This is a preview of the data structure. The actual export may have different formatting depending on the selected format.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('exportForm').submit();">
                            <i class="fas fa-download me-2"></i>Export Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('previewModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    } else {
        // Fallback: show as alert if Bootstrap is not available
        const modalElement = document.getElementById('previewModal');
        if (modalElement) {
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            modalElement.setAttribute('aria-modal', 'true');
            modalElement.setAttribute('role', 'dialog');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'previewBackdrop';
            document.body.appendChild(backdrop);
            
            // Close modal when clicking backdrop
            backdrop.addEventListener('click', function() {
                closeModal();
            });
            
            // Close modal when clicking close button
            const closeBtn = modalElement.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }
        }
    }
    
    function closeModal() {
        const modalElement = document.getElementById('previewModal');
        const backdrop = document.getElementById('previewBackdrop');
        
        if (modalElement) {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            modalElement.removeAttribute('aria-modal');
            modalElement.removeAttribute('role');
        }
        
        if (backdrop) {
            backdrop.remove();
        }
    }
}
</script>
@endsection
