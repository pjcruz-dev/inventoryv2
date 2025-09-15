@extends('layouts.app')

@section('title', 'Edit Asset Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Asset Assignment
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-assignments.update', $assetAssignment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Asset Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select name="asset_id" id="asset_id" class="form-select searchable-select @error('asset_id') is-invalid @enderror" required>
                                        <option value="">Select an asset...</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" 
                                                {{ old('asset_id', $assetAssignment->asset_id) == $asset->id ? 'selected' : '' }}>
                                                {{ $asset->name }} ({{ $asset->asset_tag }}) - {{ $asset->category->name ?? 'No Category' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- User Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Assign To <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select searchable-select @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select a user...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ old('user_id', $assetAssignment->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Assigned Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assigned_date" class="form-label">Assigned Date <span class="text-danger">*</span></label>
                                    <input type="date" name="assigned_date" id="assigned_date" 
                                           class="form-control @error('assigned_date') is-invalid @enderror" 
                                           value="{{ old('assigned_date', $assetAssignment->assigned_date?->format('Y-m-d')) }}" required>
                                    @error('assigned_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Return Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="return_date" class="form-label">Return Date</label>
                                    <input type="date" name="return_date" id="return_date" 
                                           class="form-control @error('return_date') is-invalid @enderror" 
                                           value="{{ old('return_date', $assetAssignment->return_date?->format('Y-m-d')) }}">
                                    @error('return_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Set when asset is returned</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="assigned" {{ old('status', $assetAssignment->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="pending" {{ old('status', $assetAssignment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="returned" {{ old('status', $assetAssignment->status) == 'returned' ? 'selected' : '' }}>Returned</option>
                                        <option value="overdue" {{ old('status', $assetAssignment->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Assigned By (Read-only) -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assigned_by_display" class="form-label">Assigned By</label>
                                    <input type="text" id="assigned_by_display" class="form-control" 
                                           value="{{ $assetAssignment->assignedBy->name ?? 'System' }}" readonly>
                                    <div class="form-text">Original assignor cannot be changed</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Add any additional notes about this assignment...">{{ old('notes', $assetAssignment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Assignment History -->
                        @if($assetAssignment->exists)
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Assignment History</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>Created:</strong> {{ $assetAssignment->created_at->format('M d, Y H:i') }}<br>
                                                    <strong>Last Updated:</strong> {{ $assetAssignment->updated_at->format('M d, Y H:i') }}
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                @if($assetAssignment->confirmation)
                                                    <small class="text-muted">
                                                        <strong>Confirmation Status:</strong> 
                                                        <span class="badge bg-{{ $assetAssignment->confirmation->status == 'confirmed' ? 'success' : ($assetAssignment->confirmation->status == 'declined' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($assetAssignment->confirmation->status) }}
                                                        </span><br>
                                                        @if($assetAssignment->confirmation->confirmed_at)
                                                            <strong>Confirmed At:</strong> {{ $assetAssignment->confirmation->confirmed_at->format('M d, Y H:i') }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <small class="text-muted">No confirmation record found</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('asset-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                            <div>
                                <a href="{{ route('asset-assignments.show', $assetAssignment) }}" class="btn btn-outline-info me-2">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Assignment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-set return date when status changes to 'returned'
document.getElementById('status').addEventListener('change', function() {
    const status = this.value;
    const returnDateField = document.getElementById('return_date');
    
    if (status === 'returned' && !returnDateField.value) {
        // Set return date to today if status is changed to returned and no return date is set
        returnDateField.value = new Date().toISOString().split('T')[0];
    } else if (status !== 'returned') {
        // Clear return date if status is not returned
        // returnDateField.value = ''; // Uncomment if you want to auto-clear
    }
});

// Validate return date is not before assigned date
document.getElementById('return_date').addEventListener('change', function() {
    const returnDate = new Date(this.value);
    const assignedDate = new Date(document.getElementById('assigned_date').value);
    
    if (returnDate < assignedDate) {
        alert('Return date cannot be before assigned date.');
        this.value = '';
    }
});
</script>
@endpush