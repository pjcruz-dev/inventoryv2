@extends('layouts.app')

@section('title', 'Create Asset Category')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Create Asset Category
                    </h3>
                    <a href="{{ route('asset-categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Categories
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-categories.store') }}" method="POST">
                        @csrf
                        
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
                                           value="{{ old('name') }}" 
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
                                              maxlength="1000">{{ old('description') }}</textarea>
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
                                    <a href="{{ route('asset-categories.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Category
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Help Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Category Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-2"></i>Best Practices</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Use clear, descriptive names</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Keep names concise but meaningful</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Use consistent naming conventions</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Add helpful descriptions</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb text-warning me-2"></i>Examples</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Computers & Laptops</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Network Equipment</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Office Furniture</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i>Mobile Devices</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
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
</script>
@endpush