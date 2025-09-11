@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-print"></i> Printer Details
                    </h4>
                    <div>
                        <a href="{{ route('printers.edit', $printer) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('printers.index') }}" class="btn btn-secondary btn-sm">
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
                                                <span class="badge badge-secondary badge-lg">{{ $printer->asset->asset_tag }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $printer->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $printer->asset->status == 'Available' ? 'success' : ($printer->asset->status == 'In Use' ? 'primary' : 'warning') }} badge-lg">
                                                    {{ $printer->asset->status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Movement:</strong></td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    {{ $printer->asset->movement }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Date:</strong></td>
                                            <td>{{ $printer->asset->purchase_date ? $printer->asset->purchase_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Cost:</strong></td>
                                            <td>{{ $printer->asset->cost ? '₱' . number_format($printer->asset->cost, 2) : 'N/A' }}</td>
                                        </tr>
                                        @if($printer->asset->user)
                                        <tr>
                                            <td><strong>Assigned To:</strong></td>
                                            <td>
                                                <i class="fas fa-user"></i> {{ $printer->asset->user->name }}
                                                <br><small class="text-muted">{{ $printer->asset->user->email }}</small>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($printer->asset->department)
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td>
                                                <i class="fas fa-building"></i> {{ $printer->asset->department->name }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($printer->asset->vendor)
                                        <tr>
                                            <td><strong>Vendor:</strong></td>
                                            <td>
                                                <i class="fas fa-store"></i> {{ $printer->asset->vendor->name }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Printer Specifications -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-print"></i> Printer Specifications</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    <i class="fas fa-print"></i> {{ $printer->type }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Color Support:</strong></td>
                                            <td>
                                                @if($printer->color_support)
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="fas fa-palette"></i> Color Printing
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary badge-lg">
                                                        <i class="fas fa-circle"></i> Monochrome Only
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Network Enabled:</strong></td>
                                            <td>
                                                @if($printer->network_enabled)
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="fas fa-wifi"></i> Network Enabled
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary badge-lg">
                                                        <i class="fas fa-times"></i> Local Connection Only
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duplex Printing:</strong></td>
                                            <td>
                                                @if($printer->duplex_printing)
                                                    <span class="badge badge-success badge-lg">
                                                        <i class="fas fa-copy"></i> Duplex Supported
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary badge-lg">
                                                        <i class="fas fa-times"></i> Single-sided Only
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($printer->print_speed)
                                        <tr>
                                            <td><strong>Print Speed:</strong></td>
                                            <td>
                                                <i class="fas fa-tachometer-alt"></i> {{ $printer->print_speed }} pages per minute
                                            </td>
                                        </tr>
                                        @endif
                                        @if($printer->max_paper_size)
                                        <tr>
                                            <td><strong>Max Paper Size:</strong></td>
                                            <td>
                                                <i class="fas fa-file"></i> {{ $printer->max_paper_size }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $printer->created_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $printer->updated_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if($printer->asset->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Description</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $printer->asset->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Printer Visual Representation -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visual Preview</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="printer-preview" style="display: inline-block; position: relative;">
                                        <!-- Printer Body -->
                                        <div class="printer-body" style="
                                            width: 250px; 
                                            height: 120px;
                                            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
                                            border-radius: 12px;
                                            position: relative;
                                            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
                                            margin-bottom: 10px;
                                        ">
                                            <!-- Paper Tray -->
                                            <div style="
                                                position: absolute;
                                                bottom: -5px;
                                                left: 20px;
                                                width: 210px;
                                                height: 15px;
                                                background: #7f8c8d;
                                                border-radius: 0 0 8px 8px;
                                                box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
                                            "></div>
                                            
                                            <!-- Control Panel -->
                                            <div style="
                                                position: absolute;
                                                top: 15px;
                                                right: 20px;
                                                width: 60px;
                                                height: 40px;
                                                background: #2c3e50;
                                                border-radius: 6px;
                                                border: 2px solid #34495e;
                                            ">
                                                <!-- Power Button -->
                                                <div style="
                                                    position: absolute;
                                                    top: 8px;
                                                    left: 8px;
                                                    width: 8px;
                                                    height: 8px;
                                                    background: {{ $printer->asset->status == 'In Use' ? '#27ae60' : '#95a5a6' }};
                                                    border-radius: 50%;
                                                    box-shadow: 0 0 4px rgba(0,0,0,0.3);
                                                "></div>
                                                <!-- Display -->
                                                <div style="
                                                    position: absolute;
                                                    top: 20px;
                                                    left: 8px;
                                                    width: 40px;
                                                    height: 12px;
                                                    background: #1abc9c;
                                                    border-radius: 2px;
                                                    font-size: 6px;
                                                    color: white;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                ">READY</div>
                                            </div>
                                            
                                            <!-- Printer Info -->
                                            <div style="
                                                position: absolute;
                                                top: 50%;
                                                left: 30px;
                                                transform: translateY(-50%);
                                                color: white;
                                                text-align: left;
                                                font-size: 11px;
                                                line-height: 1.3;
                                            ">
                                                <div style="font-weight: bold; margin-bottom: 4px;">
                                                    <i class="fas fa-print"></i> {{ $printer->type }}
                                                </div>
                                                @if($printer->color_support)
                                                    <div><i class="fas fa-palette" style="color: #e74c3c;"></i> Color</div>
                                                @else
                                                    <div><i class="fas fa-circle" style="color: #95a5a6;"></i> Mono</div>
                                                @endif
                                                @if($printer->network_enabled)
                                                    <div><i class="fas fa-wifi" style="color: #3498db;"></i> Network</div>
                                                @endif
                                                @if($printer->duplex_printing)
                                                    <div><i class="fas fa-copy" style="color: #f39c12;"></i> Duplex</div>
                                                @endif
                                                @if($printer->print_speed)
                                                    <div><i class="fas fa-tachometer-alt"></i> {{ $printer->print_speed }}ppm</div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Output Tray -->
                                        <div style="
                                            width: 200px;
                                            height: 8px;
                                            background: #95a5a6;
                                            margin: 0 auto;
                                            border-radius: 4px;
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                                        "></div>
                                        
                                        <!-- Paper Size Indicator -->
                                        @if($printer->max_paper_size)
                                        <div style="
                                            margin-top: 15px;
                                            font-size: 12px;
                                            color: #7f8c8d;
                                            font-weight: bold;
                                        ">
                                            <i class="fas fa-file"></i> Max: {{ $printer->max_paper_size }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Printer Features Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-list-check"></i> Features Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="feature-item">
                                                <div class="feature-icon mb-2">
                                                    @if($printer->color_support)
                                                        <i class="fas fa-palette fa-2x text-success"></i>
                                                    @else
                                                        <i class="fas fa-circle fa-2x text-secondary"></i>
                                                    @endif
                                                </div>
                                                <h6>Color Support</h6>
                                                <p class="text-muted mb-0">{{ $printer->color_support ? 'Full Color' : 'Monochrome' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="feature-item">
                                                <div class="feature-icon mb-2">
                                                    @if($printer->network_enabled)
                                                        <i class="fas fa-wifi fa-2x text-success"></i>
                                                    @else
                                                        <i class="fas fa-ethernet fa-2x text-secondary"></i>
                                                    @endif
                                                </div>
                                                <h6>Connectivity</h6>
                                                <p class="text-muted mb-0">{{ $printer->network_enabled ? 'Network Ready' : 'Local Only' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="feature-item">
                                                <div class="feature-icon mb-2">
                                                    @if($printer->duplex_printing)
                                                        <i class="fas fa-copy fa-2x text-success"></i>
                                                    @else
                                                        <i class="fas fa-file fa-2x text-secondary"></i>
                                                    @endif
                                                </div>
                                                <h6>Duplex Printing</h6>
                                                <p class="text-muted mb-0">{{ $printer->duplex_printing ? 'Automatic' : 'Manual Only' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="feature-item">
                                                <div class="feature-icon mb-2">
                                                    <i class="fas fa-tachometer-alt fa-2x text-info"></i>
                                                </div>
                                                <h6>Print Speed</h6>
                                                <p class="text-muted mb-0">{{ $printer->print_speed ? $printer->print_speed . ' ppm' : 'Not specified' }}</p>
                                            </div>
                                        </div>
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
                                    <a href="{{ route('printers.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                                <div>
                                    @if($printer->asset->assignedUser)
                                        <button type="button" class="btn btn-outline-danger me-2" onclick="unassignAsset({{ $printer->asset->id }})">
                                            <i class="fas fa-user-times"></i> Unassign User
                                        </button>
                                        <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#reassignModal">
                                            <i class="fas fa-exchange-alt"></i> Reassign User
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                                            <i class="fas fa-user-plus"></i> Assign User
                                        </button>
                                    @endif
                                    <a href="{{ route('printers.edit', $printer) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Printer
                                    </a>
                                    <form action="{{ route('printers.destroy', $printer) }}" method="POST" class="d-inline ml-2"
                                          onsubmit="return confirm('Are you sure you want to delete this printer? This action cannot be undone.')">
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
                <h5 class="modal-title" id="assignModalLabel">Assign User to Printer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST" action="{{ route('assets.assign', $printer->asset) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Select User</label>
                        <input type="text" class="form-control mb-2" id="userFilter" placeholder="Search users by name or department..." onkeyup="filterUsers()">
                        <select class="form-select" id="assigned_to" name="assigned_to" required size="6" style="height: 150px;">
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 'active')->orderBy('first_name')->get() as $user)
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
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about this assignment..."></textarea>
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
                <h5 class="modal-title" id="reassignModalLabel">Reassign Printer to Another User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.reassign', $printer->asset) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Currently assigned to: <strong>{{ $printer->asset->assignedUser->first_name ?? 'N/A' }} {{ $printer->asset->assignedUser->last_name ?? '' }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_assigned_to" class="form-label">Select New User</label>
                        <input type="text" class="form-control mb-2" id="reassignUserFilter" placeholder="Search users by name or department..." onkeyup="filterReassignUsers()">
                        <select class="form-select" id="new_assigned_to" name="new_assigned_to" required size="6" style="height: 150px;">
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 'active')->where('id', '!=', $printer->asset->assigned_to)->orderBy('first_name')->get() as $user)
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

.printer-preview {
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
}

.feature-item {
    padding: 1rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    height: 100%;
    transition: transform 0.2s ease;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.feature-icon {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
function unassignAsset(assetId) {
    if (confirm('Are you sure you want to unassign this user from the printer?')) {
        fetch(`/assets/${assetId}/unassign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error unassigning user: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error unassigning user');
        });
    }
}

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