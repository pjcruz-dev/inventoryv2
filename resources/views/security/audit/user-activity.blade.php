@extends('layouts.app')

@section('page-title', 'User Activity - ' . $user->name)

@section('content')
<div class="container-fluid">
    <!-- User Info Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">User Activity: {{ $user->name }}</h5>
                        <small class="text-muted">Last 30 days of activity</small>
                    </div>
                    <div>
                        <a href="{{ route('security.audit.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <a href="{{ route('security.audit.export', ['user_id' => $user->id]) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i>Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>User ID:</strong> {{ $user->id }}
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong> {{ $user->email }}
                        </div>
                        <div class="col-md-3">
                            <strong>Department:</strong> {{ $user->department ? $user->department->name : 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i:s') : 'Never' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $activities->count() }}</h4>
                            <p class="card-text">Total Activities</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="card-title">{{ $activities->whereIn('action', ['created', 'updated'])->count() }}</h4>
                            <p class="card-text">Data Changes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-edit fa-2x"></i>
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
                            <h4 class="card-title">{{ $activities->whereIn('action', ['auth_login', 'auth_logout'])->count() }}</h4>
                            <p class="card-text">Auth Events</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
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
                            <h4 class="card-title">{{ $activities->where('action', 'file_upload')->count() }}</h4>
                            <p class="card-text">File Operations</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-upload fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Activity Timeline</h5>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                        <div class="timeline">
                            @foreach($activities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($activity->action) }}"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                                                @if($activity->model_type)
                                                    <small class="text-muted">on {{ $activity->model_type }}</small>
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $activity->created_at->format('M d, Y H:i:s') }}
                                                <span class="ms-3">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $activity->ip_address }}
                                                </span>
                                            </p>
                                            @if($activity->details)
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $activity->id }}">
                                                    <i class="fas fa-eye"></i> View Details
                                                </button>
                                            @endif
                                        </div>
                                        <span class="badge bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($activity->action) }}">
                                            {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No activity found</h5>
                            <p class="text-muted">This user has no recorded activities in the last 30 days.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modals -->
@foreach($activities as $activity)
@if($activity->details)
<div class="modal fade" id="detailsModal{{ $activity->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Activity ID:</strong> {{ $activity->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>Timestamp:</strong> {{ $activity->created_at->format('M d, Y H:i:s') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Action:</strong> {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                    </div>
                    <div class="col-md-6">
                        <strong>IP Address:</strong> {{ $activity->ip_address }}
                    </div>
                </div>
                @if($activity->model_type)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Model Type:</strong> {{ $activity->model_type }}
                    </div>
                    <div class="col-md-6">
                        <strong>Model ID:</strong> {{ $activity->model_id }}
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <strong>User Agent:</strong>
                    <br>
                    <small class="text-muted">{{ $activity->user_agent }}</small>
                </div>
                <div class="mb-3">
                    <strong>Details:</strong>
                    <pre class="bg-light p-3 rounded mt-2">{{ json_encode($activity->details, JSON_PRETTY_PRINT) }}</pre>
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

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #dee2e6;
}
</style>
@endpush
