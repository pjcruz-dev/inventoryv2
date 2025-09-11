@extends('layouts.app')

@section('title', 'Activity Logs')

@section('page-title', 'Activity Logs')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ route('logs.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="fas fa-download me-1"></i>Export
            </a>
        </div>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
            <i class="fas fa-trash me-1"></i>Clear Old Logs
        </button>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Activity Logs</h5>
    </div>
    <div class="card-body">
        <!-- Advanced Filters -->
        <form method="GET" action="{{ route('logs.index') }}" class="mb-4">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by event, user, asset, IP..." 
                           value="{{ request('search') }}">
                </div>
                
                <!-- Category Filter -->
                <div class="col-md-2">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Event Type Filter -->
                <div class="col-md-2">
                    <label for="event_type" class="form-label">Event Type</label>
                    <select name="event_type" id="event_type" class="form-select">
                        <option value="">All Events</option>
                        @foreach($eventTypes as $eventType)
                            <option value="{{ $eventType }}" {{ request('event_type') == $eventType ? 'selected' : '' }}>
                                {{ $eventType }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- User Filter -->
                <div class="col-md-2">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Department Filter -->
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mt-2">
                <!-- Date From -->
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                           value="{{ request('date_from') }}">
                </div>
                
                <!-- Date To -->
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                           value="{{ request('date_to') }}">
                </div>
                
                <!-- Filter Actions -->
                <div class="col-md-8 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Results Summary -->
        @if($logs->total() > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} log entries
                @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                    (filtered)
                @endif
            </div>
        @endif
        
        <!-- Logs Table -->
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="fw-semibold">Date/Time</th>
                            <th class="fw-semibold">Category</th>
                            <th class="fw-semibold">Event</th>
                            <th class="fw-semibold">User</th>
                            <th class="fw-semibold">Asset</th>
                            <th class="fw-semibold">Department</th>
                            <th class="fw-semibold">IP Address</th>
                            <th class="fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="border-bottom">
                                <td>
                                    <small class="text-muted">
                                        {{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}<br>
                                        {{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-enhanced bg-secondary px-2 py-1">{{ $log->category }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-enhanced
                                        @if(str_contains(strtolower($log->event_type), 'create')) bg-success
                                        @elseif(str_contains(strtolower($log->event_type), 'update') || str_contains(strtolower($log->event_type), 'edit')) bg-warning
                                        @elseif(str_contains(strtolower($log->event_type), 'delete')) bg-danger
                                        @elseif(str_contains(strtolower($log->event_type), 'assign')) bg-info
                                        @else bg-primary
                                        @endif px-2 py-1">
                                        {{ $log->event_type }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div>
                                            <strong>{{ $log->user->first_name }} {{ $log->user->last_name }}</strong><br>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->asset)
                                        <div>
                                            <strong>{{ $log->asset->name }}</strong><br>
                                            <small class="text-muted">{{ $log->asset->asset_tag }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->department)
                                        {{ $log->department->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $log->ip_address ?: '-' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('logs.show', $log) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center" title="View Details" style="width: 32px; height: 32px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="pagination-wrapper">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} logs
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Activity Logs Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                        No logs match your current filters. Try adjusting your search criteria.
                    @else
                        No activity logs have been recorded yet.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                    <a href="{{ route('logs.index') }}" class="btn btn-primary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Clear Old Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">Clear Old Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('logs.clear') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Warning:</strong> This action will permanently delete old log entries and cannot be undone.
                    </div>
                    
                    <div class="mb-3">
                        <label for="days" class="form-label">Delete logs older than:</label>
                        <select name="days" id="days" class="form-select" required>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90" selected>90 days</option>
                            <option value="180">180 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Old Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#category, #event_type, #user_id, #department_id, #date_from, #date_to');
        
        filterSelects.forEach(function(select) {
            select.addEventListener('change', function() {
                // Optional: Auto-submit on change (uncomment if desired)
                // this.form.submit();
            });
        });
        
        // Search input with debounce
        const searchInput = document.getElementById('search');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Optional: Auto-submit on search (uncomment if desired)
                // this.form.submit();
            }, 500);
        });
    });
</script>
@endsection