@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Maintenance Record #{{ $maintenance->id }}
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('maintenance.update', $maintenance) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select class="form-select searchable-select @error('asset_id') is-invalid @enderror" 
                                            id="asset_id" name="asset_id" required>
                                        <option value="">Select Asset</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" 
                                                {{ (old('asset_id', $maintenance->asset_id) == $asset->id) ? 'selected' : '' }}>
                                                {{ $asset->name }} ({{ $asset->asset_tag }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor_id" class="form-label">Vendor</label>
                                    <select class="form-select searchable-select @error('vendor_id') is-invalid @enderror" 
                                            id="vendor_id" name="vendor_id">
                                        <option value="">Select Vendor (Optional)</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" 
                                                {{ (old('vendor_id', $maintenance->vendor_id) == $vendor->id) ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="issue_reported" class="form-label">Issue Reported <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('issue_reported') is-invalid @enderror" 
                                      id="issue_reported" name="issue_reported" rows="3" 
                                      placeholder="Describe the issue that needs maintenance..." required>{{ old('issue_reported', $maintenance->issue_reported) }}</textarea>
                            @error('issue_reported')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="repair_action" class="form-label">Repair Action</label>
                            <textarea class="form-control @error('repair_action') is-invalid @enderror" 
                                      id="repair_action" name="repair_action" rows="3" 
                                      placeholder="Describe the repair action taken or planned...">{{ old('repair_action', $maintenance->repair_action) }}</textarea>
                            @error('repair_action')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                               id="cost" name="cost" step="0.01" min="0" max="999999.99"
                                               value="{{ old('cost', $maintenance->cost) }}" placeholder="0.00">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $maintenance->start_date ? $maintenance->start_date->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $maintenance->end_date ? $maintenance->end_date->format('Y-m-d\TH:i') : '') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Scheduled" {{ old('status', $maintenance->status) == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="In Progress" {{ old('status', $maintenance->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ old('status', $maintenance->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="On Hold" {{ old('status', $maintenance->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ old('status', $maintenance->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" name="remarks" rows="3" 
                                      placeholder="Additional notes or remarks...">{{ old('remarks', $maintenance->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                                <a href="{{ route('maintenance.show', $maintenance) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Maintenance Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validate end date is after start date
    document.getElementById('end_date').addEventListener('change', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = this.value;
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alert('End date must be after start date.');
            this.value = '';
        }
    });
    
    // Validate start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alert('Start date cannot be after end date. Please adjust the end date.');
            document.getElementById('end_date').value = '';
        }
    });
    
    // Auto-populate vendor field when asset is selected
    document.getElementById('asset_id').addEventListener('change', function() {
        const assetId = this.value;
        const vendorSelect = document.getElementById('vendor_id');
        
        if (assetId) {
            // Fetch asset vendor information
            fetch(`/assets/${assetId}/vendor`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.vendor) {
                        // Set the vendor dropdown to the asset's vendor
                        vendorSelect.value = data.vendor.id;
                        
                        // Trigger change event to update any dependent fields
                        vendorSelect.dispatchEvent(new Event('change'));
                        
                        // Optional: Show a brief notification
                        console.log(`Vendor auto-populated: ${data.vendor.name}`);
                    } else {
                        // Reset vendor selection if asset has no vendor
                        vendorSelect.value = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching asset vendor:', error);
                    // Don't reset vendor selection on error to avoid disrupting user
                });
        } else {
            // Reset vendor selection when no asset is selected
            vendorSelect.value = '';
        }
    });
</script>
@endpush