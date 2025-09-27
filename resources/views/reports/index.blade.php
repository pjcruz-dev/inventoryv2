@extends('layouts.app')

@section('page-title', 'Reports & Analytics')

@section('content')
<div class="container-fluid">
    <!-- Reports Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-chart-bar me-2"></i>Reports & Analytics Dashboard
                    </h3>
                    <div>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download me-1"></i>Export All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($stats['total_assets']) }}</h4>
                            <p class="card-text">Total Assets</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($stats['total_users']) }}</h4>
                            <p class="card-text">Total Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">₱{{ number_format($stats['total_value'], 2) }}</h4>
                            <p class="card-text">Total Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($stats['reports_generated']) }}</h4>
                            <p class="card-text">Reports Generated</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Report Categories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categories as $key => $category)
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('reports.' . $key) }}" class="text-decoration-none">
                                <div class="card h-100 text-center border-0 shadow-sm">
                                    <div class="card-body">
                                        <i class="{{ $category['icon'] }} fa-3x text-{{ $category['color'] }} mb-3"></i>
                                        <h6 class="card-title">{{ $category['name'] }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Reports</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('reports.asset-analytics') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                Asset Analytics
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="{{ route('reports.user-activity') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-users text-success me-2"></i>
                                User Activity
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="{{ route('reports.financial') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-dollar-sign text-warning me-2"></i>
                                Financial Report
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="{{ route('reports.maintenance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-wrench text-info me-2"></i>
                                Maintenance Report
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Reports</h5>
                </div>
                <div class="card-body">
                    @if($recentReports->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentReports as $report)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $report['name'] }}</h6>
                                    <small class="text-muted">{{ $report['generated_at']->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $categories[$report['type']]['color'] }}">
                                    {{ $categories[$report['type']]['name'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent reports available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Analytics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Advanced Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                                    <h6>Asset Distribution</h6>
                                    <p class="text-muted">View assets by category, department, and status</p>
                                    <a href="{{ route('reports.asset-analytics') }}" class="btn btn-primary btn-sm">View Report</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                    <h6>Trend Analysis</h6>
                                    <p class="text-muted">Analyze trends over time and patterns</p>
                                    <a href="{{ route('reports.user-activity') }}" class="btn btn-success btn-sm">View Report</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                                    <h6>Performance Metrics</h6>
                                    <p class="text-muted">Track key performance indicators</p>
                                    <a href="{{ route('reports.financial') }}" class="btn btn-warning btn-sm">View Report</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType" name="report_type" required>
                            <option value="">Select Report Type</option>
                            <option value="asset_analytics">Asset Analytics</option>
                            <option value="user_activity">User Activity</option>
                            <option value="financial">Financial Report</option>
                            <option value="maintenance">Maintenance Report</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="format" class="form-label">Format</label>
                        <select class="form-select" id="format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dateFrom" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="dateFrom" name="date_from">
                    </div>
                    <div class="mb-3">
                        <label for="dateTo" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="dateTo" name="date_to">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="exportReport()">Export</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportReport() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    
    // Show loading state
    const exportBtn = document.querySelector('[onclick="exportReport()"]');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Exporting...';
    exportBtn.disabled = true;
    
    // Submit form
    fetch('{{ route("reports.export") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Export failed. Please try again.');
    })
    .finally(() => {
        // Reset button state
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
    });
}
</script>
@endpush
