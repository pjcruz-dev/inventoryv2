@extends('layouts.app')

@section('page-title', 'Security Events')

@section('content')
<div class="container-fluid">
    <!-- Security Events Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Security Events (Last 30 Days)</h5>
                    <div>
                        <a href="{{ route('security.audit.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('security.audit.export', ['action' => 'security_events']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i>Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Events Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Event Type</th>
                                        <th>IP Address</th>
                                        <th>Location</th>
                                        <th>Details</th>
                                        <th>Severity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr class="{{ app(\App\Http\Controllers\SecurityAuditController::class)->getEventRowClass($event->action) }}">
                                        <td>{{ $event->created_at->format('M d, Y H:i:s') }}</td>
                                        <td>
                                            @if($event->user)
                                                <a href="{{ route('security.audit.user-activity', $event->user->id) }}" class="text-decoration-none">
                                                    {{ $event->user->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($event->action) }}">
                                                {{ ucwords(str_replace('_', ' ', $event->action)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $event->ip_address }}</code>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ app(\App\Http\Controllers\SecurityAuditController::class)->getLocationFromIP($event->ip_address) }}</small>
                                        </td>
                                        <td>
                                            @if($event->details)
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $event->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            @else
                                                <span class="text-muted">No details</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ app(\App\Http\Controllers\SecurityAuditController::class)->getSeverityBadgeColor($event->action) }}">
                                                {{ app(\App\Http\Controllers\SecurityAuditController::class)->getSeverityLevel($event->action) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <h5 class="text-success">No security events found</h5>
                            <p class="text-muted">No security events have been recorded in the last 30 days.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Security Summary Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $events->where('action', 'failed_login_attempt')->count() }}</h4>
                            <p class="card-text">Failed Logins</p>
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
                            <h4 class="card-title">{{ $events->where('action', 'suspicious_activity')->count() }}</h4>
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
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $events->where('action', 'rate_limit_exceeded')->count() }}</h4>
                            <p class="card-text">Rate Limit Violations</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tachometer-alt fa-2x"></i>
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
                            <h4 class="card-title">{{ $events->whereIn('action', ['auth_login', 'auth_logout'])->count() }}</h4>
                            <p class="card-text">Auth Events</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modals -->
@foreach($events as $event)
@if($event->details)
<div class="modal fade" id="detailsModal{{ $event->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Security Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Event ID:</strong> {{ $event->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>Timestamp:</strong> {{ $event->created_at->format('M d, Y H:i:s') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>User:</strong> {{ $event->user ? $event->user->name : 'System' }}
                    </div>
                    <div class="col-md-6">
                        <strong>IP Address:</strong> {{ $event->ip_address }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Event Type:</strong> 
                        <span class="badge bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($event->action) }}">
                            {{ ucwords(str_replace('_', ' ', $event->action)) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Severity:</strong> 
                        <span class="badge bg-{{ app(\App\Http\Controllers\SecurityAuditController::class)->getSeverityBadgeColor($event->action) }}">
                            {{ app(\App\Http\Controllers\SecurityAuditController::class)->getSeverityLevel($event->action) }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>User Agent:</strong>
                    <br>
                    <small class="text-muted">{{ $event->user_agent }}</small>
                </div>
                <div class="mb-3">
                    <strong>Event Details:</strong>
                    <pre class="bg-light p-3 rounded mt-2">{{ json_encode($event->details, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

@push('scripts')
<script>
    // Auto-refresh every 60 seconds for security events
    setInterval(function() {
        location.reload();
    }, 60000);
</script>
@endpush

