@extends('layouts.app')

@section('page-title', 'Security Audit Logs')

@section('content')
<div class="container-fluid">
    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Audit Logs</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('security.audit.logs') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="action" class="form-label">Action</label>
                    <select name="action" id="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="auth_login" {{ request('action') == 'auth_login' ? 'selected' : '' }}>Login</option>
                        <option value="auth_logout" {{ request('action') == 'auth_logout' ? 'selected' : '' }}>Logout</option>
                        <option value="failed_login_attempt" {{ request('action') == 'failed_login_attempt' ? 'selected' : '' }}>Failed Login</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="file_upload" {{ request('action') == 'file_upload' ? 'selected' : '' }}>File Upload</option>
                        <option value="suspicious_activity" {{ request('action') == 'suspicious_activity' ? 'selected' : '' }}>Suspicious Activity</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="ip_address" class="form-label">IP Address</label>
                    <input type="text" name="ip_address" id="ip_address" class="form-control" value="{{ request('ip_address') }}" placeholder="192.168.1.1">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('security.audit.logs') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                    <a href="{{ route('security.audit.export', request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Audit Logs ({{ $logs->total() }} total)</h5>
            <div>
                <a href="{{ route('security.audit.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    @if($log->user)
                                        <a href="{{ route('security.audit.user-activity', $log->user->id) }}" class="text-decoration-none">
                                            {{ $log->user->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ \App\Http\Controllers\SecurityAuditController::getActionBadgeColor($log->action) }}">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->model_type && $log->model_id)
                                        <span class="text-muted">{{ $log->model_type }} #{{ $log->model_id }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($log->user_agent, 50) }}</small>
                                </td>
                                <td>
                                    @if($log->details)
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $log->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No audit logs found</h5>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Details Modals -->
@foreach($logs as $log)
@if($log->details)
<div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details - {{ ucwords(str_replace('_', ' ', $log->action)) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Event ID:</strong> {{ $log->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>Timestamp:</strong> {{ $log->created_at->format('M d, Y H:i:s') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>User:</strong> {{ $log->user ? $log->user->name : 'System' }}
                    </div>
                    <div class="col-md-6">
                        <strong>IP Address:</strong> {{ $log->ip_address }}
                    </div>
                </div>
                @if($log->model_type)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Model Type:</strong> {{ $log->model_type }}
                    </div>
                    <div class="col-md-6">
                        <strong>Model ID:</strong> {{ $log->model_id }}
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <strong>User Agent:</strong>
                    <br>
                    <small class="text-muted">{{ $log->user_agent }}</small>
                </div>
                <div class="mb-3">
                    <strong>Details:</strong>
                    <pre class="bg-light p-3 rounded mt-2">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
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
