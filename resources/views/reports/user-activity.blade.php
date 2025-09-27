@extends('layouts.app')

@section('page-title', 'User Activity Report')

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-users me-2"></i>User Activity Report
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
                    <form method="GET" action="{{ route('reports.user-activity') }}" class="row g-3">
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
                                <a href="{{ route('reports.user-activity') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Active Users -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Most Active Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Department</th>
                                    <th>Activity Count</th>
                                    <th>Last Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userActivity['most_active_users'] as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->department ? $user->department->name : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->audit_logs_count }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->auditLogs->first() ? $user->auditLogs->first()->created_at->diffForHumans() : 'N/A' }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No user activity found for the selected period.</p>
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

    <!-- Activity Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Login Patterns</h5>
                </div>
                <div class="card-body">
                    <canvas id="loginChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Peak Hours</h5>
                </div>
                <div class="card-body">
                    <canvas id="peakHoursChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Activity -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Department Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Department</th>
                                    <th>Active Users</th>
                                    <th>Activity Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userActivity['department_activity'] as $department)
                                <tr>
                                    <td>
                                        <strong>{{ $department->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $department->users_count }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ min(100, ($department->users_count / max(1, $userActivity['department_activity']->max('users_count'))) * 100) }}%">
                                                {{ $department->users_count }} users
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No department activity found.</p>
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

    <!-- Action Breakdown -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Action Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Action</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userActivity['action_breakdown'] as $action)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($action->action) }}">
                                            {{ ucwords(str_replace('_', ' ', $action->action)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($action->count) }}</strong>
                                    </td>
                                    <td>
                                        {{ number_format(($action->count / $userActivity['action_breakdown']->sum('count')) * 100, 1) }}%
                                    </td>
                                    <td>
                                        <i class="fas fa-chart-line text-success"></i>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No action data found.</p>
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
// Login Patterns Chart
const loginCtx = document.getElementById('loginChart').getContext('2d');
const loginChart = new Chart(loginCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($userActivity['login_patterns']->pluck('date')) !!},
        datasets: [{
            label: 'Login Count',
            data: {!! json_encode($userActivity['login_patterns']->pluck('login_count')) !!},
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
                beginAtZero: true
            }
        }
    }
});

// Peak Hours Chart
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
const peakChart = new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($userActivity['peak_hours']->pluck('hour')->map(function($h) { return $h . ':00'; })) !!},
        datasets: [{
            label: 'Activity Count',
            data: {!! json_encode($userActivity['peak_hours']->pluck('activity_count')) !!},
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
    formData.append('report_type', 'user_activity');
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
