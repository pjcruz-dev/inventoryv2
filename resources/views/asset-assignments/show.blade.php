@extends('layouts.app')

@section('title', 'Asset Assignment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-eye me-2"></i>Asset Assignment Details
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('asset-assignments.edit', $assetAssignment) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Asset Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-laptop me-2"></i>Asset Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Name:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->asset->name }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Asset Tag:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-primary">{{ $assetAssignment->asset->asset_tag }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Category:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->asset->category->name ?? 'No Category' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Serial Number:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->asset->serial_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Model:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->asset->model ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $assetAssignment->asset->status == 'available' ? 'success' : ($assetAssignment->asset->status == 'assigned' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($assetAssignment->asset->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="{{ route('assets.show', $assetAssignment->asset) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>View Asset Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user me-2"></i>Assignee Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Name:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->user->name }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Email:</strong></div>
                                        <div class="col-sm-8">
                                            <a href="mailto:{{ $assetAssignment->user->email }}">{{ $assetAssignment->user->email }}</a>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Department:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->user->department->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Position:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->user->position ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Phone:</strong></div>
                                        <div class="col-sm-8">{{ $assetAssignment->user->phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="{{ route('users.show', $assetAssignment->user) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>View User Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Assignment Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Assignment Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Assigned Date:</strong></div>
                                                <div class="col-sm-8">
                                                    <span class="badge bg-info">
                                                        {{ $assetAssignment->assigned_date ? $assetAssignment->assigned_date->format('M d, Y') : 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Return Date:</strong></div>
                                                <div class="col-sm-8">
                                                    @if($assetAssignment->return_date)
                                                        <span class="badge bg-success">
                                                            {{ $assetAssignment->return_date->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not returned</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Status:</strong></div>
                                                <div class="col-sm-8">
                                                    @switch($assetAssignment->status)
                                                        @case('assigned')
                                                            <span class="badge bg-primary">Assigned</span>
                                                            @break
                                                        @case('returned')
                                                            <span class="badge bg-success">Returned</span>
                                                            @break
                                                        @case('overdue')
                                                            <span class="badge bg-danger">Overdue</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($assetAssignment->status) }}</span>
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Assigned By:</strong></div>
                                                <div class="col-sm-8">{{ $assetAssignment->assignedBy->name ?? 'System' }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Created:</strong></div>
                                                <div class="col-sm-8">{{ $assetAssignment->created_at->format('M d, Y H:i') }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                                <div class="col-sm-8">{{ $assetAssignment->updated_at->format('M d, Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($assetAssignment->notes)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <strong>Notes:</strong>
                                            <div class="mt-2 p-3 bg-light rounded">
                                                {{ $assetAssignment->notes }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Confirmation Details -->
                    @if($assetAssignment->confirmation)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-check-circle me-2"></i>Confirmation Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Status:</strong></div>
                                                <div class="col-sm-8">
                                                    <span class="badge bg-{{ $assetAssignment->confirmation->status == 'confirmed' ? 'success' : ($assetAssignment->confirmation->status == 'declined' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($assetAssignment->confirmation->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Assigned At:</strong></div>
                                                <div class="col-sm-8">
                                                    {{ $assetAssignment->confirmation->assigned_at ? $assetAssignment->confirmation->assigned_at->format('M d, Y H:i') : 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Confirmed At:</strong></div>
                                                <div class="col-sm-8">
                                                    {{ $assetAssignment->confirmation->confirmed_at ? $assetAssignment->confirmation->confirmed_at->format('M d, Y H:i') : 'Not confirmed' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Reminder Count:</strong></div>
                                                <div class="col-sm-8">{{ $assetAssignment->confirmation->reminder_count ?? 0 }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4"><strong>Last Reminder:</strong></div>
                                                <div class="col-sm-8">
                                                    {{ $assetAssignment->confirmation->last_reminder_sent_at ? $assetAssignment->confirmation->last_reminder_sent_at->format('M d, Y H:i') : 'Never' }}
                                                </div>
                                            </div>
                                            @if($assetAssignment->confirmation->status == 'pending')
                                            <div class="row">
                                                <div class="col-12">
                                                    <a href="{{ route('asset-assignment-confirmations.send-reminder', $assetAssignment->confirmation) }}" 
                                                       class="btn btn-sm btn-outline-warning send-reminder-btn"
                                                       data-confirmation-id="{{ $assetAssignment->confirmation->id }}"
                                                       data-asset-tag="{{ $assetAssignment->asset->asset_tag }}">
                                                        <i class="fas fa-bell me-1"></i>Send Reminder
                                                    </a>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($assetAssignment->confirmation->notes)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <strong>Confirmation Notes:</strong>
                                            <div class="mt-2 p-3 bg-light rounded">
                                                {{ $assetAssignment->confirmation->notes }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('asset-assignments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        <div>
                            @if($assetAssignment->status !== 'returned')
                                @can('edit_asset_assignments')
                                <button type="button" class="btn btn-success me-2" onclick="markAsReturned()" data-bs-toggle="modal" data-bs-target="#returnModal">
                                    <i class="fas fa-undo me-1"></i>Mark as Returned
                                </button>
                                @endcan
                            @endif
                            @can('edit_asset_assignments')
                            <a href="{{ route('asset-assignments.edit', $assetAssignment) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            @endcan
                            @can('view_asset_assignments')
                            <button type="button" class="btn btn-outline-primary" onclick="printAssignment()">
                                <i class="fas fa-print me-1"></i>Print
                            </button>
                            @endcan
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
                <p>Are you sure you want to delete this asset assignment?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('asset-assignments.destroy', $assetAssignment) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Returned Modal -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Returned</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('asset-assignments.return', $assetAssignment) }}" method="POST" id="returnForm">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Asset:</strong> {{ $assetAssignment->asset->name }} ({{ $assetAssignment->asset->asset_tag }})<br>
                        <strong>Assigned to:</strong> {{ $assetAssignment->user->first_name }} {{ $assetAssignment->user->last_name }}
                    </div>
                    <p>Are you sure you want to mark this asset assignment as returned?</p>
                    
                    <div class="mb-3">
                        <label for="return_date_modal" class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" id="return_date_modal" 
                               class="form-control @error('return_date') is-invalid @enderror" 
                               value="{{ old('return_date', date('Y-m-d')) }}" 
                               max="{{ date('Y-m-d') }}" 
                               required>
                        @error('return_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="return_notes" class="form-label">Return Notes</label>
                        <textarea name="notes" id="return_notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" placeholder="Add any notes about the return...">{{ old('notes', $assetAssignment->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="returnSubmitBtn">
                        <i class="fas fa-undo me-1"></i>Mark as Returned
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete() {
    // Use jQuery if Bootstrap JS is loaded via jQuery, otherwise try vanilla JS
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#deleteModal').modal('show');
    } else if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } else {
        // Fallback: try to show modal by adding show class
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'block';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'deleteModalBackdrop';
            document.body.appendChild(backdrop);
            
            // Close modal when clicking backdrop
            backdrop.onclick = function() {
                closeDeleteModal();
            };
            
            // Close modal when clicking close button
            const closeBtn = modal.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.onclick = closeDeleteModal;
            }
            
            // Close modal when clicking cancel button
            const cancelBtn = modal.querySelector('[data-bs-dismiss="modal"]');
            if (cancelBtn) {
                cancelBtn.onclick = closeDeleteModal;
            }
        }
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const backdrop = document.getElementById('deleteModalBackdrop');
    
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('role');
    }
    
    if (backdrop) {
        backdrop.remove();
    }
}

function markAsReturned() {
    // Use jQuery if Bootstrap JS is loaded via jQuery, otherwise try vanilla JS
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#returnModal').modal('show');
    } else if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('returnModal'));
        modal.show();
    } else {
        // Fallback: try to show modal by adding show class
        const modal = document.getElementById('returnModal');
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'block';
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'returnModalBackdrop';
            document.body.appendChild(backdrop);
            
            // Close modal when clicking backdrop
            backdrop.onclick = function() {
                closeModal();
            };
            
            // Close modal when clicking close button
            const closeBtn = modal.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.onclick = function() {
                    if (typeof window.customCloseModal === 'function') {
                        window.customCloseModal();
                    } else {
                        closeModal();
                    }
                };
            }
            
            // Close modal when clicking cancel button
            const cancelBtn = modal.querySelector('[data-bs-dismiss="modal"]');
            if (cancelBtn) {
                cancelBtn.onclick = function() {
                    if (typeof window.customCloseModal === 'function') {
                        window.customCloseModal();
                    } else {
                        closeModal();
                    }
                };
            }
        }
    }
}

function closeModal() {
    const modal = document.getElementById('returnModal');
    const backdrop = document.getElementById('returnModalBackdrop');
    
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('role');
    }
    
    if (backdrop) {
        backdrop.remove();
    }
}

function printAssignment() {
    window.print();
}

// Enhanced return form handling
document.addEventListener('DOMContentLoaded', function() {
    const returnForm = document.getElementById('returnForm');
    const returnSubmitBtn = document.getElementById('returnSubmitBtn');
    
    if (returnForm && returnSubmitBtn) {
        returnForm.addEventListener('submit', function(e) {
            // Prevent double submission
            returnSubmitBtn.disabled = true;
            returnSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            
            // Re-enable button after 5 seconds as fallback
            setTimeout(() => {
                returnSubmitBtn.disabled = false;
                returnSubmitBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Mark as Returned';
            }, 5000);
        });
        
        // Reset form when modal is hidden
        const returnModal = document.getElementById('returnModal');
        if (returnModal) {
            // Handle modal close events
            const resetForm = function() {
                returnSubmitBtn.disabled = false;
                returnSubmitBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Mark as Returned';
                returnForm.reset();
            };
            
            // Try Bootstrap event first, then fallback to custom close function
            returnModal.addEventListener('hidden.bs.modal', resetForm);
            returnModal.addEventListener('modalHidden', resetForm);
            
            // Also handle when our custom closeModal function is called
            window.customCloseModal = function() {
                resetForm();
                closeModal();
            };
        }
    }
});
</script>

<style>
@media print {
    .btn, .card-header .btn-group, .d-flex.justify-content-between {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>
@endpush