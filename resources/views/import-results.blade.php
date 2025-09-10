@extends('layouts.app')

@section('title', 'Import Results')
@section('page-title', 'Import Results')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('import_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Import Successful!</strong> {{ session('import_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('import_errors'))
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Import Errors Found
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            The following errors were encountered during the import process. Please fix these issues and try again.
                        </p>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Field</th>
                                        <th>Error</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(session('import_errors') as $error)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $error['row'] ?? 'N/A' }}</span></td>
                                            <td><code>{{ $error['field'] ?? 'General' }}</code></td>
                                            <td>{{ $error['message'] }}</td>
                                            <td>
                                                @if(isset($error['value']))
                                                    <span class="text-muted">{{ Str::limit($error['value'], 50) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Go Back
                            </a>
                            <a href="{{ route('import-export.template', request()->segment(2)) }}" class="btn btn-outline-success">
                                <i class="fas fa-download me-2"></i>Download Template
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('import_warnings'))
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Import Warnings
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            The import was successful, but the following warnings were generated:
                        </p>
                        
                        <ul class="list-group list-group-flush">
                            @foreach(session('import_warnings') as $warning)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Row {{ $warning['row'] ?? 'N/A' }}</div>
                                        {{ $warning['message'] }}
                                    </div>
                                    @if(isset($warning['field']))
                                        <span class="badge bg-warning rounded-pill">{{ $warning['field'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(session('import_summary'))
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Import Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-primary">{{ session('import_summary')['total'] ?? 0 }}</h3>
                                    <small class="text-muted">Total Rows</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-success">{{ session('import_summary')['successful'] ?? 0 }}</h3>
                                    <small class="text-muted">Successful</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-danger">{{ session('import_summary')['failed'] ?? 0 }}</h3>
                                    <small class="text-muted">Failed</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-warning">{{ session('import_summary')['warnings'] ?? 0 }}</h3>
                                    <small class="text-muted">Warnings</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!session('import_success') && !session('import_errors') && !session('import_warnings'))
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-import fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Import Results</h5>
                        <p class="text-muted">No import operation has been performed yet.</p>
                        <a href="{{ url()->previous() }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-dismiss success alerts after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
</script>
@endsection