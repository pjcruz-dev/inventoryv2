@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Role Details</h3>
                    <div class="btn-group">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Roles
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
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $role->description ?? 'No description provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guard Name:</strong></td>
                                    <td>{{ $role->guard_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $role->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $role->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Statistics</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Total Permissions:</strong></td>
                                    <td>{{ $role->permissions->count() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Users with this Role:</strong></td>
                                    <td>{{ $role->users->count() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($role->permissions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Assigned Permissions</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Permission Name</th>
                                            <th>Description</th>
                                            <th>Guard</th>
                                            <th>Assigned Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($role->permissions as $permission)
                                        <tr>
                                            <td>
                                                <a href="{{ route('permissions.show', $permission) }}" class="text-decoration-none">
                                                    {{ $permission->name }}
                                                </a>
                                            </td>
                                            <td>{{ $permission->description ?? 'No description' }}</td>
                                            <td><span class="badge bg-secondary">{{ $permission->guard_name }}</span></td>
                                            <td>{{ $permission->pivot->created_at ? $permission->pivot->created_at->format('M d, Y') : 'N/A' }}</td>
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
                                <i class="fas fa-info-circle"></i> This role has no permissions assigned.
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($role->users->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Users with this Role</h5>
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
                                        @foreach($role->users as $user)
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
                                <i class="fas fa-exclamation-triangle"></i> No users are assigned to this role.
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