@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permissions Management')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Permissions Table Card -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0 text-white">All Permissions</h5>
                        <small class="text-white-50">{{ $permissions->total() }} total permissions</small>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <a href="{{ route('permissions.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                <i class="fas fa-plus me-1"></i>Add Permission
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Search Section -->
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('permissions.index') }}" id="searchForm">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search permissions..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
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
            <div class="card-body">
                @if($permissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Roles</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-key"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $permission->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $permission->description ?? 'No description' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $permission->roles->count() }} roles</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $permission->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('view_permissions')
                                                <a href="{{ route('permissions.show', $permission) }}" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_permissions')
                                                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete_permissions')
                                                @php
                                                    $systemPermissions = [
                                                        'view_assets', 'create_assets', 'edit_assets', 'delete_assets',
                                                        'view_users', 'create_users', 'edit_users', 'delete_users',
                                                        'view_reports', 'manage_transfers', 'manage_maintenance', 'manage_disposals', 'view_logs'
                                                    ];
                                                @endphp
                                                @if(!in_array($permission->name, $systemPermissions))
                                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($permissions->hasPages())
                        <div class="pagination-wrapper mt-3">
                            {{ $permissions->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No permissions found</h5>
                        <p class="text-muted">Start by creating your first permission.</p>
                        <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add Permission
                        </a>
                    </div>
                @endif
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
</style>
@endpush