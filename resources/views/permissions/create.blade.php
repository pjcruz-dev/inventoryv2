@extends('layouts.app')

@section('title', 'Create Permission')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create New Permission</h3>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Permissions
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Enter permission name (e.g., manage-reports)" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Use lowercase letters, numbers, and hyphens only. Example: manage-reports, view-analytics
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guard_name" class="form-label">Guard Name</label>
                                    <select class="form-select @error('guard_name') is-invalid @enderror" 
                                            id="guard_name" name="guard_name">
                                        <option value="web" {{ old('guard_name', 'web') == 'web' ? 'selected' : '' }}>Web</option>
                                        <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>API</option>
                                    </select>
                                    @error('guard_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Enter permission description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($roles->count() > 0)
                        <div class="mb-4">
                            <label class="form-label">Assign to Roles</label>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-roles">
                                        <label class="form-check-label fw-bold" for="select-all-roles">
                                            Select All Roles
                                        </label>
                                    </div>
                                    <hr>
                                </div>
                                @foreach($roles as $role)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input role-checkbox" 
                                               type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}" 
                                               id="role_{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            <strong>{{ $role->name }}</strong>
                                            @if($role->description)
                                                <br><small class="text-muted">{{ $role->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> System permissions (like view-assets, manage-users) cannot be modified or deleted. 
                            Only create custom permissions for specific business needs.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Permission
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
    const selectAllCheckbox = document.getElementById('select-all-roles');
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            roleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Update select all checkbox based on individual checkboxes
        roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(roleCheckboxes).every(cb => cb.checked);
                const noneChecked = Array.from(roleCheckboxes).every(cb => !cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            });
        });
    }
});
</script>
@endsection