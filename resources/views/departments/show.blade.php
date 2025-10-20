@extends('layouts.app')

@section('title', 'Department Details')
@section('page-title', $department->name)

@section('page-actions')
    <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-2"></i>Edit Department
    </a>
    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Departments
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Department Information</h5>
                    <span class="badge bg-{{ $department->status === 'active' ? 'success' : 'danger' }} fs-6">
                        {{ ucfirst($department->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Department Name:</dt>
                            <dd class="col-sm-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        {{ strtoupper(substr($department->name, 0, 2)) }}
                                    </div>
                                    <strong>{{ $department->name }}</strong>
                                </div>
                            </dd>
                            
                            <dt class="col-sm-4">Department Code:</dt>
                            <dd class="col-sm-8">
                                @if($department->code)
                                    <code class="fs-6">{{ $department->code }}</code>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Description:</dt>
                            <dd class="col-sm-8">
                                @if($department->description)
                                    {{ $department->description }}
                                @else
                                    <span class="text-muted">No description provided</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Location:</dt>
                            <dd class="col-sm-8">
                                @if($department->location)
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $department->location }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Manager:</dt>
                            <dd class="col-sm-8">
                                @if($department->manager)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($department->manager->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $department->manager->first_name }} {{ $department->manager->last_name }}</strong>
                                            <br><small class="text-muted">{{ $department->manager->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No manager assigned</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Contact Info:</dt>
                            <dd class="col-sm-8">
                                @if($department->email)
                                    <div class="mb-1">
                                        <a href="mailto:{{ $department->email }}" class="text-decoration-none">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $department->email }}
                                        </a>
                                    </div>
                                @endif
                                @if($department->phone)
                                    <div>
                                        <a href="tel:{{ $department->phone }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $department->phone }}
                                        </a>
                                    </div>
                                @endif
                                @if(!$department->email && !$department->phone)
                                    <span class="text-muted">No contact information</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Budget:</dt>
                            <dd class="col-sm-8">
                                @if($department->budget)
                                    <strong class="text-success">₱{{ number_format($department->budget, 2) }}</strong>
                                    <small class="text-muted">annually</small>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Parent Department:</dt>
                            <dd class="col-sm-8">
                                @if($department->parent)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-level-up-alt text-muted me-2"></i>
                                        <a href="{{ route('departments.show', $department->parent) }}" class="text-decoration-none">
                                            <strong>{{ $department->parent->name }}</strong>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-muted"><i class="fas fa-building me-1"></i>Main Department</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Created:</dt>
                            <dd class="col-sm-8">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $department->created_at->format('M d, Y') }}
                                <small class="text-muted">({{ $department->created_at->diffForHumans() }})</small>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Department Members -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Department Members</h6>
                    <span class="badge bg-secondary">{{ $department->users->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($department->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Job Title</th>
                                    <th>Status</th>
                                    <th>Assets</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                                    @if($user->id === $department->manager_id)
                                                        <span class="badge bg-warning text-dark ms-1">Manager</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->job_title ?? 'Not specified' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->assignedAssets->count() }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Members</h6>
                        <p class="text-muted mb-3">This department currently has no members assigned.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Add Members
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sub-Departments -->
        @if($department->children->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Sub-Departments</h6>
                        <span class="badge bg-secondary">{{ $department->children->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($department->children as $child)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($child->name, 0, 2)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">
                                                    <a href="{{ route('departments.show', $child) }}" class="text-decoration-none">
                                                        {{ $child->name }}
                                                    </a>
                                                </h6>
                                                @if($child->code)
                                                    <small class="text-muted">{{ $child->code }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($child->description)
                                            <p class="text-muted small mb-2" style="font-size: 0.85rem;">
                                                {{ Str::limit($child->description, 80) }}
                                            </p>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>{{ $child->users->count() }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-box me-1"></i>{{ $child->assets->count() }}
                                                </small>
                                            </div>
                                            <a href="{{ route('departments.show', $child) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Department Assets -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Department Assets</h6>
                    <span class="badge bg-secondary">{{ $department->assets->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($department->assets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Value</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->assets->take(10) as $asset)
                                    <tr>
                                        <td><code class="fs-6">{{ $asset->asset_tag }}</code></td>
                                        <td>{{ $asset->name }}</td>
                                        <td>
                                            @if($asset->category)
                                                <span class="badge bg-info">{{ $asset->category->name }}</span>
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $asset->status === 'active' ? 'success' : ($asset->status === 'inactive' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($asset->assignedUser)
                                                <small>{{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</small>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asset->cost)
                                    ₱{{ number_format($asset->cost, 2) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($department->assets->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('assets.index', ['department' => $department->id]) }}" class="btn btn-outline-primary">
                                View All {{ $department->assets->count() }} Assets
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Assets</h6>
                        <p class="text-muted mb-3">This department currently has no assets assigned.</p>
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Assign Assets
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Activity Timeline -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Activity Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Department Created</h6>
                            <p class="timeline-description text-muted mb-0">
                                Department was created and added to the system
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $department->created_at->format('M d, Y \a\t g:i A') }}
                            </small>
                        </div>
                    </div>
                    
                    @if($department->updated_at != $department->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Department Updated</h6>
                                <p class="timeline-description text-muted mb-0">
                                    Department information was last modified
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $department->updated_at->format('M d, Y \a\t g:i A') }}
                                </small>
                            </div>
                        </div>
                    @endif
                    
                    @if($department->manager)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Manager Assigned</h6>
                                <p class="timeline-description text-muted mb-0">
                                    {{ $department->manager->first_name }} {{ $department->manager->last_name }} assigned as department manager
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Current manager
                                </small>
                            </div>
                        </div>
                    @endif
                    
                    @if($department->users->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Members Added</h6>
                                <p class="timeline-description text-muted mb-0">
                                    {{ $department->users->count() }} member(s) currently in department
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    Active members
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Department
                    </a>
                    
                    @if($department->status === 'active')
                        <button class="btn btn-outline-warning" onclick="changeDepartmentStatus('inactive')">
                            <i class="fas fa-pause me-2"></i>Deactivate Department
                        </button>
                    @else
                        <button class="btn btn-outline-success" onclick="changeDepartmentStatus('active')">
                            <i class="fas fa-play me-2"></i>Activate Department
                        </button>
                    @endif
                    
                    <a href="{{ route('users.index', ['department' => $department->id]) }}" class="btn btn-outline-info">
                        <i class="fas fa-users me-2"></i>View Members
                    </a>
                    
                    <a href="{{ route('assets.index', ['department' => $department->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-box me-2"></i>View Assets
                    </a>
                    
                    <hr>
                    
                    <form method="POST" action="{{ route('departments.destroy', $department) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this department? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                {{ $department->users->count() > 0 || $department->assets->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash me-2"></i>Delete Department
                        </button>
                    </form>
                    
                    @if($department->users->count() > 0 || $department->assets->count() > 0)
                        <small class="text-muted text-center d-block mt-1">
                            Cannot delete department with members or assets
                        </small>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Department Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Department Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $department->created_at->diffInDays(now()) }}</h4>
                            <small class="text-muted">Days Active</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $department->users->count() }}</h4>
                        <small class="text-muted">Members</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-info mb-0">{{ $department->assets->count() }}</h4>
                            <small class="text-muted">Assets</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0">
                            @if($department->budget)
                                ₱{{ number_format($department->budget / 1000, 0) }}K
                            @else
                                N/A
                            @endif
                        </h4>
                        <small class="text-muted">Budget</small>
                    </div>
                </div>
                
                @if($department->assets->count() > 0)
                    <hr>
                    <div class="row text-center">
                        <div class="col-12">
                            <h5 class="text-success mb-0">
                                ₱{{ number_format($department->assets->sum('cost'), 2) }}
                            </h5>
                            <small class="text-muted">Total Asset Value</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Manager Information -->
        @if($department->manager)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Department Manager</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-lg bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                            {{ strtoupper(substr($department->manager->first_name, 0, 1) . substr($department->manager->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $department->manager->first_name }} {{ $department->manager->last_name }}</h6>
                            <small class="text-muted">{{ $department->manager->job_title ?? 'Department Manager' }}</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <a href="mailto:{{ $department->manager->email }}" class="text-decoration-none">
                            {{ $department->manager->email }}
                        </a>
                    </div>
                    
                    @if($department->manager->phone)
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <a href="tel:{{ $department->manager->phone }}" class="text-decoration-none">
                                {{ $department->manager->phone }}
                            </a>
                        </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('users.show', $department->manager) }}" class="btn btn-outline-primary btn-sm">
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Contact Information -->
        @if($department->email || $department->phone)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Contact Information</h6>
                </div>
                <div class="card-body">
                    @if($department->email)
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:{{ $department->email }}" class="text-decoration-none">
                                {{ $department->email }}
                            </a>
                        </div>
                    @endif
                    
                    @if($department->phone)
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <a href="tel:{{ $department->phone }}" class="text-decoration-none">
                                {{ $department->phone }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
.avatar-lg {
    width: 48px;
    height: 48px;
    font-size: 16px;
    font-weight: 600;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
}

.avatar-xs {
    width: 24px;
    height: 24px;
    font-size: 10px;
    font-weight: 600;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 20px;
    height: calc(100% + 20px);
    width: 2px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 600;
}

.timeline-description {
    font-size: 13px;
    margin-bottom: 5px;
}
</style>

<script>
    function changeDepartmentStatus(status) {
        const action = status === 'active' ? 'activate' : 'deactivate';
        
        if (confirm(`Are you sure you want to ${action} this department?`)) {
            // Create a form to submit the status change
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("departments.update", $department) }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PUT';
            form.appendChild(methodField);
            
            // Add all current department data
            const fields = {
                'name': '{{ $department->name }}',
                'code': '{{ $department->code }}',
                'description': '{{ $department->description }}',
                'manager_id': '{{ $department->manager_id }}',
                'location': '{{ $department->location }}',
                'budget': '{{ $department->budget }}',
                'phone': '{{ $department->phone }}',
                'email': '{{ $department->email }}',
                'status': status
            };
            
            Object.keys(fields).forEach(key => {
                if (fields[key]) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                }
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection