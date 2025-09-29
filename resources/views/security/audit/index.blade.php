@extends('layouts.app')

@section('page-title', 'Security Audit Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">Security Audit Dashboard</h5>
                            <small class="text-white-50">Monitor security events, login attempts, and system activities</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('security.audit.logs') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-list me-1"></i>View All Logs
                                </a>
                                <a href="{{ route('security.audit.export') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-download me-1"></i>Export
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_events'] ?? 0 }}</h4>
                            <p class="card-text">Total Events (30 days)</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['failed_logins'] ?? 0 }}</h4>
                            <p class="card-text">Failed Logins (7 days)</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['suspicious_activities'] ?? 0 }}</h4>
                            <p class="card-text">Suspicious Activities</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x"></i>
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
                            <h4 class="card-title">{{ $stats['data_changes'] ?? 0 }}</h4>
                            <p class="card-text">Data Changes (7 days)</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Security Events -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Security Events</h5>
                    <a href="{{ route('security.audit.logs') }}" class="btn btn-primary btn-sm">View All Logs</a>
                </div>
                <div class="card-body">
                    @if($recentEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>IP Address</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvents as $event)
                                    <tr>
                                        <td>{{ $event->created_at->format('M d, Y H:i:s') }}</td>
                                        <td>{{ $event->user ? $event->user->name : 'System' }}</td>
                                        <td>
                                            <span class="badge bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($event->action) }}">
                                                {{ ucwords(str_replace('_', ' ', $event->action)) }}
                                            </span>
                                        </td>
                                        <td>{{ $event->ip_address }}</td>
                                        <td>
                                            @if($event->details)
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $event->id }}">
                                                    View Details
                                                </button>
                                            @else
                                                <span class="text-muted">No details</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No recent security events</h5>
                            <p class="text-muted">Security events will appear here when they occur.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Login Attempts -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Failed Login Attempts (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    @if($failedLogins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($failedLogins as $login)
                                    <tr>
                                        <td>{{ $login->created_at->format('M d, Y H:i:s') }}</td>
                                        <td>{{ $login->user ? $login->user->name : 'Unknown' }}</td>
                                        <td>{{ $login->ip_address }}</td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($login->user_agent, 50) }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">No failed login attempts</h5>
                            <p class="text-muted">All login attempts have been successful in the last 7 days.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modals -->
@foreach($recentEvents as $event)
@if($event->details)
<div class="modal fade" id="detailsModal{{ $event->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre class="bg-light p-3 rounded">{{ json_encode($event->details, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endpush

