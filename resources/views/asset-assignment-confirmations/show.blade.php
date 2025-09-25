@extends('layouts.app')

@section('title', 'Asset Assignment Confirmation Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-eye me-2"></i>Asset Assignment Confirmation Details
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('asset-assignment-confirmations.edit', $confirmation) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $confirmation->id }})">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Asset Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-laptop me-2"></i>Asset Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Asset Name:</strong></div>
                                        <div class="col-sm-8">{{ $confirmation->asset->name }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Asset Tag:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-info">{{ $confirmation->asset->asset_tag }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Category:</strong></div>
                                        <div class="col-sm-8">{{ $confirmation->asset->category->name ?? 'N/A' }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Serial Number:</strong></div>
                                        <div class="col-sm-8">{{ $confirmation->asset->serial_number ?? 'N/A' }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Current Status:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $confirmation->asset->status == 'available' ? 'success' : ($confirmation->asset->status == 'assigned' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($confirmation->asset->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>User Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Name:</strong></div>
                                        <div class="col-sm-8">{{ $confirmation->user->name }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Email:</strong></div>
                                        <div class="col-sm-8">
                                            <a href="mailto:{{ $confirmation->user->email }}">{{ $confirmation->user->email }}</a>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Department:</strong></div>
                                        <div class="col-sm-8">{{ $confirmation->user->department ?? 'N/A' }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Phone:</strong></div>
                                        <div class="col-sm-8">{{ $confirmation->user->phone ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Assignment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h4 text-primary">{{ $confirmation->assigned_at ? $confirmation->assigned_at->format('M d, Y') : 'N/A' }}</div>
                                        <small class="text-muted">Assigned Date</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h4 text-info">{{ $confirmation->expected_return_date ? $confirmation->expected_return_date->format('M d, Y') : 'N/A' }}</div>
                                        <small class="text-muted">Expected Return</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h4">
                                            @switch($confirmation->status)
                                                @case('pending')
                                                    <span class="text-warning">Pending</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="text-success">Confirmed</span>
                                                    @break
                                                @case('declined')
                                                    <span class="text-danger">Declined</span>
                                                    @break
                                                @case('expired')
                                                    <span class="text-secondary">Expired</span>
                                                    @break
                                                @default
                                                    <span class="text-muted">{{ ucfirst($confirmation->status) }}</span>
                                            @endswitch
                                        </div>
                                        <small class="text-muted">Status</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="h4">
                                            @switch($confirmation->priority ?? 'medium')
                                                @case('low')
                                                    <span class="text-success">Low</span>
                                                    @break
                                                @case('medium')
                                                    <span class="text-info">Medium</span>
                                                    @break
                                                @case('high')
                                                    <span class="text-warning">High</span>
                                                    @break
                                                @case('urgent')
                                                    <span class="text-danger">Urgent</span>
                                                    @break
                                                @default
                                                    <span class="text-muted">{{ ucfirst($confirmation->priority) }}</span>
                                            @endswitch
                                        </div>
                                        <small class="text-muted">Priority</small>
                                    </div>
                                </div>
                            </div>

                            @if($confirmation->notes)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h6>Notes:</h6>
                                    <p class="text-muted">{{ $confirmation->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Confirmation Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Confirmation Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="h4 text-info">{{ $confirmation->reminder_count ?? 0 }}</div>
                                        <small class="text-muted">Reminders Sent</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="h4 text-{{ $confirmation->confirmed_at ? 'success' : 'muted' }}">
                                            {{ $confirmation->confirmed_at ? $confirmation->confirmed_at->format('M d, Y H:i') : 'Not Confirmed' }}
                                        </div>
                                        <small class="text-muted">Confirmation Date</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="h4 text-{{ $confirmation->declined_at ? 'danger' : 'muted' }}">
                                            {{ $confirmation->declined_at ? $confirmation->declined_at->format('M d, Y H:i') : 'Not Declined' }}
                                        </div>
                                        <small class="text-muted">Decline Date</small>
                                    </div>
                                </div>
                            </div>

                            @if($confirmation->confirmation_token)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h6>Confirmation Token:</h6>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $confirmation->confirmation_token }}" readonly>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $confirmation->confirmation_token }}')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                    <small class="text-muted">Use this token for manual confirmation</small>
                                </div>
                            </div>
                            @endif

                            @if($confirmation->decline_reason)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <h6>Decline Reason:</h6>
                                    <div class="alert alert-danger">
                                        {{ $confirmation->decline_reason }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('asset-assignment-confirmations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="btn-group">
                            @if($confirmation->status == 'pending')
                                <a href="{{ route('asset-assignment-confirmations.confirm', $confirmation->confirmation_token) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-check me-1"></i>Mark as Confirmed
                                </a>
                                <a href="{{ route('asset-assignment-confirmations.decline', $confirmation->confirmation_token) }}" 
                                   class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i>Mark as Declined
                                </a>
                                <a href="{{ route('asset-assignment-confirmations.send-reminder', $confirmation) }}" 
                                   class="btn btn-warning send-reminder-btn"
                                   data-confirmation-id="{{ $confirmation->id }}"
                                   data-asset-tag="{{ $confirmation->asset->asset_tag }}">
                                    <i class="fas fa-bell me-1"></i>Send Reminder
                                </a>
                            @endif
                            <a href="{{ route('asset-assignment-confirmations.edit', $confirmation) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                        </div>
                    </div>
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
@media print {
    .btn, .card-header .btn-group, .no-print {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background: none !important;
        border: none !important;
        padding-bottom: 0 !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .h4 {
        font-size: 14px;
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

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">Token copied to clipboard!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    });
}

// Handle individual send reminder buttons
$(document).ready(function() {
    $('.send-reminder-btn').on('click', function(e) {
        e.preventDefault();
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        const confirmationId = $btn.data('confirmation-id');
        const assetTag = $btn.data('asset-tag');
        
        // Show loading state
        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...').prop('disabled', true);
        
        // Make AJAX request
        $.ajax({
            url: $btn.attr('href'),
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
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
});
</script>
@endpush