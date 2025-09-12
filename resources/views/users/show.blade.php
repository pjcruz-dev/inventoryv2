@extends('layouts.app')

@section('title', 'User Details')
@section('page-title', $user->first_name . ' ' . $user->last_name)

@section('page-actions')
    <div class="d-flex gap-2">
        @if($user->assignedAssets->count() > 0)
            <a href="{{ route('assets.print-single-employee-assets', $user) }}" class="btn btn-outline-info btn-sm" target="_blank">
                <i class="fas fa-print me-1"></i>Print Assets
            </a>
        @endif
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit User
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Information</h5>
                    @php
                        $statusConfig = [
                            1 => ['label' => 'Active', 'class' => 'success'],
                            0 => ['label' => 'Inactive', 'class' => 'danger'],
                            2 => ['label' => 'Suspended', 'class' => 'warning']
                        ];
                        $config = $statusConfig[$user->status] ?? ['label' => 'Unknown', 'class' => 'secondary'];
                    @endphp
                    <span class="badge bg-{{ $config['class'] }} fs-6">
                        {{ $config['label'] }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Full Name:</dt>
                            <dd class="col-sm-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                </div>
                            </dd>
                            
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ $user->email }}
                                </a>
                            </dd>
                            
                            <dt class="col-sm-4">Employee ID:</dt>
                            <dd class="col-sm-8">
                                @if($user->employee_id)
                                    <code class="fs-6">{{ $user->employee_id }}</code>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $user->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Department:</dt>
                            <dd class="col-sm-8">
                                @if($user->department)
                                    <span class="badge bg-info">{{ $user->department->name }}</span>
                                @else
                                    <span class="text-muted">No Department</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Job Title:</dt>
                            <dd class="col-sm-8">{{ $user->job_title ?? 'Not specified' }}</dd>
                            
                            <dt class="col-sm-4">Member Since:</dt>
                            <dd class="col-sm-8">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $user->created_at->format('M d, Y') }}
                                <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                            </dd>
                            
                            <dt class="col-sm-4">Last Updated:</dt>
                            <dd class="col-sm-8">
                                <i class="fas fa-clock me-1"></i>
                                {{ $user->updated_at->format('M d, Y \a\t g:i A') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assigned Assets -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Assigned Assets</h6>
                    <span class="badge bg-secondary">{{ $user->assignedAssets->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($user->assignedAssets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Assigned Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->assignedAssets as $asset)
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
                                            @if($asset->assigned_date)
                                                {{ $asset->assigned_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not recorded</span>
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
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Assets Assigned</h6>
                        <p class="text-muted mb-3">This user currently has no assets assigned to them.</p>
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
                            <h6 class="timeline-title">User Account Created</h6>
                            <p class="timeline-description text-muted mb-0">
                                User account was created and added to the system
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $user->created_at->format('M d, Y \a\t g:i A') }}
                            </small>
                        </div>
                    </div>
                    
                    @if($user->updated_at != $user->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Profile Updated</h6>
                                <p class="timeline-description text-muted mb-0">
                                    User profile information was last modified
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $user->updated_at->format('M d, Y \a\t g:i A') }}
                                </small>
                            </div>
                        </div>
                    @endif
                    
                    @if($user->assignedAssets->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Assets Assigned</h6>
                                <p class="timeline-description text-muted mb-2">
                                    {{ $user->assignedAssets->count() }} asset(s) currently assigned:
                                </p>
                                <div class="mb-2">
                                    @foreach($user->assignedAssets as $asset)
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-{{ $asset->category->name === 'Computer' ? 'desktop' : ($asset->category->name === 'Monitor' ? 'tv' : ($asset->category->name === 'Printer' ? 'print' : 'box')) }} text-primary me-2"></i>
                                            <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none me-2">
                                                <strong>{{ $asset->asset_tag }}</strong>
                                            </a>
                                            <span class="text-muted">{{ $asset->name }}</span>
                                            @if($asset->assigned_date)
                                                <small class="text-muted ms-auto">
                                                    {{ \Carbon\Carbon::parse($asset->assigned_date)->format('M d, Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-box me-1"></i>
                                    Active assignments
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
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                    
                    @if($user->status === 1)
                        <button class="btn btn-outline-warning" onclick="changeUserStatus('0')">
                            <i class="fas fa-user-slash me-2"></i>Deactivate User
                        </button>
                    @else
                        <button class="btn btn-outline-success" onclick="changeUserStatus('active')">
                            <i class="fas fa-user-check me-2"></i>Activate User
                        </button>
                    @endif
                    
                    <button class="btn btn-outline-info" onclick="sendPasswordReset()">
                        <i class="fas fa-key me-2"></i>Send Password Reset
                    </button>
                    
                    <a href="{{ route('assets.index', ['assigned_to' => $user->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>View User's Assets
                    </a>
                    
                    <hr>
                    
                    <form method="POST" action="{{ route('users.destroy', $user) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                {{ $user->assignedAssets->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </form>
                    
                    @if($user->assignedAssets->count() > 0)
                        <small class="text-muted text-center d-block mt-1">
                            Cannot delete user with assigned assets
                        </small>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- User Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">User Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $user->created_at->diffInDays(now()) }}</h4>
                            <small class="text-muted">Days Active</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $user->assignedAssets->count() }}</h4>
                        <small class="text-muted">Assets</small>
                    </div>
                </div>
                
                @if($user->assignedAssets->count() > 0)
                    <hr>
                    <div class="row text-center">
                        <div class="col-12">
                            <h5 class="text-info mb-0">
                                ₱{{ number_format($user->assignedAssets->sum('cost'), 2) }}
                            </h5>
                            <small class="text-muted">Total Asset Value</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Department Information -->
        @if($user->department)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Department Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-building text-primary me-2"></i>
                        <strong>{{ $user->department->name }}</strong>
                    </div>
                    
                    @if($user->department->description)
                        <p class="text-muted small mb-2">{{ $user->department->description }}</p>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ $user->department->users->count() }} member(s)
                        </small>
                        <a href="{{ route('departments.show', $user->department) }}" class="btn btn-outline-primary btn-sm">
                            View Department
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Contact Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-envelope text-primary me-2"></i>
                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                        {{ $user->email }}
                    </a>
                </div>
                
                @if($user->phone)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                            {{ $user->phone }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
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
    function changeUserStatus(status) {
        const action = status === 'active' ? 'activate' : 'deactivate';
        
        if (confirm(`Are you sure you want to ${action} this user?`)) {
            // Create a form to submit the status change
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("users.update", $user) }}';
            
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
            
            // Add all current user data
            const fields = {
                'first_name': '{{ $user->first_name }}',
                'last_name': '{{ $user->last_name }}',
                'email': '{{ $user->email }}',
                'employee_id': '{{ $user->employee_id }}',
                'department_id': '{{ $user->department_id }}',
                'job_title': '{{ $user->job_title }}',
                'phone': '{{ $user->phone }}',
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
    
    function sendPasswordReset() {
        if (confirm('Send password reset email to {{ $user->email }}?')) {
            // This would typically make an AJAX call to send password reset
            alert('Password reset email would be sent to {{ $user->email }}');
            console.log('Sending password reset email...');
        }
    }
</script>
@endsection