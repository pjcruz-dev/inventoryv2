@extends('layouts.app')

@section('title', 'Peripheral Details')
@section('page-title', 'Peripheral: ' . $peripheral->asset->name)

@section('page-actions')
    @can('edit_peripherals')
    <a href="{{ route('peripherals.edit', $peripheral) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-2"></i>Edit Peripheral
    </a>
    @endcan
    <a href="{{ route('peripherals.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Peripherals
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Peripheral Information</h5>
                    <span class="badge bg-{{ 
                        $peripheral->asset->status === 'Active' ? 'success' : 
                        ($peripheral->asset->status === 'Available' ? 'primary' : 
                        ($peripheral->asset->status === 'Inactive' ? 'danger' : 
                        ($peripheral->asset->status === 'Under Maintenance' ? 'warning' : 
                        ($peripheral->asset->status === 'Pending Confirmation' ? 'info' : 
                        ($peripheral->asset->status === 'Disposed' ? 'dark' : 'secondary')))))
                    }} fs-6">
                        {{ $peripheral->asset->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Asset Tag:</dt>
                            <dd class="col-sm-8">
                                <code class="fs-6">{{ $peripheral->asset->asset_tag }}</code>
                            </dd>
                            
                            <dt class="col-sm-4">Asset Name:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->name }}</dd>
                            
                            <dt class="col-sm-4">Peripheral Type:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-info">{{ $peripheral->type }}</span>
                            </dd>
                            
                            <dt class="col-sm-4">Interface:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-secondary">{{ $peripheral->interface }}</span>
                            </dd>
                            
                            <dt class="col-sm-4">Serial Number:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->serial_number ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Category:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->category->name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Model:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->model ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Vendor:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->vendor->name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Purchase Date:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->purchase_date ? $peripheral->asset->purchase_date->format('M d, Y') : 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Cost:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->cost ? '₱' . number_format($peripheral->asset->cost, 2) : 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
                
                @if($peripheral->asset->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <dt>Description:</dt>
                        <dd>{{ $peripheral->asset->description }}</dd>
                    </div>
                </div>
                @endif
                
                @if($peripheral->asset->location)
                <div class="row mt-3">
                    <div class="col-12">
                        <dt>Location:</dt>
                        <dd>{{ $peripheral->asset->location }}</dd>
                    </div>
                </div>
                @endif
                
                @if($peripheral->asset->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <dt>Notes:</dt>
                        <dd>{{ $peripheral->asset->notes }}</dd>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Assignment Information -->
        @if($peripheral->asset->assigned_to)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Assignment Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Assigned To:</dt>
                            <dd class="col-sm-8">
                                <strong>{{ $peripheral->asset->assignedUser->first_name ?? '' }} {{ $peripheral->asset->assignedUser->last_name ?? '' }}</strong>
                            </dd>
                            
                            <dt class="col-sm-4">Department:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->assignedUser->department->name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Assigned Date:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->assigned_date ? $peripheral->asset->assigned_date->format('M d, Y') : 'N/A' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Movement:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-primary">{{ $peripheral->asset->movement ?? 'N/A' }}</span>
                            </dd>
                            
                            <dt class="col-sm-4">Entity:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->entity ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Lifespan:</dt>
                            <dd class="col-sm-8">{{ $peripheral->asset->lifespan ? $peripheral->asset->lifespan . ' years' : 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Assignment Information</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    This peripheral is currently unassigned and available for assignment.
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                @can('edit_peripherals')
                <a href="{{ route('peripherals.edit', $peripheral) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                    <i class="fas fa-edit me-2"></i>Edit Peripheral
                </a>
                @endcan
                
                @can('view_assets')
                <a href="{{ route('assets.show', $peripheral->asset) }}" class="btn btn-outline-info btn-sm w-100 mb-2">
                    <i class="fas fa-eye me-2"></i>View Asset Details
                </a>
                @endcan
                
                @can('delete_peripherals')
                <form action="{{ route('peripherals.destroy', $peripheral) }}" method="POST" class="d-inline w-100" 
                      onsubmit="return confirm('Are you sure you want to delete this peripheral? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fas fa-trash me-2"></i>Delete Peripheral
                    </button>
                </form>
                @endcan
            </div>
        </div>
        
        <!-- Peripheral Details -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Peripheral Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Type</label>
                    <div>
                        <span class="badge bg-info fs-6">{{ $peripheral->type }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Connectivity</label>
                    <div>
                        <span class="badge bg-secondary fs-6">{{ $peripheral->interface }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Created</label>
                    <div class="text-muted">
                        {{ $peripheral->created_at->format('M d, Y') }}
                    </div>
                </div>
                
                <div class="mb-0">
                    <label class="form-label fw-semibold">Last Updated</label>
                    <div class="text-muted">
                        {{ $peripheral->updated_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Asset Status -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Asset Status</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <span class="badge bg-{{ 
                        $peripheral->asset->status === 'Active' ? 'success' : 
                        ($peripheral->asset->status === 'Available' ? 'primary' : 
                        ($peripheral->asset->status === 'Inactive' ? 'danger' : 
                        ($peripheral->asset->status === 'Under Maintenance' ? 'warning' : 
                        ($peripheral->asset->status === 'Pending Confirmation' ? 'info' : 
                        ($peripheral->asset->status === 'Disposed' ? 'dark' : 'secondary')))))
                    }} fs-5 px-3 py-2">
                        {{ $peripheral->asset->status }}
                    </span>
                </div>
                
                @if($peripheral->asset->movement)
                <div class="mt-3 text-center">
                    <small class="text-muted">Movement: </small>
                    <span class="badge bg-primary">{{ $peripheral->asset->movement }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
