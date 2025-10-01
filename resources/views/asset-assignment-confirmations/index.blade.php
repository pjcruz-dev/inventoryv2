@extends('layouts.app')

@section('title', 'Asset Assignment Confirmations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Asset Assignment Confirmations</h5>
                            <small class="text-white-50">{{ $confirmations->total() }} total confirmations</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                @can('create_asset_assignment_confirmations')
                                <a href="{{ route('asset-assignment-confirmations.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus me-1"></i>New Confirmation
                                </a>
                                @endcan
                                @can('manage_assignment_confirmations')
                                <button type="button" class="btn btn-light btn-sm" id="sendBulkRemindersBtn" disabled style="color: #667eea;">
                                    <i class="fas fa-bell me-1"></i>Send Bulk Reminders
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('asset-assignment-confirmations.index') }}" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search confirmations..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                                        <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">

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
                                                       class="btn btn-sm btn-outline-warning send-reminder-btn" 
                                                       title="Send Reminder"
                                                       data-confirmation-id="{{ $confirmation->id }}"
                                                       data-asset-tag="{{ $confirmation->asset->asset_tag }}">
                                                        <i class="fas fa-bell"></i>
                                                    </a>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('view_assignment_confirmations')
                                                <a href="{{ route('asset-assignment-confirmations.show', $confirmation) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_assignment_confirmations')
                                                <a href="{{ route('asset-assignment-confirmations.edit', $confirmation) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @if($confirmation->status == 'pending')
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-reminder dropdown-toggle" 
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
                                                <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" 
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
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="text-muted">
                                        Showing {{ $confirmations->firstItem() }} to {{ $confirmations->lastItem() }} of {{ $confirmations->total() }} results
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        {{ $confirmations->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
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

@push('styles')
<style>
/* Action Button Styles */
.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 14px;
    position: relative;
    overflow: hidden;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-btn-view {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    border-color: #4f46e5;
}

.action-btn-view:hover {
    background: linear-gradient(135deg, #3730a3 0%, #6d28d9 100%);
    color: white;
}

.action-btn-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    border-color: #f59e0b;
}

.action-btn-edit:hover {
    background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
    color: white;
}

.action-btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-color: #ef4444;
}

.action-btn-delete:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.action-btn-print {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-color: #10b981;
}

.action-btn-print:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
}

.action-btn-reminder {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border-color: #8b5cf6;
}

.action-btn-reminder:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
}

.action-btn-mark {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
    border-color: #06b6d4;
}

.action-btn-mark:hover {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
}

/* Loading state */
.action-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.action-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Pagination styling */
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: #667eea;
    border: 1px solid #e9ecef;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    color: #764ba2;
    background-color: #f8f9fa;
    border-color: #667eea;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #e9ecef;
}

.pagination .page-item:first-child .page-link {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

.pagination .page-item:last-child .page-link {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .card-footer .row {
        text-align: center;
    }
    
    .card-footer .col-md-6:first-child {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

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
    
    // Debug: Check if buttons are found
    console.log('Found send reminder buttons:', $('.send-reminder-btn').length);
    
    // Handle individual send reminder buttons
    $('.send-reminder-btn').on('click', function(e) {
        e.preventDefault();
        
        console.log('Send reminder button clicked!'); // Debug log
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        const confirmationId = $btn.data('confirmation-id');
        const assetTag = $btn.data('asset-tag');
        
        console.log('Confirmation ID:', confirmationId); // Debug log
        console.log('Asset Tag:', assetTag); // Debug log
        console.log('URL:', $btn.attr('href')); // Debug log
        
        // Show loading state
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        // Make AJAX request
        $.ajax({
            url: $btn.attr('href'),
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('AJAX Success:', response); // Debug log
                
                // Show success message
                if (response && response.message) {
                    showAlert('success', response.message);
                } else {
                    showAlert('success', 'Reminder sent successfully!');
                }
                
                // Reload page to show updated reminder counts
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr); // Debug log
                console.error('Error sending reminder:', xhr);
                
                let errorMessage = 'Error sending reminder. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    // Try to extract error message from response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(xhr.responseText, 'text/html');
                    const alertElement = doc.querySelector('.alert-danger, .alert-warning');
                    if (alertElement) {
                        errorMessage = alertElement.textContent.trim();
                    }
                }
                
                showAlert('error', errorMessage);
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });
    
    // Function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the content
        $('.card-body').first().prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    // Initialize state
    updateSelectAllState();
    updateBulkReminderButton();
});
</script>
@endpush