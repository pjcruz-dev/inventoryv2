@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles Management')

@section('page-actions')
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Role
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Search and Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('roles.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search roles..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Roles Table Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Roles ({{ $roles->total() }})</h5>
            </div>
            <div class="card-body">
                @if($roles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Permissions</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-user-tag"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $role->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $role->description ?? 'No description' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $role->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'User', 'IT Support']))
                                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this role?')">
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
                    @if($roles->hasPages())
                        <div class="pagination-wrapper">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} roles
                                </div>
                                <div>
                                    {{ $roles->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No roles found</h5>
                        <p class="text-muted">Start by creating your first role.</p>
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Role
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection