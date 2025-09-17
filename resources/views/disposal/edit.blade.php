@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Disposal Record #{{ $disposal->id }}
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('disposal.update', $disposal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select class="form-select @error('asset_id') is-invalid @enderror" 
                                            id="asset_id" name="asset_id" required>
                                        <option value="">Select Asset</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" 
                                                {{ (old('asset_id', $disposal->asset_id) == $asset->id) ? 'selected' : '' }}
                                                data-purchase-cost="{{ $asset->purchase_cost }}">
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
                                    <label for="disposal_date" class="form-label">Disposal Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('disposal_date') is-invalid @enderror" 
                                           id="disposal_date" name="disposal_date" 
                                           value="{{ old('disposal_date', $disposal->disposal_date->format('Y-m-d')) }}" required>
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
                        <option value="Sold" {{ old('disposal_type', $disposal->disposal_type) == 'Sold' ? 'selected' : '' }}>Sold</option>
                        <option value="Donated" {{ old('disposal_type', $disposal->disposal_type) == 'Donated' ? 'selected' : '' }}>Donated</option>
                        <option value="Recycled" {{ old('disposal_type', $disposal->disposal_type) == 'Recycled' ? 'selected' : '' }}>Recycled</option>
                        <option value="Destroyed" {{ old('disposal_type', $disposal->disposal_type) == 'Destroyed' ? 'selected' : '' }}>Destroyed</option>
                        <option value="Lost" {{ old('disposal_type', $disposal->disposal_type) == 'Lost' ? 'selected' : '' }}>Lost</option>
                        <option value="Stolen" {{ old('disposal_type', $disposal->disposal_type) == 'Stolen' ? 'selected' : '' }}>Stolen</option>
                        <option value="Trade-in" {{ old('disposal_type', $disposal->disposal_type) == 'Trade-in' ? 'selected' : '' }}>Trade-in</option>
                        <option value="Return to Vendor" {{ old('disposal_type', $disposal->disposal_type) == 'Return to Vendor' ? 'selected' : '' }}>Return to Vendor</option>
                        <option value="Upgrade Replacement" {{ old('disposal_type', $disposal->disposal_type) == 'Upgrade Replacement' ? 'selected' : '' }}>Upgrade Replacement</option>
                        <option value="Damaged Beyond Repair" {{ old('disposal_type', $disposal->disposal_type) == 'Damaged Beyond Repair' ? 'selected' : '' }}>Damaged Beyond Repair</option>
                        <option value="End of Life" {{ old('disposal_type', $disposal->disposal_type) == 'End of Life' ? 'selected' : '' }}>End of Life</option>
                        <option value="Security Risk" {{ old('disposal_type', $disposal->disposal_type) == 'Security Risk' ? 'selected' : '' }}>Security Risk</option>
                        <option value="Theft/Loss" {{ old('disposal_type', $disposal->disposal_type) == 'Theft/Loss' ? 'selected' : '' }}>Theft/Loss</option>
                        <option value="Obsolete Technology" {{ old('disposal_type', $disposal->disposal_type) == 'Obsolete Technology' ? 'selected' : '' }}>Obsolete Technology</option>
                        <option value="Cost of Repair Exceeds Value" {{ old('disposal_type', $disposal->disposal_type) == 'Cost of Repair Exceeds Value' ? 'selected' : '' }}>Cost of Repair Exceeds Value</option>
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
                                               value="{{ old('disposal_value', $disposal->disposal_value) }}" placeholder="0.00">
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
                                      placeholder="Additional notes about the disposal (reason, condition, recipient details, etc.)">{{ old('remarks', $disposal->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Current Asset Information Display -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Current Asset Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Asset Name:</strong> {{ $disposal->asset->name }}</p>
                                        <p class="mb-1"><strong>Asset Tag:</strong> {{ $disposal->asset->asset_tag }}</p>
                                        <p class="mb-1"><strong>Model:</strong> {{ $disposal->asset->model->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Purchase Cost:</strong> 
                                            @if($disposal->asset->purchase_cost)
                                                ${{ number_format($disposal->asset->purchase_cost, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                        <p class="mb-1"><strong>Current Status:</strong> 
                                            <span class="badge badge-{{ $disposal->asset->status_label->color ?? 'secondary' }}">
                                                {{ $disposal->asset->status_label->name ?? $disposal->asset->status }}
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Location:</strong> {{ $disposal->asset->location->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Disposal History -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Disposal Record History</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Created:</strong> {{ $disposal->created_at->format('M d, Y H:i') }}</p>
                                        <p class="mb-1"><strong>Created By:</strong> {{ $disposal->createdBy->name ?? 'System' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Last Updated:</strong> {{ $disposal->updated_at->format('M d, Y H:i') }}</p>
                                        <p class="mb-1"><strong>Updated By:</strong> {{ $disposal->updatedBy->name ?? 'System' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('disposal.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                                @can('view_disposal')
                                <a href="{{ route('disposal.show', $disposal) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                @endcan
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Disposal Record
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
    // Disposal type change handler
    document.getElementById('disposal_type').addEventListener('change', function() {
        const disposalValue = document.getElementById('disposal_value');
        const disposalType = this.value;
        
        // Auto-clear disposal value for certain types
        if (['Donated', 'Recycled', 'Destroyed', 'Lost', 'Stolen'].includes(disposalType)) {
            if (disposalValue.value && !confirm('Changing to ' + disposalType + ' will typically have no monetary value. Continue?')) {
                return;
            }
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
                this.value = '{{ $disposal->disposal_date->format('Y-m-d') }}';
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
        
        // Confirm update action
        if (!confirm('Are you sure you want to update this disposal record?')) {
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

.card.bg-light {
    border-left: 4px solid #17a2b8;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-info {
    background-color: #17a2b8;
}
</style>
@endpush