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
                                                       class="btn btn-sm btn-outline-warning">
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
                                <button type="button" class="btn btn-success me-2" onclick="markAsReturned()">
                                    <i class="fas fa-undo me-1"></i>Mark as Returned
                                </button>
                            @endif
                            <a href="{{ route('asset-assignments.edit', $assetAssignment) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="printAssignment()">
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
            <form action="{{ route('asset-assignments.return', $assetAssignment) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Mark this asset assignment as returned?</p>
                    <div class="mb-3">
                        <label for="return_date_modal" class="form-label">Return Date</label>
                        <input type="date" name="return_date" id="return_date_modal" 
                               class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="return_notes" class="form-label">Return Notes</label>
                        <textarea name="notes" id="return_notes" class="form-control" rows="3" 
                                  placeholder="Add any notes about the return...">{{ $assetAssignment->notes }}</textarea>
                    </div>
                    <input type="hidden" name="status" value="returned">
                    <input type="hidden" name="asset_id" value="{{ $assetAssignment->asset_id }}">
                    <input type="hidden" name="user_id" value="{{ $assetAssignment->user_id }}">
                    <input type="hidden" name="assigned_date" value="{{ $assetAssignment->assigned_date?->format('Y-m-d') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Returned</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function markAsReturned() {
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    modal.show();
}

function printAssignment() {
    window.print();
}
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