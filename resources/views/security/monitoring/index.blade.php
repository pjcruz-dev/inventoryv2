@extends('layouts.app')

@section('title', 'Security Monitoring')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">Security Monitoring Dashboard</h5>
                            <small class="text-white-50">Real-time security threat detection and monitoring</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm" onclick="refreshDashboard()" style="color: #667eea;">
                                    <i class="fas fa-sync-alt me-1"></i>Refresh
                                </button>
                                <button class="btn btn-light btn-sm" onclick="runMonitoring()" style="color: #667eea;">
                                    <i class="fas fa-play me-1"></i>Run Monitoring
                                </button>
                                <button class="btn btn-light btn-sm" onclick="clearBlocks()" style="color: #667eea;">
                                    <i class="fas fa-unlock me-1"></i>Clear Blocks
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Score and Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Security Score
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="security-score">
                                {{ $dashboardData['security_score'] ?? 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Threats Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="threats-today">
                                {{ $dashboardData['threats_today'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Blocked IPs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="blocked-ips">
                                {{ $dashboardData['blocked_ips'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Failed Logins (1h)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="failed-logins">
                                {{ $dashboardData['failed_logins'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Recent Threats -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Recent Security Threats
                    </h6>
                    <button class="btn btn-sm btn-outline-light" onclick="refreshThreats()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="threats-container">
                        @if(isset($dashboardData['recent_threats']) && count($dashboardData['recent_threats']) > 0)
                            @foreach($dashboardData['recent_threats'] as $threat)
                                <div class="alert alert-{{ $threat['severity'] === 'high' ? 'danger' : ($threat['severity'] === 'medium' ? 'warning' : 'info') }} mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="alert-heading mb-1">
                                                <i class="fas fa-{{ $threat['type'] === 'brute_force' ? 'fist-raised' : ($threat['type'] === 'unauthorized_access' ? 'user-secret' : 'exclamation-triangle') }} me-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $threat['type'])) }}
                                            </h6>
                                            <p class="mb-1">{{ $threat['message'] ?? 'No description available' }}</p>
                                            <small class="text-muted">
                                                Count: {{ $threat['count'] ?? 'N/A' }} | 
                                                Severity: {{ ucfirst($threat['severity']) }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $threat['severity'] === 'high' ? 'danger' : ($threat['severity'] === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($threat['severity']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent threats detected</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Recommendations -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-lightbulb me-2"></i>
                        Security Recommendations
                    </h6>
                </div>
                <div class="card-body">
                    <div id="recommendations-container">
                        @if(isset($dashboardData['recommendations']) && count($dashboardData['recommendations']) > 0)
                            @foreach($dashboardData['recommendations'] as $recommendation)
                                <div class="mb-3 p-3 border-start border-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'info') }} border-3">
                                    <h6 class="mb-1 text-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $recommendation['type'])) }}
                                    </h6>
                                    <p class="mb-1 small">{{ $recommendation['message'] }}</p>
                                    <small class="text-muted">{{ $recommendation['action'] }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">No recommendations at this time</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Events Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-list me-2"></i>
                        Security Events
                    </h6>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="event-filter">
                            <option value="">All Events</option>
                            <option value="login_failed">Failed Logins</option>
                            <option value="suspicious_activity">Suspicious Activity</option>
                            <option value="unauthorized_access">Unauthorized Access</option>
                            <option value="password_change">Password Changes</option>
                            <option value="permission_denied">Permission Denied</option>
                            <option value="file_upload">File Uploads</option>
                            <option value="data_export">Data Exports</option>
                        </select>
                        <button class="btn btn-sm btn-outline-light" onclick="refreshEvents()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="security-events-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="events-tbody">
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Security Monitoring Scripts -->
<script>
let refreshInterval;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadSecurityEvents();
    startAutoRefresh();
});

// Auto refresh every 30 seconds
function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        refreshDashboard();
    }, 30000);
}

// Refresh dashboard data
function refreshDashboard() {
    fetch('/security/monitoring/threats', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            updateThreatsDisplay(data.threats);
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
        });
}

// Refresh threats display
function updateThreatsDisplay(threats) {
    const container = document.getElementById('threats-container');
    
    if (threats.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">No recent threats detected</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = threats.map(threat => `
        <div class="alert alert-${threat.severity === 'high' ? 'danger' : (threat.severity === 'medium' ? 'warning' : 'info')} mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="alert-heading mb-1">
                        <i class="fas fa-${getThreatIcon(threat.type)} me-2"></i>
                        ${threat.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                    </h6>
                    <p class="mb-1">${threat.message || 'No description available'}</p>
                    <small class="text-muted">
                        Count: ${threat.count || 'N/A'} | 
                        Severity: ${threat.severity.charAt(0).toUpperCase() + threat.severity.slice(1)}
                    </small>
                </div>
                <span class="badge bg-${threat.severity === 'high' ? 'danger' : (threat.severity === 'medium' ? 'warning' : 'info')}">
                    ${threat.severity.charAt(0).toUpperCase() + threat.severity.slice(1)}
                </span>
            </div>
        </div>
    `).join('');
}

// Get threat icon
function getThreatIcon(type) {
    const icons = {
        'brute_force': 'fist-raised',
        'unauthorized_access': 'user-secret',
        'suspicious_activity': 'exclamation-triangle',
        'data_exfiltration': 'download'
    };
    return icons[type] || 'exclamation-triangle';
}

// Load security events
function loadSecurityEvents() {
    const filter = document.getElementById('event-filter').value;
    const params = new URLSearchParams();
    if (filter) params.append('action', filter);
    
    fetch(`/security/monitoring/events?${params}`)
        .then(response => response.json())
        .then(data => {
            updateEventsTable(data.events.data);
        })
        .catch(error => {
            console.error('Error loading events:', error);
        });
}

// Update events table
function updateEventsTable(events) {
    const tbody = document.getElementById('events-tbody');
    
    if (events.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">No events found</td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = events.map(event => `
        <tr>
            <td>${new Date(event.created_at).toLocaleString()}</td>
            <td>
                <span class="badge bg-${getActionBadgeColor(event.action)}">
                    ${event.action.replace(/_/g, ' ').toUpperCase()}
                </span>
            </td>
            <td>${event.user ? event.user.email : 'N/A'}</td>
            <td>${event.ip_address}</td>
            <td>${event.details || 'N/A'}</td>
        </tr>
    `).join('');
}

// Get action badge color
function getActionBadgeColor(action) {
    const colors = {
        'login_failed': 'danger',
        'suspicious_activity': 'warning',
        'unauthorized_access': 'danger',
        'password_change': 'info',
        'permission_denied': 'warning',
        'file_upload': 'primary',
        'data_export': 'success',
        'bulk_operation': 'info',
        'admin_action': 'secondary'
    };
    return colors[action] || 'secondary';
}

// Refresh threats
function refreshThreats() {
    refreshDashboard();
}

// Refresh events
function refreshEvents() {
    loadSecurityEvents();
}

// Run monitoring
function runMonitoring() {
    fetch('/security/monitoring/run', { 
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            showToast('Security monitoring completed', 'success');
            refreshDashboard();
        })
        .catch(error => {
            console.error('Error running monitoring:', error);
            showToast('Error running monitoring', 'error');
        });
}

// Clear blocks
function clearBlocks() {
    if (confirm('Are you sure you want to clear all security blocks?')) {
        fetch('/security/monitoring/clear-blocks', { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                showToast('Security blocks cleared', 'success');
                refreshDashboard();
            })
            .catch(error => {
                console.error('Error clearing blocks:', error);
                showToast('Error clearing blocks', 'error');
            });
    }
}

// Show toast notification
function showToast(message, type) {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Event filter change
document.getElementById('event-filter').addEventListener('change', function() {
    loadSecurityEvents();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endsection
