@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permissions Management')

@section('page-actions')
    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Permission
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Search and Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('permissions.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search permissions..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Permissions Table Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Permissions ({{ $permissions->total() }})</h5>
            </div>
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
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('permissions.show', $permission) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($permissions->hasPages())
                        <div class="pagination-wrapper">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Showing {{ $permissions->firstItem() }} to {{ $permissions->lastItem() }} of {{ $permissions->total() }} permissions
                                </div>
                                <div>
                                    {{ $permissions->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No permissions found</h5>
                        <p class="text-muted">Start by creating your first permission.</p>
                        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Permission
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection