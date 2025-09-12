@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Add New Asset Disposal
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('disposal.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select class="form-select @error('asset_id') is-invalid @enderror" 
                                            id="asset_id" name="asset_id" required>
                                        <option value="">Select Asset</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}
                                                    data-purchase-cost="{{ $asset->purchase_cost }}">
                                                {{ $asset->name }} ({{ $asset->asset_tag }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Only assets available for disposal are shown.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="disposal_date" class="form-label">Disposal Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('disposal_date') is-invalid @enderror" 
                                           id="disposal_date" name="disposal_date" 
                                           value="{{ old('disposal_date', now()->format('Y-m-d')) }}" required>
                                    @error('disposal_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="disposal_type" class="form-label">Disposal Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('disposal_type') is-invalid @enderror" 
                                            id="disposal_type" name="disposal_type" required>
                                        <option value="">Select Disposal Type</option>
                                        <option value="Sold" {{ old('disposal_type') == 'Sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="Donated" {{ old('disposal_type') == 'Donated' ? 'selected' : '' }}>Donated</option>
                                        <option value="Recycled" {{ old('disposal_type') == 'Recycled' ? 'selected' : '' }}>Recycled</option>
                                        <option value="Destroyed" {{ old('disposal_type') == 'Destroyed' ? 'selected' : '' }}>Destroyed</option>
                                        <option value="Lost" {{ old('disposal_type') == 'Lost' ? 'selected' : '' }}>Lost</option>
                                        <option value="Stolen" {{ old('disposal_type') == 'Stolen' ? 'selected' : '' }}>Stolen</option>
                                    </select>
                                    @error('disposal_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="disposal_value" class="form-label">Disposal Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('disposal_value') is-invalid @enderror" 
                                               id="disposal_value" name="disposal_value" step="0.01" min="0" max="999999.99"
                                               value="{{ old('disposal_value') }}" placeholder="0.00">
                                        @error('disposal_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Amount received for the asset (if applicable).</small>
                                </div>
                            </div>
                        </div>
                        

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" name="remarks" rows="4" 
                                      placeholder="Additional notes about the disposal (reason, condition, recipient details, etc.)">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Asset Information Display -->
                        <div id="asset-info" class="card bg-light mb-3" style="display: none;">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Asset Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Asset Name:</strong> <span id="asset-name">-</span></p>
                                        <p class="mb-1"><strong>Asset Tag:</strong> <span id="asset-tag">-</span></p>
                                        <p class="mb-1"><strong>Model:</strong> <span id="asset-model">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Purchase Cost:</strong> <span id="asset-cost">-</span></p>
                                        <p class="mb-1"><strong>Current Status:</strong> <span id="asset-status">-</span></p>
                                        <p class="mb-1"><strong>Location:</strong> <span id="asset-location">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('disposal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i>Create Disposal Record
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
    // Asset selection handler
    document.getElementById('asset_id').addEventListener('change', function() {
        const assetId = this.value;
        const assetInfo = document.getElementById('asset-info');
        
        if (assetId) {
            // Get asset information via AJAX
            fetch(`/api/assets/${assetId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('asset-name').textContent = data.name || '-';
                    document.getElementById('asset-tag').textContent = data.asset_tag || '-';
                    document.getElementById('asset-model').textContent = data.model?.name || '-';
                    document.getElementById('asset-cost').textContent = data.purchase_cost ? `$${parseFloat(data.purchase_cost).toFixed(2)}` : '-';
                    document.getElementById('asset-status').textContent = data.status_label?.name || data.status || '-';
                    document.getElementById('asset-location').textContent = data.location?.name || '-';
                    
                    assetInfo.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching asset data:', error);
                    assetInfo.style.display = 'none';
                });
        } else {
            assetInfo.style.display = 'none';
        }
    });
    
    // Disposal type change handler
    document.getElementById('disposal_type').addEventListener('change', function() {
        const disposalValue = document.getElementById('disposal_value');
        const disposalType = this.value;
        
        // Auto-clear disposal value for certain types
        if (['Donated', 'Recycled', 'Destroyed', 'Lost', 'Stolen'].includes(disposalType)) {
            disposalValue.value = '';
            disposalValue.placeholder = 'N/A for ' + disposalType.toLowerCase();
        } else {
            disposalValue.placeholder = '0.00';
        }
    });
    
    // Date validation
    document.getElementById('disposal_date').addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate > today) {
            if (!confirm('The disposal date is in the future. Are you sure this is correct?')) {
                this.value = new Date().toISOString().split('T')[0];
            }
        }
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const assetId = document.getElementById('asset_id').value;
        const disposalType = document.getElementById('disposal_type').value;
        
        if (!assetId || !disposalType) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
        
        // Confirm disposal action
        if (!confirm('Are you sure you want to dispose of this asset? This action will change the asset status and cannot be easily undone.')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush

@push('styles')
<style>
.form-text {
    font-size: 0.875em;
}

#asset-info {
    border-left: 4px solid #17a2b8;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}
</style>
@endpush