@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Permission: {{ $permission->name }}</h3>
                    <div class="btn-group">
                        @can('view_permissions')
                        <a href="{{ route('permissions.show', $permission) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @endcan
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Permissions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(in_array($permission->name, ['view-assets', 'create-assets', 'edit-assets', 'delete-assets', 'manage-users', 'manage-roles']))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>System Permission:</strong> This is a system permission and cannot be modified. 
                        You can only view its details and role assignments.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Permission Name</label>
                                <input type="text" class="form-control" value="{{ $permission->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Guard Name</label>
                                <input type="text" class="form-control" value="{{ $permission->guard_name }}" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" readonly>{{ $permission->description ?? 'No description provided' }}</textarea>
                    </div>
                    
                    @else
                    
                    <form action="{{ route('permissions.update', $permission) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $permission->name) }}" 
                                           placeholder="Enter permission name" required>
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
                                        <option value="web" {{ old('guard_name', $permission->guard_name) == 'web' ? 'selected' : '' }}>Web</option>
                                        <option value="api" {{ old('guard_name', $permission->guard_name) == 'api' ? 'selected' : '' }}>API</option>
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
                                      placeholder="Enter permission description (optional)">{{ old('description', $permission->description) }}</textarea>
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
                                               {{ in_array($role->id, old('roles', $permission->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
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

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('permissions.show', $permission) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Permission
                            </button>
                        </div>
                    </form>
                    
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(!in_array($permission->name, ['view-assets', 'create-assets', 'edit-assets', 'delete-assets', 'manage-users', 'manage-roles']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-roles');
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    
    if (selectAllCheckbox) {
        // Initialize select all checkbox state
        const updateSelectAllState = () => {
            const checkedCount = Array.from(roleCheckboxes).filter(cb => cb.checked).length;
            const totalCount = roleCheckboxes.length;
            
            selectAllCheckbox.checked = checkedCount === totalCount;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        };
        
        // Initial state
        updateSelectAllState();
        
        selectAllCheckbox.addEventListener('change', function() {
            roleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Update select all checkbox based on individual checkboxes
        roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllState);
        });
    }
});
</script>
@endif
@endsection