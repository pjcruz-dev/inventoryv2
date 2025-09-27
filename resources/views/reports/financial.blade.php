@extends('layouts.app')

@section('page-title', 'Financial Report')

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-dollar-sign me-2"></i>Financial Report
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
                    <form method="GET" action="{{ route('reports.financial') }}" class="row g-3">
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
                                <a href="{{ route('reports.financial') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">₱{{ number_format($financial['total_investment'], 2) }}</h4>
                            <p class="card-text">Total Investment</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <h4 class="card-title">₱{{ number_format($financial['maintenance_costs']->sum('total_cost'), 2) }}</h4>
                            <p class="card-text">Maintenance Costs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wrench fa-2x"></i>
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
                            <h4 class="card-title">₱{{ number_format($financial['disposal_value']->sum('total_value'), 2) }}</h4>
                            <p class="card-text">Disposal Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-recycle fa-2x"></i>
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
                            <h4 class="card-title">₱{{ number_format($financial['depreciation']->sum('depreciation_amount'), 2) }}</h4>
                            <p class="card-text">Total Depreciation</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Investment Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Investment Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="investmentChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown Charts -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Category Costs</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryCostsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Department Costs</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentCostsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Vendor Costs</h5>
                </div>
                <div class="card-body">
                    <canvas id="vendorCostsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Depreciation Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Asset Depreciation</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset Name</th>
                                    <th>Original Cost</th>
                                    <th>Days Owned</th>
                                    <th>Annual Depreciation</th>
                                    <th>Current Value</th>
                                    <th>Depreciation Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financial['depreciation'] as $asset)
                                <tr>
                                    <td>{{ $asset->name }}</td>
                                    <td>₱{{ number_format($asset->cost, 2) }}</td>
                                    <td>{{ number_format($asset->days_owned) }}</td>
                                    <td>₱{{ number_format($asset->annual_depreciation, 2) }}</td>
                                    <td>₱{{ number_format($asset->current_value, 2) }}</td>
                                    <td>₱{{ number_format($asset->depreciation_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No depreciation data found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Analysis Tables -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Category Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Category</th>
                                    <th>Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financial['category_costs'] as $category)
                                <tr>
                                    <td>{{ $category['category'] }}</td>
                                    <td>₱{{ number_format($category['total_cost'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No category cost data found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Department Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Department</th>
                                    <th>Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financial['department_costs'] as $department)
                                <tr>
                                    <td>{{ $department['department'] }}</td>
                                    <td>₱{{ number_format($department['total_cost'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No department cost data found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
// Monthly Investment Chart
const investmentCtx = document.getElementById('investmentChart').getContext('2d');
const investmentChart = new Chart(investmentCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($financial['monthly_investment']->pluck('month')) !!},
        datasets: [{
            label: 'Monthly Investment',
            data: {!! json_encode($financial['monthly_investment']->pluck('total_investment')) !!},
            borderColor: '#36A2EB',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Category Costs Chart
const categoryCostsCtx = document.getElementById('categoryCostsChart').getContext('2d');
const categoryCostsChart = new Chart(categoryCostsCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($financial['category_costs']->pluck('category')) !!},
        datasets: [{
            data: {!! json_encode($financial['category_costs']->pluck('total_cost')) !!},
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

// Department Costs Chart
const departmentCostsCtx = document.getElementById('departmentCostsChart').getContext('2d');
const departmentCostsChart = new Chart(departmentCostsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($financial['department_costs']->pluck('department')) !!},
        datasets: [{
            label: 'Cost',
            data: {!! json_encode($financial['department_costs']->pluck('total_cost')) !!},
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

// Vendor Costs Chart
const vendorCostsCtx = document.getElementById('vendorCostsChart').getContext('2d');
const vendorCostsChart = new Chart(vendorCostsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($financial['vendor_costs']->pluck('vendor')) !!},
        datasets: [{
            label: 'Cost',
            data: {!! json_encode($financial['vendor_costs']->pluck('total_cost')) !!},
            backgroundColor: '#FF6384'
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

function exportReport() {
    // Show loading state
    const exportBtn = document.querySelector('[onclick="exportReport()"]');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Exporting...';
    exportBtn.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('report_type', 'financial');
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
