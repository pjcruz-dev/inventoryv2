@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Add Timeline Entry</h4>
                    <a href="{{ route('timeline.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Timeline
                    </a>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('timeline.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                <select name="asset_id" id="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                    <option value="">Select Asset</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->name }} ({{ $asset->asset_tag }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="action" class="form-label">Action <span class="text-danger">*</span></label>
                                <select name="action" id="action" class="form-select @error('action') is-invalid @enderror" required>
                                    <option value="">Select Action</option>
                                    <option value="assigned" {{ old('action') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="unassigned" {{ old('action') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                    <option value="transferred" {{ old('action') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="updated" {{ old('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                </select>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3" id="user-fields">
                            <div class="col-md-6">
                                <label for="from_user_id" class="form-label">From User</label>
                                <select name="from_user_id" id="from_user_id" class="form-select @error('from_user_id') is-invalid @enderror">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('from_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="to_user_id" class="form-label">To User</label>
                                <select name="to_user_id" id="to_user_id" class="form-select @error('to_user_id') is-invalid @enderror">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('to_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Add any additional notes about this timeline entry...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Timeline entries are automatically created when assets are assigned or transferred through the normal asset management process. Use this form only for manual entries or corrections.
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('timeline.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Timeline Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionSelect = document.getElementById('action');
    const userFields = document.getElementById('user-fields');
    const fromUserSelect = document.getElementById('from_user_id');
    const toUserSelect = document.getElementById('to_user_id');
    
    function toggleUserFields() {
        const action = actionSelect.value;
        
        if (action === 'assigned') {
            fromUserSelect.parentElement.style.display = 'none';
            toUserSelect.parentElement.style.display = 'block';
            fromUserSelect.value = '';
        } else if (action === 'unassigned') {
            fromUserSelect.parentElement.style.display = 'block';
            toUserSelect.parentElement.style.display = 'none';
            toUserSelect.value = '';
        } else if (action === 'transferred') {
            fromUserSelect.parentElement.style.display = 'block';
            toUserSelect.parentElement.style.display = 'block';
        } else {
            fromUserSelect.parentElement.style.display = 'none';
            toUserSelect.parentElement.style.display = 'none';
            fromUserSelect.value = '';
            toUserSelect.value = '';
        }
    }
    
    actionSelect.addEventListener('change', toggleUserFields);
    toggleUserFields(); // Initialize on page load
});
</script>
@endsection