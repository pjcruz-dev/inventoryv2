@extends('layouts.app')

@section('title', $vendor->name)
@section('page-title', $vendor->name)

@section('page-actions')
    <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-2"></i>Edit Vendor
    </a>
    <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Vendors
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Vendor Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Vendor Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Company Name</label>
                            <p class="fw-bold">{{ $vendor->name }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Contact Person</label>
                            <p class="fw-bold">{{ $vendor->contact_person }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Email Address</label>
                            <p class="fw-bold">
                                <a href="mailto:{{ $vendor->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-2"></i>{{ $vendor->email }}
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        @if($vendor->phone)
                        <div class="mb-3">
                            <label class="form-label text-muted">Phone Number</label>
                            <p class="fw-bold">
                                <a href="tel:{{ $vendor->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-2"></i>{{ $vendor->phone }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        @if($vendor->address)
                        <div class="mb-3">
                            <label class="form-label text-muted">Address</label>
                            <p class="fw-bold">{{ $vendor->address }}</p>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Member Since</label>
                            <p class="fw-bold">{{ $vendor->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Associated Assets -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Associated Assets ({{ $vendor->assets->count() }})</h5>
                @if($vendor->assets->count() > 0)
                <span class="badge bg-primary">Total Value: ₱{{ number_format($vendor->assets->sum('cost'), 2) }}</span>
                @endif
            </div>
            <div class="card-body">
                @if($vendor->assets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Asset Tag</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Purchase Price</th>
                                <th>Purchase Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendor->assets as $asset)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $asset->asset_tag }}</span>
                                </td>
                                <td>
                                    <strong>{{ $asset->name }}</strong>
                                    @if($asset->model)
                                    <br><small class="text-muted">{{ $asset->model }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->category)
                                    <span class="badge bg-info">{{ $asset->category->name }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($asset->status)
                                        @case('available')
                                            <span class="badge bg-success">Available</span>
                                            @break
                                        @case('assigned')
                                            <span class="badge bg-primary">Assigned</span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge bg-warning">Maintenance</span>
                                            @break
                                        @case('retired')
                                            <span class="badge bg-danger">Retired</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($asset->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($asset->cost)
                                            <strong>₱{{ number_format($asset->cost, 2) }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                </td>
                                <td>
                                    @if($asset->purchase_date)
                                    {{ $asset->purchase_date->format('M d, Y') }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
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
                    <h5 class="text-muted">No Assets Found</h5>
                    <p class="text-muted">This vendor doesn't have any associated assets yet.</p>
                    <a href="{{ route('assets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Asset
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Activity Timeline -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Vendor Created</h6>
                            <p class="text-muted mb-0">{{ $vendor->created_at->format('F d, Y \\a\\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($vendor->updated_at != $vendor->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Information Updated</h6>
                            <p class="text-muted mb-0">{{ $vendor->updated_at->format('F d, Y \\a\\t g:i A') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @foreach($vendor->assets->sortByDesc('created_at')->take(3) as $asset)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Asset Added</h6>
                            <p class="mb-1">{{ $asset->name }} ({{ $asset->asset_tag }})</p>
                            <p class="text-muted mb-0">{{ $asset->created_at->format('F d, Y \\a\\t g:i A') }}</p>
                        </div>
                    </div>
                    @endforeach
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
                    <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Vendor
                    </a>
                    
                    <button type="button" class="btn btn-outline-info" onclick="sendEmail()">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </button>
                    
                    @if($vendor->phone)
                    <a href="tel:{{ $vendor->phone }}" class="btn btn-outline-success">
                        <i class="fas fa-phone me-2"></i>Call Vendor
                    </a>
                    @endif
                    
                    <hr>
                    
                    <a href="{{ route('assets.create') }}?vendor={{ $vendor->id }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Add Asset
                    </a>
                    
                    <hr>
                    
                    @can('delete_vendors')
                    <button type="button" class="btn btn-outline-danger" onclick="deleteVendor()">
                        <i class="fas fa-trash me-2"></i>Delete Vendor
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        
        <!-- Vendor Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-0 text-primary">{{ $vendor->assets->count() }}</h4>
                            <small class="text-muted">Total Assets</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-success">₱{{ number_format($vendor->assets->sum('cost'), 2) }}</h4>
                        <small class="text-muted">Total Value</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="mb-0 text-info">{{ $vendor->assets->where('status', 'available')->count() }}</h5>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-warning">{{ $vendor->assets->where('status', 'assigned')->count() }}</h5>
                        <small class="text-muted">Assigned</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-circle me-3">
                        {{ strtoupper(substr($vendor->name, 0, 2)) }}
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $vendor->contact_person }}</h6>
                        <small class="text-muted">{{ $vendor->name }}</small>
                    </div>
                </div>
                
                <div class="contact-info">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-envelope text-muted me-3"></i>
                        <a href="mailto:{{ $vendor->email }}" class="text-decoration-none">{{ $vendor->email }}</a>
                    </div>
                    
                    @if($vendor->phone)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone text-muted me-3"></i>
                        <a href="tel:{{ $vendor->phone }}" class="text-decoration-none">{{ $vendor->phone }}</a>
                    </div>
                    @endif
                    
                    @if($vendor->address)
                    <div class="d-flex align-items-start">
                        <i class="fas fa-map-marker-alt text-muted me-3 mt-1"></i>
                        <span>{{ $vendor->address }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $vendor->name }}</strong>?</p>
                @if($vendor->assets->count() > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This vendor has {{ $vendor->assets->count() }} associated asset(s). 
                    You cannot delete this vendor until all assets are reassigned or removed.
                </div>
                @else
                <p class="text-muted">This action cannot be undone.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if($vendor->assets->count() == 0)
                @can('delete_vendors')
                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Vendor</button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.contact-info i {
    width: 20px;
    text-align: center;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
}

.btn {
    border-radius: 0.375rem;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}
</style>

<script>
    function deleteVendor() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    function sendEmail() {
        const email = '{{ $vendor->email }}';
        const subject = 'Regarding {{ $vendor->name }}';
        const mailtoLink = `mailto:${email}?subject=${encodeURIComponent(subject)}`;
        window.location.href = mailtoLink;
    }
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection