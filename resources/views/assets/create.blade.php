@extends('layouts.app')

@section('title', 'Create Asset')
@section('page-title', 'Create New Asset')

@section('page-actions')
    <a href="{{ route('assets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Assets
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Asset Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="asset_tag" class="form-label">Asset Tag <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" 
                                       id="asset_tag" name="asset_tag" value="{{ old('asset_tag') }}" required>
                                @error('asset_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Current Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="deployed" {{ old('status') == 'deployed' ? 'selected' : '' }}>Deployed</option>
                                    <option value="problematic" {{ old('status') == 'problematic' ? 'selected' : '' }}>Problematic</option>
                                    <option value="pending_confirm" {{ old('status') == 'pending_confirm' ? 'selected' : '' }}>Pending Confirm</option>
                                    <option value="returned" {{ old('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                    <option value="new_arrived" {{ old('status') == 'new_arrived' ? 'selected' : '' }}>New Arrived</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model') }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                                       id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vendor_id" class="form-label">Vendor</label>
                                <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                       id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost" class="form-label">Purchase Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" 
                                           id="cost" name="cost" value="{{ old('cost') }}">
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location') }}" 
                               placeholder="e.g., Office 101, Warehouse A">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Additional information about this asset...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Asset tags should be unique identifiers</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Use descriptive names for easy identification</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Keep serial numbers for warranty tracking</small>
                    </li>
                    <li>
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Regular status updates help with maintenance</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate asset tag based on category and current date
    document.getElementById('category_id').addEventListener('change', function() {
        const assetTagField = document.getElementById('asset_tag');
        if (!assetTagField.value && this.value) {
            const categoryText = this.options[this.selectedIndex].text.substring(0, 3).toUpperCase();
            const date = new Date();
            const timestamp = date.getFullYear().toString().substr(-2) + 
                            String(date.getMonth() + 1).padStart(2, '0') + 
                            String(date.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            assetTagField.value = categoryText + '-' + timestamp + '-' + random;
        }
    });
</script>
@endsection