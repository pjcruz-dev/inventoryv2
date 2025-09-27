@extends('layouts.app')

@section('page-title', 'System Health Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-heartbeat me-2"></i>System Health Dashboard
                    </h3>
                    <div>
                        <button class="btn btn-light btn-sm" onclick="refreshMetrics()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <button class="btn btn-outline-light btn-sm" onclick="clearCache()">
                            <i class="fas fa-trash me-1"></i>Clear Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-server fa-2x text-{{ $healthData['system_status']['status'] === 'healthy' ? 'success' : ($healthData['system_status']['status'] === 'warning' ? 'warning' : 'danger') }}"></i>
                    </div>
                    <h5 class="card-title">System Status</h5>
                    <h3 class="text-{{ $healthData['system_status']['status'] === 'healthy' ? 'success' : ($healthData['system_status']['status'] === 'warning' ? 'warning' : 'danger') }}">
                        {{ ucfirst($healthData['system_status']['status']) }}
                    </h3>
                    @if(!empty($healthData['system_status']['issues']))
                        <small class="text-muted">
                            @foreach($healthData['system_status']['issues'] as $issue)
                                <div>{{ $issue }}</div>
                            @endforeach
                        </small>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-memory fa-2x text-info"></i>
                    </div>
                    <h5 class="card-title">Memory Usage</h5>
                    <h3 class="text-{{ $healthData['performance']['memory']['usage_percentage'] > 80 ? 'danger' : ($healthData['performance']['memory']['usage_percentage'] > 60 ? 'warning' : 'success') }}">
                        {{ $healthData['performance']['memory']['usage_percentage'] }}%
                    </h3>
                    <small class="text-muted">
                        {{ number_format($healthData['performance']['memory']['current'] / 1024 / 1024, 2) }}MB / 
                        {{ $healthData['performance']['memory']['limit'] }}
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-database fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">Database</h5>
                    <h3 class="text-{{ $healthData['database']['status'] === 'healthy' ? 'success' : 'danger' }}">
                        {{ ucfirst($healthData['database']['status']) }}
                    </h3>
                    <small class="text-muted">
                        {{ $healthData['database']['response_time'] }}ms response
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title">Errors Today</h5>
                    <h3 class="text-{{ ($healthData['errors'][0]['total_errors'] ?? 0) > 10 ? 'danger' : (($healthData['errors'][0]['total_errors'] ?? 0) > 5 ? 'warning' : 'success') }}">
                        {{ $healthData['errors'][0]['total_errors'] ?? 0 }}
                    </h3>
                    <small class="text-muted">Last 24 hours</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Memory Peak</label>
                                <div class="h5">{{ number_format($healthData['performance']['memory']['peak'] / 1024 / 1024, 2) }}MB</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Database Queries</label>
                                <div class="h5">{{ $healthData['performance']['database']['queries_count'] }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Cache Hits</label>
                                <div class="h5 text-success">{{ $healthData['performance']['cache']['stats']['hits'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Cache Misses</label>
                                <div class="h5 text-warning">{{ $healthData['performance']['cache']['stats']['misses'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Error Statistics (7 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="errorChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    @if(!empty($healthData['recommendations']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Performance Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($healthData['recommendations'] as $recommendation)
                    <div class="alert alert-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'info') }} alert-dismissible fade show">
                        <strong>{{ ucfirst($recommendation['priority']) }} Priority:</strong> {{ $recommendation['message'] }}
                        <br><small class="text-muted">{{ $recommendation['action'] }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Server Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-server me-2"></i>Server Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>PHP Version</strong></td>
                            <td>{{ $healthData['performance']['server']['php_version'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Laravel Version</strong></td>
                            <td>{{ $healthData['performance']['server']['laravel_version'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Load Average</strong></td>
                            <td>{{ implode(', ', $healthData['performance']['server']['load_average']) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Disk Free</strong></td>
                            <td>{{ number_format($healthData['performance']['server']['disk_free'] / 1024 / 1024 / 1024, 2) }}GB</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-database me-2"></i>Database Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                <span class="badge bg-{{ $healthData['database']['status'] === 'healthy' ? 'success' : 'danger' }}">
                                    {{ ucfirst($healthData['database']['status']) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Response Time</strong></td>
                            <td>{{ $healthData['database']['response_time'] }}ms</td>
                        </tr>
                        <tr>
                            <td><strong>Connections</strong></td>
                            <td>{{ $healthData['database']['connections'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Slow Queries</strong></td>
                            <td>{{ count($healthData['database']['slow_queries']) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js" integrity="sha512-9HvCqQx0-4vP9f5Q0KNOB0F7e0ddEhto+loFyXy3F1OwqXhV6D4g6amX/7FhX4JQ9UfF8E6DgO0VlqB4N4qB4C4A==" crossorigin="anonymous"></script>
<script>
// Error Chart
const errorCtx = document.getElementById('errorChart').getContext('2d');
const errorChart = new Chart(errorCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($healthData['errors'])->pluck('date')) !!},
        datasets: [{
            label: 'Total Errors',
            data: {!! json_encode(collect($healthData['errors'])->pluck('total_errors')) !!},
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Critical',
            data: {!! json_encode(collect($healthData['errors'])->pluck('critical')) !!},
            borderColor: '#6f42c1',
            backgroundColor: 'rgba(111, 66, 193, 0.1)',
            tension: 0.4
        }, {
            label: 'High',
            data: {!! json_encode(collect($healthData['errors'])->pluck('high')) !!},
            borderColor: '#fd7e14',
            backgroundColor: 'rgba(253, 126, 20, 0.1)',
            tension: 0.4
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

// Refresh metrics
function refreshMetrics() {
    location.reload();
}

// Clear cache
function clearCache() {
    if (confirm('Are you sure you want to clear all cache?')) {
        fetch('{{ route("system.health.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache cleared successfully');
                location.reload();
            } else {
                alert('Error clearing cache: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error clearing cache: ' + error.message);
        });
    }
}
</script>
@endpush
