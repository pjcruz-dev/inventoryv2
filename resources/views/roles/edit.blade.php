@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Role: {{ $role->name }}</h3>
                    <div class="btn-group">
                        @can('view_roles')
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @endcan
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $role->name) }}" 
                                           placeholder="Enter role name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guard_name" class="form-label">Guard Name</label>
                                    <select class="form-select @error('guard_name') is-invalid @enderror" 
                                            id="guard_name" name="guard_name">
                                        <option value="web" {{ old('guard_name', $role->guard_name) == 'web' ? 'selected' : '' }}>Web</option>
                                        <option value="api" {{ old('guard_name', $role->guard_name) == 'api' ? 'selected' : '' }}>API</option>
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
                                      placeholder="Enter role description (optional)">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($allPermissions->count() > 0)
                        <div class="mb-4">
                            <label class="form-label">Assign Permissions</label>
                            
                            <!-- Global Controls -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="select-all-permissions">
                                                        <label class="form-check-label fw-bold text-success" for="select-all-permissions">
                                                            <i class="fas fa-check-square text-success"></i> Select All Permissions
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="select-none-permissions">
                                                        <label class="form-check-label fw-bold text-muted" for="select-none-permissions">
                                                            <i class="fas fa-square text-muted"></i> Select None
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Module Filter -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-filter"></i> Filter by Module</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <select class="form-select" id="module-filter">
                                                        <option value="">All Modules</option>
                                                        @foreach($permissionsByModule as $module => $permissions)
                                                            <option value="{{ $module }}">{{ $module }} ({{ $permissions->count() }} permissions)</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info btn-sm" id="select-module-all">
                                                        <i class="fas fa-check-double"></i> Select All in Module
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Permissions by Module -->
                            <div class="row">
                                @foreach($permissionsByModule as $module => $permissions)
                                <div class="col-12 mb-4 module-section" data-module="{{ $module }}">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cube"></i> {{ $module }}
                                                <span class="badge bg-secondary ms-2">{{ $permissions->count() }}</span>
                                            </h6>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-primary btn-sm select-module-permissions" data-module="{{ $module }}">
                                                    <i class="fas fa-check"></i> Select All
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm deselect-module-permissions" data-module="{{ $module }}">
                                                    <i class="fas fa-times"></i> Deselect All
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                                    <div class="form-check permission-item" data-permission-id="{{ $permission->id }}">
                                                        <input class="form-check-input permission-checkbox" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->id }}" 
                                                               id="permission_{{ $permission->id }}"
                                                               data-module="{{ $module }}"
                                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            <div class="permission-name">{{ $permission->name }}</div>
                                                            @if($permission->description)
                                                                <small class="text-muted d-block">{{ $permission->description }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            @error('permissions')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Role
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
    const selectAllCheckbox = document.getElementById('select-all-permissions');
    const selectNoneCheckbox = document.getElementById('select-none-permissions');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const moduleFilter = document.getElementById('module-filter');
    const moduleSections = document.querySelectorAll('.module-section');
    const selectModuleAllBtn = document.getElementById('select-module-all');
    
    // Initialize select all checkbox state
    const updateSelectAllState = () => {
        const checkedCount = Array.from(permissionCheckboxes).filter(cb => cb.checked).length;
        const totalCount = permissionCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        
        // Update select none checkbox
        selectNoneCheckbox.checked = checkedCount === 0;
    };
    
    // Initial state
    updateSelectAllState();
    
    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            if (this.checked) {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                selectNoneCheckbox.checked = false;
            }
        });
    }
    
    // Select None functionality
    if (selectNoneCheckbox) {
        selectNoneCheckbox.addEventListener('change', function() {
            if (this.checked) {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                selectAllCheckbox.checked = false;
            }
        });
    }
    
    // Update checkboxes based on individual checkbox changes
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });
    
    // Module filtering
    if (moduleFilter) {
        moduleFilter.addEventListener('change', function() {
            const selectedModule = this.value;
            
            moduleSections.forEach(section => {
                if (selectedModule === '' || section.dataset.module === selectedModule) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    }
    
    // Select all in filtered module
    if (selectModuleAllBtn) {
        selectModuleAllBtn.addEventListener('click', function() {
            const selectedModule = moduleFilter.value;
            
            if (selectedModule === '') {
                // Select all visible permissions
                permissionCheckboxes.forEach(checkbox => {
                    if (checkbox.closest('.module-section').style.display !== 'none') {
                        checkbox.checked = true;
                    }
                });
            } else {
                // Select all in specific module
                permissionCheckboxes.forEach(checkbox => {
                    if (checkbox.dataset.module === selectedModule) {
                        checkbox.checked = true;
                    }
                });
            }
            
            updateSelectAllState();
        });
    }
    
    // Module-specific select all buttons
    document.querySelectorAll('.select-module-permissions').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            
            permissionCheckboxes.forEach(checkbox => {
                if (checkbox.dataset.module === module) {
                    checkbox.checked = true;
                }
            });
            
            updateSelectAllState();
        });
    });
    
    // Module-specific deselect all buttons
    document.querySelectorAll('.deselect-module-permissions').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            
            permissionCheckboxes.forEach(checkbox => {
                if (checkbox.dataset.module === module) {
                    checkbox.checked = false;
                }
            });
            
            updateSelectAllState();
        });
    });
    
    // Add some visual feedback
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const permissionItem = this.closest('.permission-item');
            if (this.checked) {
                permissionItem.classList.add('bg-light');
            } else {
                permissionItem.classList.remove('bg-light');
            }
        });
        
        // Initial visual state
        if (checkbox.checked) {
            const permissionItem = checkbox.closest('.permission-item');
            permissionItem.classList.add('bg-light');
        }
    });
    
    // Add search functionality
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'Search permissions...';
    searchInput.id = 'permission-search';
    
    const moduleFilterCard = document.querySelector('#module-filter').closest('.card');
    moduleFilterCard.querySelector('.card-body').appendChild(searchInput);
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        permissionCheckboxes.forEach(checkbox => {
            const permissionItem = checkbox.closest('.permission-item');
            const permissionName = permissionItem.querySelector('.permission-name').textContent.toLowerCase();
            
            if (permissionName.includes(searchTerm)) {
                permissionItem.style.display = 'block';
            } else {
                permissionItem.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.permission-item {
    transition: all 0.3s ease;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid transparent;
}

.permission-item.bg-light {
    background-color: #f8f9fa !important;
    border-color: #28a745 !important;
}

.module-section {
    transition: all 0.3s ease;
}

.permission-checkbox:checked + label {
    color: #28a745;
    font-weight: 500;
}

.card-header h6 {
    color: #495057;
}

/* Enhanced checkbox and border styling */
.permission-item {
    transition: all 0.3s ease;
    padding: 12px;
    border-radius: 6px;
    border: 2px solid transparent;
    background-color: #ffffff;
}

.permission-item:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.permission-item.bg-light {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
    box-shadow: 0 2px 4px rgba(33, 150, 243, 0.1);
}

.permission-checkbox {
    transform: scale(1.2);
    margin-right: 8px;
}

.permission-checkbox:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.permission-checkbox:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.permission-checkbox:checked + label {
    color: #007bff;
    font-weight: 600;
}

.permission-name {
    font-weight: 500;
    margin-bottom: 2px;
}

/* Enhanced button styling for better harmony with card headers */
.btn-group-sm .btn {
    font-size: 0.75rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
}

.btn-group-sm .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group-sm .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Primary button (Select All) - matches card header styling */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

/* Secondary button (Deselect All) - neutral color */
.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
}

/* Info button (Select All in Module) - complementary blue */
.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

/* Global controls styling */
.form-check-label.text-success {
    color: #28a745 !important;
}

.form-check-label.text-muted {
    color: #6c757d !important;
}

#permission-search {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

#permission-search:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Card header enhancement */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-header h6 {
    color: #495057;
    font-weight: 600;
}

/* Badge styling */
.badge.bg-secondary {
    background-color: #6c757d !important;
}
</style>
@endsection