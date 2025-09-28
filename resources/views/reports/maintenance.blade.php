@extends('layouts.app')

@section('page-title', 'Maintenance Report')

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-wrench me-2"></i>Maintenance Report
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
                    <form method="GET" action="{{ route('reports.maintenance') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Apply Filter
                                </button>
                                <a href="{{ route('reports.maintenance') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($maintenance['total_maintenance']) }}</h4>
                            <p class="card-text">Total Maintenance</p>
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
                            <h4 class="card-title">{{ number_format($maintenance['pending_maintenance']) }}</h4>
                            <p class="card-text">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="card-title">{{ number_format($maintenance['completed_maintenance']) }}</h4>
                            <p class="card-text">Completed</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ number_format($maintenance['overdue_maintenance']) }}</h4>
                            <p class="card-text">Overdue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="maintenanceTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Maintenance Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assets Requiring Most Maintenance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset Name</th>
                                    <th>Category</th>
                                    <th>Maintenance Count</th>
                                    <th>Total Cost</th>
                                    <th>Last Maintenance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenance['asset_maintenance'] as $asset)
                                <tr>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->category ? $asset->category->name : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $asset->maintenances_count }}</span>
                                    </td>
                                    <td>₱{{ number_format($asset->maintenances->sum('cost'), 2) }}</td>
                                    <td>
                                        @if($asset->maintenances->count() > 0)
                                            {{ $asset->maintenances->first()->created_at->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No maintenance data found.</p>
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

    <!-- Cost Analysis and Technician Performance -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset</th>
                                    <th>Count</th>
                                    <th>Total Cost</th>
                                    <th>Avg Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenance['cost_analysis'] as $analysis)
                                <tr>
                                    <td>{{ $analysis->asset ? $analysis->asset->name : 'Unknown Asset' }}</td>
                                    <td>{{ $analysis->maintenance_count }}</td>
                                    <td>₱{{ number_format($analysis->total_cost, 2) }}</td>
                                    <td>₱{{ number_format($analysis->avg_cost, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No cost analysis data found.</p>
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
                    <h5 class="mb-0">Vendor Performance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Vendor</th>
                                    <th>Count</th>
                                    <th>Total Cost</th>
                                    <th>Avg Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenance['technician_performance'] as $vendor)
                                <tr>
                                    <td>{{ $vendor['technician'] }}</td>
                                    <td>{{ $vendor['maintenance_count'] }}</td>
                                    <td>₱{{ number_format($vendor['total_cost'], 2) }}</td>
                                    <td>₱{{ number_format($vendor['avg_cost'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No vendor performance data found.</p>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" crossorigin="anonymous"></script>
<script>
// Maintenance Trends Chart
const maintenanceTrendsCtx = document.getElementById('maintenanceTrendsChart').getContext('2d');
const maintenanceTrendsChart = new Chart(maintenanceTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($maintenance['maintenance_trends']->pluck('month')->toArray()) !!},
        datasets: [{
            label: 'Maintenance Count',
            data: {!! json_encode($maintenance['maintenance_trends']->pluck('count')->toArray()) !!},
            borderColor: '#36A2EB',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.4
        }, {
            label: 'Total Cost',
            data: {!! json_encode($maintenance['maintenance_trends']->pluck('total_cost')->toArray()) !!},
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
    formData.append('report_type', 'maintenance');
    formData.append('format', 'csv');
    formData.append('date_from', '{{ $dateFrom->format('Y-m-d') }}');
    formData.append('date_to', '{{ $dateTo->format('Y-m-d') }}');
    
    // Submit form
    fetch('{{ route("reports.export") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.blob();
    })
    .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'maintenance_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
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
