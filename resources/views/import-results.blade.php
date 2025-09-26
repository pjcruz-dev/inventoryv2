@extends('layouts.app')

@section('title', 'Import Results')
@section('page-title', 'Import Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-import me-2"></i>Import Results
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('import_success'))
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>Import Successful</h6>
                            <p class="mb-0">{{ session('import_success') }}</p>
                        </div>
                    @endif

                    @if(session('validation_message'))
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Validation Results</h6>
                            <p class="mb-0">{{ session('validation_message') }}</p>
                        </div>
                    @endif

                    @if(session('import_summary'))
                        @php $summary = session('import_summary'); @endphp
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ $summary['total'] ?? 0 }}</h3>
                                        <small>Total Records</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ $summary['successful'] ?? 0 }}</h3>
                                        <small>Successful</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ $summary['failed'] ?? 0 }}</h3>
                                        <small>Failed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ $summary['warnings'] ?? 0 }}</h3>
                                        <small>Warnings</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Import Errors</h6>
                            <p class="mb-3">The following errors occurred during import:</p>
                            
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Field</th>
                                            <th>Error Message</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('import_errors') as $error)
                                        <tr>
                                            <td>{{ $error['row'] ?? 'N/A' }}</td>
                                            <td>{{ $error['field'] ?? 'N/A' }}</td>
                                            <td>{{ $error['message'] ?? 'Unknown error' }}</td>
                                            <td>
                                                <code>{{ Str::limit($error['value'] ?? '', 50) }}</code>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(session('import_warnings') && count(session('import_warnings')) > 0)
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Import Warnings</h6>
                            <p class="mb-3">The following warnings occurred during import:</p>
                            
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Field</th>
                                            <th>Warning Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('import_warnings') as $warning)
                                        <tr>
                                            <td>{{ $warning['row'] ?? 'N/A' }}</td>
                                            <td>{{ $warning['field'] ?? 'N/A' }}</td>
                                            <td>{{ $warning['message'] ?? 'Unknown warning' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('import-export.interface') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Import/Export
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection