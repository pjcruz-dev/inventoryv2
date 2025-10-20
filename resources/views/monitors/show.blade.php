@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-tv"></i> Monitor Details
                    </h4>
                    <div>
                        <a href="{{ route('monitors.edit', $monitor) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('monitors.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Asset Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-tag"></i> Asset Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Asset Tag:</strong></td>
                                            <td>
                                                <span class="badge badge-secondary badge-lg">{{ $monitor->asset->asset_tag }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $monitor->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $monitor->asset->status == 'Available' ? 'success' : ($monitor->asset->status == 'In Use' ? 'primary' : 'warning') }} badge-lg">
                                                    {{ $monitor->asset->status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Movement:</strong></td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    {{ $monitor->asset->movement }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Date:</strong></td>
                                            <td>{{ $monitor->asset->purchase_date ? $monitor->asset->purchase_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Cost:</strong></td>
                                            <td>{{ $monitor->asset->cost ? '₱' . number_format($monitor->asset->cost, 2) : 'N/A' }}</td>
                                        </tr>
                                        @if($monitor->asset->user)
                                        <tr>
                                            <td><strong>Assigned To:</strong></td>
                                            <td>
                                                <i class="fas fa-user"></i> {{ $monitor->asset->user->name }}
                                                <br><small class="text-muted">{{ $monitor->asset->user->email }}</small>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($monitor->asset->department)
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td>
                                                <i class="fas fa-building"></i> {{ $monitor->asset->department->name }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($monitor->asset->vendor)
                                        <tr>
                                            <td><strong>Vendor:</strong></td>
                                            <td>
                                                <i class="fas fa-store"></i> {{ $monitor->asset->vendor->name }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Monitor Specifications -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-tv"></i> Monitor Specifications</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Size:</strong></td>
                                            <td>
                                                <i class="fas fa-expand-arrows-alt"></i> {{ $monitor->size }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Resolution:</strong></td>
                                            <td>
                                                <i class="fas fa-desktop"></i> {{ $monitor->resolution }}
                                                @php
                                                    $resolutionNames = [
                                                        '1920x1080' => 'Full HD',
                                                        '2560x1440' => 'QHD',
                                                        '3840x2160' => '4K UHD',
                                                        '1366x768' => 'HD',
                                                        '1680x1050' => 'WSXGA+',
                                                        '1920x1200' => 'WUXGA',
                                                        '2560x1600' => 'WQXGA',
                                                        '5120x2880' => '5K'
                                                    ];
                                                @endphp
                                                @if(isset($resolutionNames[$monitor->resolution]))
                                                    <br><small class="text-muted">{{ $resolutionNames[$monitor->resolution] }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Panel Type:</strong></td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    <i class="fas fa-tv"></i> {{ $monitor->panel_type }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Aspect Ratio:</strong></td>
                                            <td>
                                                @php
                                                    if (preg_match('/(\d+)x(\d+)/', $monitor->resolution, $matches)) {
                                                        $width = (int)$matches[1];
                                                        $height = (int)$matches[2];
                                                        $gcd = function($a, $b) use (&$gcd) {
                                                            return $b ? $gcd($b, $a % $b) : $a;
                                                        };
                                                        $divisor = $gcd($width, $height);
                                                        $aspectRatio = ($width / $divisor) . ':' . ($height / $divisor);
                                                    } else {
                                                        $aspectRatio = 'Unknown';
                                                    }
                                                @endphp
                                                <i class="fas fa-crop-alt"></i> {{ $aspectRatio }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $monitor->created_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $monitor->updated_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if($monitor->asset->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Description</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $monitor->asset->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Monitor Visual Representation -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visual Preview</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="monitor-preview" style="display: inline-block; position: relative;">
                                        <div class="monitor-screen" style="
                                            width: 200px; 
                                            height: {{ $monitor->resolution && preg_match('/(\d+)x(\d+)/', $monitor->resolution, $matches) ? round(200 * (int)$matches[2] / (int)$matches[1]) : 120 }}px;
                                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                            border: 8px solid #2c3e50;
                                            border-radius: 8px;
                                            position: relative;
                                            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                                        ">
                                            <div style="
                                                position: absolute;
                                                top: 50%;
                                                left: 50%;
                                                transform: translate(-50%, -50%);
                                                color: white;
                                                text-align: center;
                                                font-size: 12px;
                                            ">
                                                <i class="fas fa-tv fa-2x mb-2"></i><br>
                                                {{ $monitor->size }}<br>
                                                {{ $monitor->resolution }}<br>
                                                {{ $monitor->panel_type }}
                                            </div>
                                        </div>
                                        <div style="
                                            width: 40px;
                                            height: 20px;
                                            background: #34495e;
                                            margin: 0 auto;
                                            border-radius: 0 0 8px 8px;
                                        "></div>
                                        <div style="
                                            width: 80px;
                                            height: 8px;
                                            background: #34495e;
                                            margin: 0 auto;
                                            border-radius: 4px;
                                        "></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('monitors.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                                <div>
                                    @if($monitor->asset->assignedUser)
                                        <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#reassignModal">
                                            <i class="fas fa-exchange-alt"></i> Reassign User
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                                            <i class="fas fa-user-plus"></i> Assign User
                                        </button>
                                    @endif
                                    <a href="{{ route('monitors.edit', $monitor) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Monitor
                                    </a>
                                    <form action="{{ route('monitors.destroy', $monitor) }}" method="POST" class="d-inline ml-2"
                                          onsubmit="return confirm('Are you sure you want to delete this monitor? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Assign User Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign User to Monitor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST" action="{{ route('assets.assign', $monitor->asset) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Select User</label>
                        <input type="text" class="form-control mb-2" id="userFilter" placeholder="Search users by name or department..." onkeyup="filterUsers()">
                        <select class="form-select" id="assigned_to" name="assigned_to" required size="6" style="height: 150px;">
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 1)->orderBy('first_name')->get() as $user)
                                <option value="{{ $user->id }}" data-name="{{ strtolower($user->first_name . ' ' . $user->last_name) }}" data-department="{{ strtolower($user->department->name ?? '') }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assignment Date</label>
                        <input type="date" class="form-control" id="assigned_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Assignment Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter assignment details, location, purpose, or any specific instructions for this monitor assignment..."></textarea>
                        <small class="form-text text-muted">Include details like: location, purpose, special instructions, or monitor configuration notes</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reassign User Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1" aria-labelledby="reassignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassignModalLabel">Reassign Monitor to Another User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.reassign', $monitor->asset) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Currently assigned to: <strong>{{ $monitor->asset->assignedUser->first_name ?? 'N/A' }} {{ $monitor->asset->assignedUser->last_name ?? '' }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_assigned_to" class="form-label">Select New User</label>
                        <input type="text" class="form-control mb-2" id="reassignUserFilter" placeholder="Search users by name or department..." onkeyup="filterReassignUsers()">
                        <select class="form-select" id="new_assigned_to" name="new_assigned_to" required size="6" style="height: 150px;">
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 1)->where('id', '!=', $monitor->asset->assigned_to)->orderBy('first_name')->get() as $user)
                                <option value="{{ $user->id }}" data-name="{{ strtolower($user->first_name . ' ' . $user->last_name) }}" data-department="{{ strtolower($user->department->name ?? '') }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assignment Date</label>
                        <input type="date" class="form-control" id="assigned_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Reason for reassignment or any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reassign User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}

.table-borderless td {
    border: none;
    padding: 0.5rem 0.75rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.monitor-preview {
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
}
</style>
@endpush

@push('scripts')
<script>

function filterUsers() {
    const filter = document.getElementById('userFilter').value.toLowerCase();
    const select = document.getElementById('assigned_to');
    const options = select.getElementsByTagName('option');
    
    for (let i = 1; i < options.length; i++) { // Skip first option ("Choose a user...")
        const option = options[i];
        const name = option.getAttribute('data-name') || '';
        const department = option.getAttribute('data-department') || '';
        const text = option.textContent.toLowerCase();
        
        if (name.includes(filter) || department.includes(filter) || text.includes(filter)) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
}

function filterReassignUsers() {
    const filter = document.getElementById('reassignUserFilter').value.toLowerCase();
    const select = document.getElementById('new_assigned_to');
    const options = select.getElementsByTagName('option');
    
    for (let i = 1; i < options.length; i++) { // Skip first option ("Choose a user...")
        const option = options[i];
        const name = option.getAttribute('data-name') || '';
        const department = option.getAttribute('data-department') || '';
        const text = option.textContent.toLowerCase();
        
        if (name.includes(filter) || department.includes(filter) || text.includes(filter)) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
}
</script>
@endpush