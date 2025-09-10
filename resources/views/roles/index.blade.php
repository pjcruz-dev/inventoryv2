@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles Management')

@section('page-actions')
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Role
    </a>
@endsection

@section('content')
<style>
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, var(--bs-white) 0%, rgba(var(--bs-primary-rgb), 0.02) 100%);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .filter-container {
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.05) 0%, rgba(var(--bs-info-rgb), 0.05) 100%);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid rgba(var(--bs-primary-rgb), 0.1);
    }
    
    .table-modern {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }
    
    .status-badge {
        background: linear-gradient(135deg, var(--bs-info) 0%, var(--bs-primary) 100%);
        border: none;
        color: white;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
    }
    
    .btn-action {
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 1px solid rgba(var(--bs-gray-300-rgb), 0.5);
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="row">
    <div class="col-12">
        <!-- Enhanced Filter Container -->
        <div class="card-modern mb-4">
            <div class="filter-container">
                <form method="GET" action="{{ route('roles.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-dark">
                            <i class="fas fa-search me-2 text-primary"></i>Search Roles
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search by name or description..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            @if(request('search'))
                                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-action">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enhanced Roles Table -->
        <div class="card-modern">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.1) 0%, rgba(var(--bs-info-rgb), 0.05) 100%); border-bottom: 1px solid rgba(var(--bs-primary-rgb), 0.1); border-radius: 16px 16px 0 0;">
                <h5 class="mb-0 fw-semibold text-dark">
                    <i class="fas fa-user-tag me-2 text-primary"></i>All Roles 
                    <span class="status-badge ms-2">{{ $roles->total() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @if($roles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead style="background: linear-gradient(135deg, rgba(var(--bs-light-rgb), 0.8) 0%, rgba(var(--bs-primary-rgb), 0.05) 100%);">
                                <tr>
                                    <th class="border-0 fw-semibold text-dark">
                                        <i class="fas fa-tag me-2 text-primary"></i>Name
                                    </th>
                                    <th class="border-0 fw-semibold text-dark">
                                        <i class="fas fa-info-circle me-2 text-info"></i>Description
                                    </th>
                                    <th class="border-0 fw-semibold text-dark">
                                        <i class="fas fa-key me-2 text-success"></i>Permissions
                                    </th>
                                    <th class="border-0 fw-semibold text-dark">
                                        <i class="fas fa-calendar me-2 text-warning"></i>Created
                                    </th>
                                    <th class="border-0 fw-semibold text-dark">
                                        <i class="fas fa-cogs me-2 text-secondary"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr class="border-0">
                                        <td class="border-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%); width: 40px; height: 40px;">
                                                    <i class="fas fa-user-tag"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark">{{ $role->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-0 py-3">
                                            <span class="text-muted">{{ $role->description ?? 'No description' }}</span>
                                        </td>
                                        <td class="border-0 py-3">
                                            <span class="status-badge">{{ $role->permissions->count() }} permissions</span>
                                        </td>
                                        <td class="border-0 py-3">
                                            <small class="text-muted fw-medium">{{ $role->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="border-0 py-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info btn-action" title="View Role">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-warning btn-action" title="Edit Role">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'User', 'IT Support']))
                                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to permanently delete this role? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Delete Role">
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
                    
                    <!-- Enhanced Pagination -->
                    <div class="d-flex justify-content-center p-4" style="background: linear-gradient(135deg, rgba(var(--bs-light-rgb), 0.3) 0%, rgba(var(--bs-primary-rgb), 0.02) 100%); border-radius: 0 0 16px 16px;">
                        {{ $roles->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-user-tag" style="font-size: 4rem; background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
                        </div>
                        <h5 class="text-dark fw-semibold mb-2">No roles found</h5>
                        <p class="text-muted mb-4">{{ request('search') ? 'No roles match your search criteria.' : 'Start by creating your first role to manage user permissions.' }}</p>
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-action" style="background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%); border: none; padding: 0.75rem 2rem;">
                            <i class="fas fa-plus me-2"></i>Add Role
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection