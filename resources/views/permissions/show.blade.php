@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Permission Details</h3>
                    <div class="btn-group">
                        @if(!in_array($permission->name, ['view-assets', 'create-assets', 'edit-assets', 'delete-assets', 'manage-users', 'manage-roles']))
                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Permissions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $permission->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $permission->description ?? 'No description provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guard Name:</strong></td>
                                    <td>{{ $permission->guard_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if(in_array($permission->name, ['view-assets', 'create-assets', 'edit-assets', 'delete-assets', 'manage-users', 'manage-roles']))
                                            <span class="badge bg-danger">System Permission</span>
                                        @else
                                            <span class="badge bg-primary">Custom Permission</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $permission->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $permission->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Statistics</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Roles with this Permission:</strong></td>
                                    <td>{{ $permission->roles->count() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Users with this Permission:</strong></td>
                                    <td>{{ $permission->users->count() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($permission->roles->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Roles with this Permission</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Description</th>
                                            <th>Guard</th>
                                            <th>Users Count</th>
                                            <th>Assigned Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permission->roles as $role)
                                        <tr>
                                            <td>
                                                <a href="{{ route('roles.show', $role) }}" class="text-decoration-none">
                                                    {{ $role->name }}
                                                </a>
                                            </td>
                                            <td>{{ $role->description ?? 'No description' }}</td>
                                            <td><span class="badge bg-secondary">{{ $role->guard_name }}</span></td>
                                            <td>{{ $role->users->count() }}</td>
                                            <td>{{ $role->pivot->created_at ? $role->pivot->created_at->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> This permission is not assigned to any roles.
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($permission->users->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Users with Direct Permission</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                            <th>Position</th>
                                            <th>Assigned Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permission->users as $user)
                                        <tr>
                                            <td>
                                                <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->department->name ?? 'N/A' }}</td>
                                            <td>{{ $user->position ?? 'N/A' }}</td>
                                            <td>{{ $user->pivot->created_at ? $user->pivot->created_at->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> No users have this permission assigned directly.
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection