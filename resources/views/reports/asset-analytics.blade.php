@extends('layouts.app')

@section('page-title', 'Asset Analytics Report')

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-chart-line me-2"></i>Asset Analytics Report
                    </h3>
                    <div>
                        <button class="btn btn-light btn-sm" onclick="exportReport()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.asset-analytics') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Apply Filter
                                </button>
                                <a href="{{ route('reports.asset-analytics') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($analytics['total_assets']) }}</h4>
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
                            <h4 class="card-title">{{ number_format($analytics['active_assets']) }}</h4>
                            <p class="card-text">Active Assets</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="card-title">₱{{ number_format($analytics['total_value'], 2) }}</h4>
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
                            <h4 class="card-title">₱{{ number_format($analytics['average_value'], 2) }}</h4>
                            <p class="card-text">Average Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assets by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assets by Department</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Asset Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Assets by Value</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset Name</th>
                                    <th>Category</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['top_assets'] as $asset)
                                <tr>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->category ? $asset->category->name : 'N/A' }}</td>
                                    <td>₱{{ number_format($asset->cost, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Category Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Category</th>
                                    <th>Count</th>
                                    <th>Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['category_breakdown'] as $category)
                                <tr>
                                    <td>{{ $category['category'] }}</td>
                                    <td>{{ $category['count'] }}</td>
                                    <td>₱{{ number_format($category['total_value'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Summary -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <h4 class="text-primary">{{ $analytics['maintenance_summary']['total'] }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-warning">{{ $analytics['maintenance_summary']['pending'] }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-success">{{ $analytics['maintenance_summary']['completed'] }}</h4>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-danger">{{ $analytics['maintenance_summary']['overdue'] }}</h4>
                            <small class="text-muted">Overdue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Disposal Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $analytics['disposal_summary']['total'] }}</h4>
                            <small class="text-muted">Total Disposed</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">₱{{ number_format($analytics['disposal_summary']['total_value'], 2) }}</h4>
                            <small class="text-muted">Total Value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js" integrity="sha512-9HvCqQx0-4vP9f5Q0KNOB0F7e0ddEhto+loFyXy3F1OwqXhV6D4g6amX/7FhX4JQ9UfF8E6DgO0VlqB4N4qB4C4A==" crossorigin="anonymous"></script>
<script>
// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($analytics['category_breakdown']->pluck('category')) !!},
        datasets: [{
            data: {!! json_encode($analytics['category_breakdown']->pluck('count')) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Department Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
const departmentChart = new Chart(departmentCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($analytics['department_breakdown']->pluck('department')) !!},
        datasets: [{
            label: 'Asset Count',
            data: {!! json_encode($analytics['department_breakdown']->pluck('count')) !!},
            backgroundColor: '#36A2EB'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Trends Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
const trendsChart = new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analytics['monthly_trends']->pluck('month')) !!},
        datasets: [{
            label: 'Asset Count',
            data: {!! json_encode($analytics['monthly_trends']->pluck('count')) !!},
            borderColor: '#36A2EB',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4
        }, {
            label: 'Total Value',
            data: {!! json_encode($analytics['monthly_trends']->pluck('total_value')) !!},
            borderColor: '#FF6384',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

function exportReport() {
    // Show loading state
    const exportBtn = document.querySelector('[onclick="exportReport()"]');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Exporting...';
    exportBtn.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('report_type', 'asset_analytics');
    formData.append('format', 'csv');
    formData.append('date_from', '{{ $dateFrom }}');
    formData.append('date_to', '{{ $dateTo }}');
    
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
    });
}
</script>
@endpush
