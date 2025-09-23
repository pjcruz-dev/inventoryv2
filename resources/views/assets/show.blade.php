@extends('layouts.app')

@section('title', 'Asset Details')
@section('page-title', 'Asset: ' . $asset->name)

@section('page-actions')
    @can('edit_assets')
    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary me-2">
        <i class="fas fa-edit me-2"></i>Edit Asset
    </a>
    @endcan
    @can('view_assets')
    <a href="{{ route('assets.qr-code', $asset) }}" class="btn btn-info me-2" target="_blank">
        <i class="fas fa-qrcode me-2"></i>QR Code
    </a>
    @endcan
    <a href="{{ route('assets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Assets
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Asset Information</h5>
                    <span class="badge bg-{{ 
                    $asset->status === 'active' ? 'success' : 
                    ($asset->status === 'deployed' ? 'primary' : 
                    ($asset->status === 'inactive' ? 'danger' : 
                    ($asset->status === 'problematic' ? 'danger' : 
                    ($asset->status === 'disposed' ? 'dark' : 
                    ($asset->status === 'maintenance' ? 'warning' : 
                    ($asset->status === 'pending_confirm' ? 'info' : 
                    ($asset->status === 'returned' ? 'secondary' : 
                    ($asset->status === 'new_arrived' ? 'success' : 'warning'))))))))
                }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Asset Tag:</dt>
                            <dd class="col-sm-8">
                                <code class="fs-6">{{ $asset->asset_tag }}</code>
                            </dd>
                            
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8">{{ $asset->name }}</dd>
                            
                            <dt class="col-sm-4">Category:</dt>
                            <dd class="col-sm-8">
                                @if($asset->category)
                                    <span class="badge bg-info">{{ $asset->category->name }}</span>
                                @else
                                    <span class="text-muted">No Category</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Model:</dt>
                            <dd class="col-sm-8">{{ $asset->model ?? 'Not specified' }}</dd>
                            
                            <dt class="col-sm-4">Serial Number:</dt>
                            <dd class="col-sm-8">
                                @if($asset->serial_number)
                                    <code class="fs-6">{{ $asset->serial_number }}</code>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </dd>
                            
                            @if($asset->category && strtolower($asset->category->name) == 'mobile devices' && $asset->mobile_number)
                            <dt class="col-sm-4">Mobile Number:</dt>
                            <dd class="col-sm-8">
                                <code class="fs-6">{{ $asset->mobile_number }}</code>
                            </dd>
                            @endif
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Vendor:</dt>
                            <dd class="col-sm-8">
                                @if($asset->vendor)
                                    <a href="{{ route('vendors.show', $asset->vendor) }}" class="text-decoration-none">
                                        {{ $asset->vendor->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Purchase Date:</dt>
                            <dd class="col-sm-8">
                                {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'Not specified' }}
                            </dd>
                            
                            <dt class="col-sm-4">Purchase Cost:</dt>
                    <dd class="col-sm-8">
                        @if($asset->cost)
                            <strong>₱{{ number_format($asset->cost, 2) }}</strong>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </dd>
                            
                            <dt class="col-sm-4">Assigned To:</dt>
                            <dd class="col-sm-8">
                                @if($asset->assignedUser)
                                    <a href="{{ route('users.show', $asset->assignedUser) }}" class="text-decoration-none">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}
                                    </a>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Location:</dt>
                            <dd class="col-sm-8">
                                @if($asset->location)
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $asset->location }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
                
                @if($asset->notes)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Notes:</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $asset->notes }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Asset History/Timeline -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Asset Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Asset Created</h6>
                            <p class="timeline-description text-muted mb-0">
                                Asset was added to the system
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $asset->created_at->format('M d, Y \a\t g:i A') }}
                            </small>
                        </div>
                    </div>
                    
                    @if($asset->updated_at != $asset->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Asset Updated</h6>
                                <p class="timeline-description text-muted mb-0">
                                    Asset information was last modified
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $asset->updated_at->format('M d, Y \a\t g:i A') }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Asset Image -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-image me-2"></i>Asset Image
                </h6>
            </div>
            <div class="card-body text-center">
                <div id="asset-image-container">
                    @if($asset->hasImage())
                        <img src="{{ $asset->getImageUrl() }}" 
                             alt="{{ $asset->getImageAlt() }}" 
                             class="img-fluid mb-3 rounded"
                             style="max-width: 100%; max-height: 300px; object-fit: cover;"
                             id="asset-image">
                        <div class="image-info mb-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Size: {{ $asset->getFormattedImageSize() }}
                            </small>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="viewFullImage()">
                                <i class="fas fa-expand me-1"></i>View Full Size
                            </button>
                            @can('edit_assets')
                            <button class="btn btn-outline-warning btn-sm" onclick="openImageUploadModal()">
                                <i class="fas fa-edit me-1"></i>Change Image
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteAssetImage()">
                                <i class="fas fa-trash me-1"></i>Delete Image
                            </button>
                            @endcan
                        </div>
                    @else
                        <div class="no-image-placeholder mb-3">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No image uploaded</p>
                        </div>
                        @can('edit_assets')
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="openImageUploadModal()">
                                <i class="fas fa-upload me-1"></i>Upload Image
                            </button>
                        </div>
                        @endcan
                    @endif
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-qrcode me-2"></i>QR Code
                </h6>
            </div>
            <div class="card-body text-center">
                <div id="qr-code-container">
                    <img src="{{ route('assets.qr-code', $asset) }}?size=200" 
                         alt="QR Code for {{ $asset->asset_tag }}" 
                         class="img-fluid mb-3"
                         style="max-width: 200px;">
                </div>
                <p class="text-muted small mb-3">
                    Scan this QR code to quickly access this asset
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('assets.qr-code.download', $asset) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-1"></i>Download QR Code
                    </a>
                    <button class="btn btn-outline-info btn-sm" onclick="copyQRCodeUrl()">
                        <i class="fas fa-copy me-1"></i>Copy QR Code URL
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('edit_assets')
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Asset
                    </a>
                    
                    @if($asset->assigned_to)
                        <button class="btn btn-outline-warning" onclick="unassignAsset({{ $asset->id }})">
                            <i class="fas fa-user-times me-2"></i>Unassign User
                        </button>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#reassignModal">
                            <i class="fas fa-exchange-alt me-2"></i>Reassign User
                        </button>
                    @else
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="fas fa-user-plus me-2"></i>Assign User
                        </button>
                    @endif
                    @endcan
                    
                    @can('view_assets')
                    <button class="btn btn-outline-info" onclick="printAssetLabel()">
                        <i class="fas fa-print me-2"></i>Print Label
                    </button>
                    @endcan
                    
                    <hr>
                    
                    @can('delete_assets')
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Asset
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        
        <!-- Asset Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Asset Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $asset->created_at->diffInDays(now()) }}</h4>
                            <small class="text-muted">Days Old</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">
                            @if($asset->cost)
                            ₱{{ number_format($asset->cost, 0) }}
                            @else
                                N/A
                            @endif
                        </h4>
                        <small class="text-muted">Value</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Information -->
        @if($asset->category || $asset->vendor || $asset->assignedUser)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Related Information</h6>
                </div>
                <div class="card-body">
                    @if($asset->category)
                        <div class="mb-2">
                            <strong>Category:</strong>
                            <a href="{{ route('assets.index', ['category' => $asset->category->id]) }}" class="text-decoration-none">
                                View all {{ $asset->category->name }} assets
                            </a>
                        </div>
                    @endif
                    
                    @if($asset->vendor)
                        <div class="mb-2">
                            <strong>Vendor:</strong>
                            <a href="{{ route('vendors.show', $asset->vendor) }}" class="text-decoration-none">
                                View {{ $asset->vendor->name }} details
                            </a>
                        </div>
                    @endif
                    
                    @if($asset->assignedUser)
                        <div class="mb-2">
                            <strong>Assigned User:</strong>
                            <a href="{{ route('users.show', $asset->assignedUser) }}" class="text-decoration-none">
                                View user profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

<!-- Assign User Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign User to Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm" method="POST" action="{{ route('assets.assign', $asset) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Select User</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 1)->orderBy('first_name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})</option>
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
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus me-2"></i>Assign User
                    </button>
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
                <h5 class="modal-title" id="reassignModalLabel">Reassign Asset to Another User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reassignForm" method="POST" action="{{ route('assets.reassign', $asset) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Currently assigned to: <strong>{{ $asset->assignedUser ? $asset->assignedUser->first_name . ' ' . $asset->assignedUser->last_name : 'Unassigned' }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user_search_reassign" class="form-label">Search Users</label>
                        <input type="text" class="form-control" id="user_search_reassign" placeholder="Search by name or department..." onkeyup="filterReassignUsers()">
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_assigned_to" class="form-label">Select New User</label>
                        <select class="form-select" id="new_assigned_to" name="new_assigned_to" required>
                            <option value="">Choose a user...</option>
                            @foreach(\App\Models\User::where('status', 1)->orderBy('first_name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reassign_date" class="form-label">Assignment Date</label>
                        <input type="date" class="form-control" id="reassign_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reassign_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="reassign_notes" name="notes" rows="3" placeholder="Reason for reassignment or additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt me-2"></i>Reassign Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<style>
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
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-marker.bg-primary {
    background-color: #0d6efd !important;
    box-shadow: 0 0 0 2px #0d6efd;
}

.timeline-marker.bg-success {
    background-color: #198754 !important;
    box-shadow: 0 0 0 2px #198754;
}

.timeline-marker.bg-warning {
    background-color: #ffc107 !important;
    box-shadow: 0 0 0 2px #ffc107;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #dee2e6;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-description {
    margin-bottom: 8px;
}
</style>

<script>
    function unassignAsset(assetId) {
        if (confirm('Are you sure you want to unassign this user from the asset?')) {
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
    
    function printAssetLabel() {
        // Create a simple print view for asset label
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Asset Label - {{ $asset->asset_tag }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .label { border: 2px solid #000; padding: 15px; width: 300px; text-align: center; }
                        .asset-tag { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                        .asset-name { font-size: 14px; margin-bottom: 5px; }
                        .asset-details { font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="label">
                        <div class="asset-tag">{{ $asset->asset_tag }}</div>
                        <div class="asset-name">{{ $asset->name }}</div>
                        <div class="asset-details">
                            @if($asset->model)Model: {{ $asset->model }}<br>@endif
                            @if($asset->serial_number)S/N: {{ $asset->serial_number }}<br>@endif
                            @if($asset->location)Location: {{ $asset->location }}@endif
                        </div>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
    
    function filterReassignUsers() {
        const searchTerm = document.getElementById('user_search_reassign').value.toLowerCase();
        const select = document.getElementById('new_assigned_to');
        const options = select.getElementsByTagName('option');
        
        for (let i = 1; i < options.length; i++) { // Skip first option ("Choose a user...")
            const option = options[i];
            const text = option.textContent.toLowerCase();
            
            if (text.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    }
    
    function copyQRCodeUrl() {
        const qrCodeUrl = '{{ route("assets.qr-code", $asset) }}';
        
        // Create a temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = qrCodeUrl;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            // Copy the text
            document.execCommand('copy');
            
            // Show success message
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
            btn.classList.remove('btn-outline-info');
            btn.classList.add('btn-success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-info');
            }, 2000);
            
        } catch (err) {
            console.error('Failed to copy: ', err);
            alert('Failed to copy QR code URL');
        }
        
        // Remove the temporary input
        document.body.removeChild(tempInput);
    }
    
    // Image upload functions
    function openImageUploadModal() {
        $('#imageUploadModal').modal('show');
    }
    
    function uploadAssetImage() {
        const formData = new FormData();
        const imageFile = document.getElementById('imageFile').files[0];
        const altText = document.getElementById('altText').value;
        
        if (!imageFile) {
            alert('Please select an image file.');
            return;
        }
        
        formData.append('image', imageFile);
        formData.append('alt_text', altText);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Show loading state
        const uploadBtn = document.getElementById('uploadImageBtn');
        const originalText = uploadBtn.innerHTML;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
        uploadBtn.disabled = true;
        
        fetch('{{ route("assets.upload-image", $asset) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the image display
                updateImageDisplay(data);
                $('#imageUploadModal').modal('hide');
                showAlert('success', 'Image uploaded successfully!');
            } else {
                showAlert('danger', data.message || 'Error uploading image');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error uploading image');
        })
        .finally(() => {
            uploadBtn.innerHTML = originalText;
            uploadBtn.disabled = false;
        });
    }
    
    function deleteAssetImage() {
        if (!confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
            return;
        }
        
        fetch('{{ route("assets.delete-image", $asset) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the image display to show no image
                updateImageDisplay(null);
                showAlert('success', 'Image deleted successfully!');
            } else {
                showAlert('danger', data.message || 'Error deleting image');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error deleting image');
        });
    }
    
    function updateImageDisplay(imageData) {
        const container = document.getElementById('asset-image-container');
        
        if (imageData) {
            container.innerHTML = `
                <img src="${imageData.image_url}" 
                     alt="${imageData.image_data.alt}" 
                     class="img-fluid mb-3 rounded"
                     style="max-width: 100%; max-height: 300px; object-fit: cover;"
                     id="asset-image">
                <div class="image-info mb-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Size: ${imageData.image_data.size}
                    </small>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="viewFullImage()">
                        <i class="fas fa-expand me-1"></i>View Full Size
                    </button>
                    @can('edit_assets')
                    <button class="btn btn-outline-warning btn-sm" onclick="openImageUploadModal()">
                        <i class="fas fa-edit me-1"></i>Change Image
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteAssetImage()">
                        <i class="fas fa-trash me-1"></i>Delete Image
                    </button>
                    @endcan
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="no-image-placeholder mb-3">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No image uploaded</p>
                </div>
                @can('edit_assets')
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="openImageUploadModal()">
                        <i class="fas fa-upload me-1"></i>Upload Image
                    </button>
                </div>
                @endcan
            `;
        }
    }
    
    function viewFullImage() {
        const image = document.getElementById('asset-image');
        if (image) {
            // Open image in new window
            const newWindow = window.open('', '_blank');
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Asset Image - {{ $asset->asset_tag }}</title>
                        <style>
                            body { margin: 0; padding: 20px; background: #f8f9fa; text-align: center; }
                            img { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                            .image-info { margin-top: 20px; color: #666; }
                        </style>
                    </head>
                    <body>
                        <img src="${image.src}" alt="${image.alt}">
                        <div class="image-info">
                            <h4>{{ $asset->name }}</h4>
                            <p>Asset Tag: {{ $asset->asset_tag }}</p>
                        </div>
                    </body>
                </html>
            `);
        }
    }
    
    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at top of content
        const content = document.querySelector('.container-fluid');
        content.insertBefore(alertDiv, content.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>

<!-- Image Upload Modal -->
<div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageUploadModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Asset Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="imageUploadForm">
                    <div class="mb-3">
                        <label for="imageFile" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="imageFile" accept="image/*" required>
                        <div class="form-text">
                            Supported formats: JPEG, PNG, GIF, WebP. Maximum size: 10MB.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="altText" class="form-label">Alt Text (Optional)</label>
                        <input type="text" class="form-control" id="altText" placeholder="Describe the image for accessibility">
                        <div class="form-text">
                            This text will be used for screen readers and when the image cannot be displayed.
                        </div>
                    </div>
                    <div class="image-preview" id="imagePreview" style="display: none;">
                        <h6>Preview:</h6>
                        <img id="previewImage" class="img-fluid rounded" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadImageBtn" onclick="uploadAssetImage()">
                    <i class="fas fa-upload me-1"></i>Upload Image
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('imageFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
@endsection