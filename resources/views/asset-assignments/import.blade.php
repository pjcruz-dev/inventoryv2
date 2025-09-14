@extends('layouts.app')

@section('title', 'Import Asset Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-import me-2"></i>Import Asset Assignments
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('asset-assignments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('asset-assignments.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="file" class="form-label">Select Excel File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Supported formats: Excel (.xlsx, .xls) and CSV (.csv). Maximum file size: 2MB.
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i>Import Assignments
                                    </button>
                                    <a href="{{ route('asset-assignments.download-template') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-download me-1"></i>Download Template
                                    </a>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Import Instructions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h6>Required Columns:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i><strong>Asset Tag</strong> - Must exist in system</li>
                                        <li><i class="fas fa-check text-success me-2"></i><strong>User Email</strong> - Must exist in system</li>
                                    </ul>
                                    
                                    <h6>Optional Columns:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-minus text-muted me-2"></i>Assigned Date (YYYY-MM-DD)</li>
                                        <li><i class="fas fa-minus text-muted me-2"></i>Return Date (YYYY-MM-DD)</li>
                                        <li><i class="fas fa-minus text-muted me-2"></i>Notes</li>
                                    </ul>
                                    
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Note:</strong> Existing assignments will be updated. Assets will be marked as 'assigned' automatically.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // File input validation
    $('#file').on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                                'application/vnd.ms-excel', 'text/csv'];
            
            if (fileSize > 2) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
                alert('Please select a valid Excel or CSV file');
                this.value = '';
                return;
            }
        }
    });
    
    // Form submission confirmation
    $('form').on('submit', function(e) {
        const file = $('#file')[0].files[0];
        if (!file) {
            e.preventDefault();
            alert('Please select a file to import');
            return;
        }
        
        if (!confirm('Are you sure you want to import these asset assignments? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush