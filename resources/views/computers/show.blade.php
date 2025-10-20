@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-desktop"></i> Computer Details
                    </h4>
                    <div>
                        @can('edit_computers')
                        <a href="{{ route('computers.edit', $computer) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endcan
                        <a href="{{ route('computers.index') }}" class="btn btn-secondary btn-sm">
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
                                                <span class="badge badge-secondary badge-lg">{{ $computer->asset->asset_tag }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $computer->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $computer->asset->status == 'Available' ? 'success' : ($computer->asset->status == 'In Use' ? 'primary' : 'warning') }} badge-lg">
                                                    {{ $computer->asset->status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Movement:</strong></td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    {{ $computer->asset->movement }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Date:</strong></td>
                                            <td>{{ $computer->asset->purchase_date ? $computer->asset->purchase_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Cost:</strong></td>
                                            <td>{{ $computer->asset->cost ? '₱' . number_format($computer->asset->cost, 2) : 'N/A' }}</td>
                                        </tr>
                                        @if($computer->asset->user)
                                        <tr>
                                            <td><strong>Assigned To:</strong></td>
                                            <td>
                                                <i class="fas fa-user"></i> {{ $computer->asset->user->name }}
                                                <br><small class="text-muted">{{ $computer->asset->user->email }}</small>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($computer->asset->department)
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td>
                                                <i class="fas fa-building"></i> {{ $computer->asset->department->name }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($computer->asset->vendor)
                                        <tr>
                                            <td><strong>Vendor:</strong></td>
                                            <td>
                                                <i class="fas fa-store"></i> {{ $computer->asset->vendor->name }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Computer Specifications -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-microchip"></i> Computer Specifications</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Processor:</strong></td>
                                            <td>
                                                <i class="fas fa-microchip"></i> {{ $computer->processor }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Memory (RAM):</strong></td>
                                            <td>
                                                <i class="fas fa-memory"></i> {{ $computer->memory }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Storage:</strong></td>
                                            <td>
                                                <i class="fas fa-hdd"></i> {{ $computer->storage }}
                                            </td>
                                        </tr>
                                        @if($computer->graphics_card)
                                        <tr>
                                            <td><strong>Graphics Card:</strong></td>
                                            <td>
                                                <i class="fas fa-tv"></i> {{ $computer->graphics_card }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($computer->operating_system)
                                        <tr>
                                            <td><strong>Operating System:</strong></td>
                                            <td>
                                                <i class="fab fa-{{ strtolower($computer->operating_system) == 'windows 10' || strtolower($computer->operating_system) == 'windows 11' ? 'windows' : (strtolower($computer->operating_system) == 'macos' ? 'apple' : 'linux') }}"></i> 
                                                {{ $computer->operating_system }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $computer->created_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $computer->updated_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if($computer->asset->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Description</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $computer->asset->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('computers.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                                <div>
                                    @if($computer->asset->assignedUser)
                                        <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#reassignModal">
                                            <i class="fas fa-exchange-alt"></i> Reassign User
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                                            <i class="fas fa-user-plus"></i> Assign User
                                        </button>
                                    @endif
                                    <a href="{{ route('computers.edit', $computer) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Computer
                                    </a>
                                    <form action="{{ route('computers.destroy', $computer) }}" method="POST" class="d-inline ml-2"
                                          onsubmit="return confirm('Are you sure you want to delete this computer? This action cannot be undone.')">
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
                <h5 class="modal-title" id="assignModalLabel">Assign User to Computer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST" action="{{ route('assets.assign', $computer->asset) }}">
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
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter assignment details, location, purpose, or any specific instructions for this computer assignment..."></textarea>
                        <small class="form-text text-muted">Include details like: location, purpose, special instructions, or computer configuration notes</small>
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
                <h5 class="modal-title" id="reassignModalLabel">Reassign Computer to Another User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.reassign', $computer->asset) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Currently assigned to: <strong>{{ $computer->asset->assignedUser->first_name ?? 'N/A' }} {{ $computer->asset->assignedUser->last_name ?? '' }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_assigned_to" class="form-label">Select New User</label>
                        <input type="text" class="form-control mb-2" id="reassignUserFilter" placeholder="Search users by name or department..." onkeyup="filterReassignUsers()">
                        <select class="form-select" id="new_assigned_to" name="new_assigned_to" required size="6" style="height: 150px;">
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 1)->where('id', '!=', $computer->asset->assigned_to)->orderBy('first_name')->get() as $user)
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
</style>
@endpush