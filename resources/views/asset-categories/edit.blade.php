@extends('layouts.app')

@section('title', 'Edit Asset Category')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Asset Category
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('asset-categories.show', $assetCategory) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="{{ route('asset-categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Categories
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-categories.update', $assetCategory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Category Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $assetCategory->name) }}" 
                                           placeholder="Enter category name"
                                           maxlength="100"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        Maximum 100 characters. This name must be unique.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        Description
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Enter category description (optional)"
                                              maxlength="1000">{{ old('description', $assetCategory->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        Maximum 1000 characters. Provide a brief description of this category.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('asset-categories.show', $assetCategory) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Category
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Category Info Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Category Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-bar text-primary me-2"></i>Statistics</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                    <strong>Assets:</strong> {{ $assetCategory->assets()->count() }}
                                </li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                    <strong>Created:</strong> {{ $assetCategory->created_at->format('M d, Y') }}
                                </li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                    <strong>Last Updated:</strong> {{ $assetCategory->updated_at->format('M d, Y') }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Important Notes</h6>
                            <ul class="list-unstyled">
                                @if($assetCategory->assets()->count() > 0)
                                    <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                        This category has {{ $assetCategory->assets()->count() }} asset(s) assigned
                                    </li>
                                    <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                        Cannot be deleted while assets are assigned
                                    </li>
                                @else
                                    <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                        No assets assigned to this category
                                    </li>
                                    <li><i class="fas fa-chevron-right text-muted me-2"></i>
                                        Category can be safely deleted if needed
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Danger Zone -->
            @if($assetCategory->assets()->count() == 0)
                <div class="card mt-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Once you delete this category, there is no going back. Please be certain.
                        </p>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i>Delete Category
                        </button>
                    </div>
                </div>
                
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete the category <strong>{{ $assetCategory->name }}</strong>?</p>
                                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                                <p class="text-muted">Type the category name to confirm:</p>
                                <input type="text" id="confirmName" class="form-control" placeholder="{{ $assetCategory->name }}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('asset-categories.destroy', $assetCategory) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" id="deleteButton" class="btn btn-danger" disabled>
                                        Delete Category
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Character counter for name field
    document.getElementById('name').addEventListener('input', function() {
        const maxLength = 100;
        const currentLength = this.value.length;
        const remaining = maxLength - currentLength;
        
        // Update or create character counter
        let counter = document.getElementById('name-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.id = 'name-counter';
            counter.className = 'form-text';
            this.parentNode.appendChild(counter);
        }
        
        counter.textContent = `${currentLength}/${maxLength} characters`;
        counter.className = remaining < 10 ? 'form-text text-warning' : 'form-text text-muted';
    });
    
    // Character counter for description field
    document.getElementById('description').addEventListener('input', function() {
        const maxLength = 1000;
        const currentLength = this.value.length;
        const remaining = maxLength - currentLength;
        
        // Update or create character counter
        let counter = document.getElementById('description-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.id = 'description-counter';
            counter.className = 'form-text';
            this.parentNode.appendChild(counter);
        }
        
        counter.textContent = `${currentLength}/${maxLength} characters`;
        counter.className = remaining < 50 ? 'form-text text-warning' : 'form-text text-muted';
    });
    
    // Delete confirmation
    @if($assetCategory->assets()->count() == 0)
        document.getElementById('confirmName').addEventListener('input', function() {
            const deleteButton = document.getElementById('deleteButton');
            const expectedName = '{{ $assetCategory->name }}';
            
            if (this.value === expectedName) {
                deleteButton.disabled = false;
                deleteButton.classList.remove('btn-danger');
                deleteButton.classList.add('btn-outline-danger');
            } else {
                deleteButton.disabled = true;
                deleteButton.classList.remove('btn-outline-danger');
                deleteButton.classList.add('btn-danger');
            }
        });
    @endif
</script>
@endpush