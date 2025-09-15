@extends('layouts.app')

@section('title', 'Create Asset Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Create Asset Assignment
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-assignments.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Asset Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select name="asset_id" id="asset_id" class="form-select searchable-select @error('asset_id') is-invalid @enderror" required>
                                        <option value="">Select an asset...</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
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
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                           value="{{ old('assigned_date', date('Y-m-d')) }}" required>
                                    @error('assigned_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Expected Return Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_return_date" class="form-label">Expected Return Date</label>
                                    <input type="date" name="expected_return_date" id="expected_return_date" 
                                           class="form-control @error('expected_return_date') is-invalid @enderror" 
                                           value="{{ old('expected_return_date') }}">
                                    @error('expected_return_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty for permanent assignment</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="assigned" {{ old('status', 'assigned') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="returned" {{ old('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Priority -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
                                        <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Add any additional notes about this assignment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Assignment Options -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Assignment Options</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="send_notification" 
                                                   id="send_notification" value="1" {{ old('send_notification', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="send_notification">
                                                Send email notification to assignee
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="require_confirmation" 
                                                   id="require_confirmation" value="1" {{ old('require_confirmation', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="require_confirmation">
                                                Require confirmation from assignee
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="update_asset_status" 
                                                   id="update_asset_status" value="1" {{ old('update_asset_status', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="update_asset_status">
                                                Update asset status to 'Assigned'
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('asset-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Create Assignment
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
// Auto-populate expected return date based on asset category or type
document.getElementById('asset_id').addEventListener('change', function() {
    const assetId = this.value;
    if (assetId) {
        // You can add logic here to set default return dates based on asset type
        // For now, we'll set a default 30 days from assignment date
        const assignedDate = document.getElementById('assigned_date').value;
        if (assignedDate) {
            const date = new Date(assignedDate);
            date.setDate(date.getDate() + 30);
            document.getElementById('expected_return_date').value = date.toISOString().split('T')[0];
        }
    }
});

// Update expected return date when assigned date changes
document.getElementById('assigned_date').addEventListener('change', function() {
    const assignedDate = this.value;
    const assetId = document.getElementById('asset_id').value;
    
    if (assignedDate && assetId) {
        const date = new Date(assignedDate);
        date.setDate(date.getDate() + 30);
        document.getElementById('expected_return_date').value = date.toISOString().split('T')[0];
    }
});
</script>
@endpush