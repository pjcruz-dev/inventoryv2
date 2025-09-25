@extends('layouts.app')

@section('title', 'Asset Assignment Confirmations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>Asset Assignment Confirmations
                    </h3>
                    <div class="btn-group">
                        @can('create_asset_assignment_confirmations')
                        <a href="{{ route('asset-assignment-confirmations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>New Confirmation
                        </a>
                        @endcan
                        @can('manage_assignment_confirmations')
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i>Import/Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('asset-assignment-confirmations.export') }}">
                                    <i class="fas fa-file-excel me-2"></i>Export to Excel
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('asset-assignment-confirmations.import-form') }}">
                                    <i class="fas fa-file-import me-2"></i>Import from Excel
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('asset-assignment-confirmations.download-template') }}">
                                    <i class="fas fa-file-download me-2"></i>Download Template
                                </a></li>
                            </ul>
                        </div>
                        @endcan
                        @can('manage_assignment_confirmations')
                        <button type="button" class="btn btn-warning" id="sendBulkRemindersBtn" disabled>
                            <i class="fas fa-bell me-1"></i>Send Bulk Reminders
                        </button>
                        @endcan
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('asset-assignment-confirmations.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search confirmations..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('asset-assignment-confirmations.index') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_from" class="form-control" 
                                       placeholder="From Date" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $confirmations->where('status', 'pending')->count() }}</h4>
                                            <small>Pending</small>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $confirmations->where('status', 'confirmed')->count() }}</h4>
                                            <small>Confirmed</small>
                                        </div>
                                        <i class="fas fa-check fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $confirmations->where('status', 'declined')->count() }}</h4>
                                            <small>Declined</small>
                                        </div>
                                        <i class="fas fa-times fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $confirmations->where('status', 'expired')->count() }}</h4>
                                            <small>Expired</small>
                                        </div>
                                        <i class="fas fa-hourglass-end fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmations Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                    </th>
                                    <th>Asset</th>
                                    <th>User</th>
                                    <th>Assigned At</th>
                                    <th>Status</th>
                                    <th>Confirmed At</th>
                                    <th>Reminders</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($confirmations as $confirmation)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input confirmation-checkbox" 
                                                   value="{{ $confirmation->id }}" 
                                                   data-status="{{ $confirmation->status }}"
                                                   {{ $confirmation->status !== 'pending' ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="asset-icon me-2">
                                                    <i class="fas fa-laptop text-primary"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $confirmation->asset->name }}</strong><br>
                                                    <small class="text-muted">{{ $confirmation->asset->asset_tag }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-2">
                                                    <i class="fas fa-user-circle text-secondary"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $confirmation->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $confirmation->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $confirmation->assigned_at ? $confirmation->assigned_at->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($confirmation->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                    @break
                                                @case('declined')
                                                    <span class="badge bg-danger">Declined</span>
                                                    @break
                                                @case('expired')
                                                    <span class="badge bg-secondary">Expired</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($confirmation->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($confirmation->confirmed_at)
                                                <span class="badge bg-success">
                                                    {{ $confirmation->confirmed_at->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not confirmed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2">{{ $confirmation->reminder_count ?? 0 }}</span>
                                                @if($confirmation->status == 'pending')
                                                    @can('manage_assignment_confirmations')
                                                    <a href="{{ route('asset-assignment-confirmations.send-reminder', $confirmation) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Send Reminder">
                                                        <i class="fas fa-bell"></i>
                                                    </a>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('view_assignment_confirmations')
                                                <a href="{{ route('asset-assignment-confirmations.show', $confirmation) }}" 
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_assignment_confirmations')
                                                <a href="{{ route('asset-assignment-confirmations.edit', $confirmation) }}" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @if($confirmation->status == 'pending')
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                                data-bs-toggle="dropdown" title="Quick Actions">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item text-success" 
                                                                   href="{{ route('asset-assignment-confirmations.confirm', $confirmation->confirmation_token) }}">
                                                                <i class="fas fa-check me-2"></i>Confirm
                                                            </a></li>
                                                            <li><a class="dropdown-item text-danger" 
                                                                   href="{{ route('asset-assignment-confirmations.decline', $confirmation->confirmation_token) }}">
                                                                <i class="fas fa-times me-2"></i>Decline
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete({{ $confirmation->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p class="mb-0">No asset assignment confirmations found.</p>
                                                @if(request()->hasAny(['search', 'status', 'date_from']))
                                                    <a href="{{ route('asset-assignment-confirmations.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                                        Clear filters
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($confirmations->hasPages())
                        <div class="pagination-wrapper mt-3">
                            {{ $confirmations->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this asset assignment confirmation?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(confirmationId) {
    const form = document.getElementById('deleteForm');
    form.action = `/asset-assignment-confirmations/${confirmationId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-refresh pending confirmations every 30 seconds
setInterval(function() {
    if (window.location.href.includes('status=pending')) {
        location.reload();
    }
}, 30000);

// Bulk reminder functionality
$(document).ready(function() {
    const selectAllCheckbox = $('#selectAllCheckbox');
    const confirmationCheckboxes = $('.confirmation-checkbox');
    const sendBulkRemindersBtn = $('#sendBulkRemindersBtn');
    
    // Handle select all checkbox
    selectAllCheckbox.on('change', function() {
        const isChecked = $(this).is(':checked');
        confirmationCheckboxes.filter(':not(:disabled)').prop('checked', isChecked);
        updateBulkReminderButton();
    });
    
    // Handle individual checkboxes
    confirmationCheckboxes.on('change', function() {
        updateSelectAllState();
        updateBulkReminderButton();
    });
    
    // Update select all checkbox state
    function updateSelectAllState() {
        const pendingCheckboxes = confirmationCheckboxes.filter(':not(:disabled)');
        const checkedPendingCheckboxes = pendingCheckboxes.filter(':checked');
        
        if (checkedPendingCheckboxes.length === 0) {
            selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
        } else if (checkedPendingCheckboxes.length === pendingCheckboxes.length) {
            selectAllCheckbox.prop('checked', true).prop('indeterminate', false);
        } else {
            selectAllCheckbox.prop('checked', false).prop('indeterminate', true);
        }
    }
    
    // Update bulk reminder button state
    function updateBulkReminderButton() {
        const selectedPendingCheckboxes = confirmationCheckboxes.filter(':checked').filter('[data-status="pending"]');
        sendBulkRemindersBtn.prop('disabled', selectedPendingCheckboxes.length === 0);
        
        if (selectedPendingCheckboxes.length > 0) {
            sendBulkRemindersBtn.text(`Send Bulk Reminders (${selectedPendingCheckboxes.length})`);
        } else {
            sendBulkRemindersBtn.text('Send Bulk Reminders');
        }
    }
    
    // Handle bulk reminder sending
    sendBulkRemindersBtn.on('click', function() {
        const selectedCheckboxes = confirmationCheckboxes.filter(':checked').filter('[data-status="pending"]');
        const confirmationIds = selectedCheckboxes.map(function() {
            return $(this).val();
        }).get();
        
        if (confirmationIds.length === 0) {
            alert('Please select at least one pending confirmation to send reminders.');
            return;
        }
        
        if (confirm(`Send reminders to ${confirmationIds.length} selected confirmations?`)) {
            // Show loading state
            const originalText = sendBulkRemindersBtn.html();
            sendBulkRemindersBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...').prop('disabled', true);
            
            // Make AJAX request
            $.ajax({
                url: '{{ route("asset-assignment-confirmations.send-bulk-reminders") }}',
                method: 'POST',
                data: {
                    confirmation_ids: confirmationIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Reload page to show updated reminder counts
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Error sending bulk reminders:', xhr);
                    alert('Error sending bulk reminders. Please try again.');
                    sendBulkRemindersBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    });
    
    // Initialize state
    updateSelectAllState();
    updateBulkReminderButton();
});
</script>
@endpush