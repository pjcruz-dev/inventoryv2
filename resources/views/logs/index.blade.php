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
<style>
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, var(--bs-white) 0%, rgba(var(--bs-primary-rgb), 0.02) 100%);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .filter-container {
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.05) 0%, rgba(var(--bs-info-rgb), 0.05) 100%);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid rgba(var(--bs-primary-rgb), 0.1);
        margin-bottom: 1.5rem;
    }
    
    .table-modern {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    
    .status-badge {
        background: linear-gradient(135deg, var(--bs-info) 0%, var(--bs-primary) 100%);
        border: none;
        color: white;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
    }
    
    .btn-action {
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 1px solid rgba(var(--bs-gray-300-rgb), 0.5);
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .filter-row {
        background: rgba(var(--bs-light-rgb), 0.3);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>

<div class="card-modern">
    <div class="card-header" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.1) 0%, rgba(var(--bs-info-rgb), 0.05) 100%); border-bottom: 1px solid rgba(var(--bs-primary-rgb), 0.1); border-radius: 16px 16px 0 0;">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>Activity Logs
        </h5>
    </div>
    <div class="card-body">
        <!-- Enhanced Advanced Filters -->
        <div class="filter-container">
            <form method="GET" action="{{ route('logs.index') }}">
                <div class="filter-row">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold text-dark">
                                <i class="fas fa-search me-2 text-primary"></i>Search
                            </label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by event, user, asset, IP..." 
                                   value="{{ request('search') }}">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="col-md-2">
                            <label for="category" class="form-label fw-semibold text-dark">
                                <i class="fas fa-tags me-2 text-info"></i>Category
                            </label>
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
                            <label for="event_type" class="form-label fw-semibold text-dark">
                                <i class="fas fa-bolt me-2 text-warning"></i>Event Type
                            </label>
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
                            <label for="user_id" class="form-label fw-semibold text-dark">
                                <i class="fas fa-user me-2 text-success"></i>User
                            </label>
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
                            <label for="department_id" class="form-label fw-semibold text-dark">
                                <i class="fas fa-building me-2 text-secondary"></i>Department
                            </label>
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
                </div>
                
                <div class="filter-row">
                    <div class="row g-3">
                        <!-- Date From -->
                        <div class="col-md-3">
                            <label for="date_from" class="form-label fw-semibold text-dark">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>Date From
                            </label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <!-- Date To -->
                        <div class="col-md-3">
                            <label for="date_to" class="form-label fw-semibold text-dark">
                                <i class="fas fa-calendar-check me-2 text-info"></i>Date To
                            </label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>
                        
                        <!-- Filter Actions -->
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-action" style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%); border: none;">
                                <i class="fas fa-filter me-1"></i>Apply Filters
                            </button>
                            @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                                <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary btn-action">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Enhanced Results Summary -->
        @if($logs->total() > 0)
            <div class="alert" style="background: linear-gradient(135deg, rgba(var(--bs-info-rgb), 0.1) 0%, rgba(var(--bs-primary-rgb), 0.05) 100%); border: 1px solid rgba(var(--bs-info-rgb), 0.2); border-radius: 12px;">
                <i class="fas fa-info-circle me-2 text-info"></i>
                <span class="fw-semibold text-dark">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} log entries</span>
                @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                    <span class="status-badge ms-2">filtered</span>
                @endif
            </div>
        @endif
        
        <!-- Enhanced Logs Table -->
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead style="background: linear-gradient(135deg, rgba(var(--bs-dark-rgb), 0.9) 0%, rgba(var(--bs-primary-rgb), 0.8) 100%);">
                        <tr>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-clock me-2"></i>Date/Time
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-tags me-2"></i>Category
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-bolt me-2"></i>Event
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-user me-2"></i>User
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-cube me-2"></i>Asset
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-building me-2"></i>Department
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-globe me-2"></i>IP Address
                            </th>
                            <th class="border-0 fw-semibold text-white">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="border-0" style="border-bottom: 1px solid rgba(var(--bs-gray-200-rgb), 0.5) !important;">
                                <td class="py-3">
                                    <div class="text-nowrap">
                                        <strong class="text-dark">{{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $log->created_at ? $log->created_at->format('h:i A') : '' }}</small>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="status-badge" style="background: linear-gradient(135deg, var(--bs-secondary) 0%, var(--bs-dark) 100%);">{{ $log->category }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="status-badge 
                                        @if(str_contains(strtolower($log->event_type), 'create')) style="background: linear-gradient(135deg, var(--bs-success) 0%, #28a745 100%);"
                                        @elseif(str_contains(strtolower($log->event_type), 'update') || str_contains(strtolower($log->event_type), 'edit')) style="background: linear-gradient(135deg, var(--bs-warning) 0%, #ffc107 100%);"
                                        @elseif(str_contains(strtolower($log->event_type), 'delete')) style="background: linear-gradient(135deg, var(--bs-danger) 0%, #dc3545 100%);"
                                        @elseif(str_contains(strtolower($log->event_type), 'assign')) style="background: linear-gradient(135deg, var(--bs-info) 0%, #17a2b8 100%);"
                                        @else style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);"
                                        @endif">
                                        {{ $log->event_type }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2" style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%); border-radius: 50%; padding: 2px;">
                                                <img src="{{ $log->user->avatar ? asset('storage/' . $log->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($log->user->first_name . ' ' . $log->user->last_name) . '&background=6366f1&color=ffffff' }}" 
                                                     alt="{{ $log->user->first_name }} {{ $log->user->last_name }}" 
                                                     class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
                                                <small class="text-muted">{{ $log->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fw-medium">System</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($log->asset)
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $log->asset->name }}</div>
                                            <small class="text-muted">{{ $log->asset->asset_tag }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($log->department)
                                        <span class="status-badge" style="background: linear-gradient(135deg, rgba(var(--bs-light-rgb), 0.8) 0%, rgba(var(--bs-gray-100-rgb), 0.9) 100%); color: var(--bs-dark);">{{ $log->department->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <code class="text-muted" style="background: rgba(var(--bs-gray-100-rgb), 0.5); padding: 0.25rem 0.5rem; border-radius: 6px;">{{ $log->ip_address ?: '-' }}</code>
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('logs.show', $log) }}" class="btn btn-sm btn-outline-primary btn-action" title="View Details">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Enhanced Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4 p-3" style="background: rgba(var(--bs-light-rgb), 0.3); border-radius: 12px;">
                <div>
                    <small class="text-muted fw-medium">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                    </small>
                </div>
                <div>
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <!-- Enhanced Empty State -->
            <div class="text-center py-5" style="background: linear-gradient(135deg, rgba(var(--bs-light-rgb), 0.3) 0%, rgba(var(--bs-gray-100-rgb), 0.2) 100%); border-radius: 16px; border: 2px dashed rgba(var(--bs-gray-300-rgb), 0.5);">
                <div style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <i class="fas fa-clipboard-list fa-2x text-white"></i>
                </div>
                <h5 class="text-dark fw-semibold mb-2">No Activity Logs Found</h5>
                <p class="text-muted mb-3">
                    @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                        No logs match your current filters. Try adjusting your search criteria.
                    @else
                        No activity logs have been recorded yet.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'category', 'event_type', 'user_id', 'department_id', 'date_from', 'date_to']))
                    <a href="{{ route('logs.index') }}" class="btn btn-outline-primary btn-action" style="border-radius: 25px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Enhanced Clear Old Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(var(--bs-danger-rgb), 0.1) 0%, rgba(var(--bs-warning-rgb), 0.05) 100%); border-bottom: 1px solid rgba(var(--bs-danger-rgb), 0.1); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-semibold text-dark" id="clearLogsModalLabel">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>Clear Old Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('logs.clear') }}">
                @csrf
                <div class="modal-body" style="padding: 2rem;">
                    <div class="alert" style="background: linear-gradient(135deg, rgba(var(--bs-warning-rgb), 0.1) 0%, rgba(var(--bs-danger-rgb), 0.05) 100%); border: 1px solid rgba(var(--bs-warning-rgb), 0.2); border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        <strong class="text-dark">Warning:</strong> <span class="text-dark">This action will permanently delete old log entries and cannot be undone.</span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="days" class="form-label fw-semibold text-dark">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Delete logs older than:
                        </label>
                        <select name="days" id="days" class="form-select" required style="border-radius: 8px;">
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90" selected>90 days</option>
                            <option value="180">180 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(var(--bs-gray-200-rgb), 0.5); padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-secondary btn-action" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger btn-action" style="background: linear-gradient(135deg, var(--bs-danger) 0%, #dc3545 100%); border: none;">
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