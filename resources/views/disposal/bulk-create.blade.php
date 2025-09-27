@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Bulk Create Disposal Records</h4>
                    <a href="{{ route('disposal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No available assets found for disposal. Please ensure you have assets that are available for disposal.
                        </div>
                        <a href="{{ route('disposal.index') }}" class="btn btn-secondary">Back to Disposal</a>
                    @else
                        <form action="{{ route('disposal.bulk-store') }}" method="POST" id="bulkCreateForm">
                            @csrf
                            <div class="mb-3">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Warning</h6>
                                    <p class="mb-2"><strong>Disposal is a permanent action!</strong> Once assets are disposed, their status will be changed to "Disposed" and cannot be easily undone.</p>
                                    <ul class="mb-0">
                                        <li>Found <strong>{{ $assets->count() }}</strong> available assets for disposal</li>
                                        <li>Check the assets you want to dispose of</li>
                                        <li>Fill in the required details for each selected asset</li>
                                        <li>Use the "Select All" checkbox to quickly select all assets</li>
                                        <li>All fields marked with <span class="text-danger">*</span> are required for selected assets</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll" class="form-check-input" title="Select All">
                                                <label for="selectAll" class="form-check-label ms-1">All</label>
                                            </th>
                                            <th width="20%">Asset Details</th>
                                            <th width="12%">Disposal Date <span class="text-danger">*</span></th>
                                            <th width="15%">Disposal Type <span class="text-danger">*</span></th>
                                            <th width="10%">Disposal Value</th>
                                            <th width="25%">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $index => $asset)
                                        <tr class="disposal-row">
                                            <td>
                                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->name }}</strong><br>
                                                <small class="text-muted">Tag: {{ $asset->asset_tag }}</small><br>
                                                <small class="text-muted">Status: {{ $asset->status }}</small><br>
                                                <small class="text-muted">Cost: ₱{{ number_format($asset->cost ?? 0, 2) }}</small>
                                                <input type="hidden" name="disposal[{{ $index }}][asset_id]" value="{{ $asset->id }}" class="disposal-input" disabled>
                                            </td>
                                            <td>
                                                <input type="date" name="disposal[{{ $index }}][disposal_date]" class="form-control disposal-input" disabled value="{{ date('Y-m-d') }}">
                                                @error("disposal.{$index}.disposal_date")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="disposal[{{ $index }}][disposal_type]" class="form-select disposal-input" disabled>
                                                    <option value="">Select Type</option>
                                                    <option value="Sold">Sold</option>
                                                    <option value="Donated">Donated</option>
                                                    <option value="Recycled">Recycled</option>
                                                    <option value="Destroyed">Destroyed</option>
                                                    <option value="Lost">Lost</option>
                                                    <option value="Stolen">Stolen</option>
                                                    <option value="Trade-in">Trade-in</option>
                                                    <option value="Return to Vendor">Return to Vendor</option>
                                                    <option value="Upgrade Replacement">Upgrade Replacement</option>
                                                    <option value="Damaged Beyond Repair">Damaged Beyond Repair</option>
                                                    <option value="End of Life">End of Life</option>
                                                    <option value="Security Risk">Security Risk</option>
                                                    <option value="Theft/Loss">Theft/Loss</option>
                                                    <option value="Obsolete Technology">Obsolete Technology</option>
                                                    <option value="Cost of Repair Exceeds Value">Cost of Repair Exceeds Value</option>
                                                </select>
                                                @error("disposal.{$index}.disposal_type")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" name="disposal[{{ $index }}][disposal_value]" class="form-control disposal-input" disabled step="0.01" min="0" max="999999.99" placeholder="0.00">
                                                </div>
                                                @error("disposal.{$index}.disposal_value")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <textarea name="disposal[{{ $index }}][remarks]" class="form-control disposal-input" disabled rows="2" placeholder="Additional notes about the disposal..."></textarea>
                                                @error("disposal.{$index}.remarks")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <span id="selectedCount" class="text-muted">0 disposal records selected</span>
                                </div>
                                <div>
                                    <a href="{{ route('disposal.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-danger" id="submitBtn" disabled>
                                        <i class="fas fa-trash-alt"></i> Create Selected Disposal Records
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    const submitBtn = document.getElementById('submitBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Handle select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            toggleRowInputs(checkbox);
        });
        updateSelectedCount();
        updateSubmitButton();
    });

    // Handle individual checkbox changes
    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleRowInputs(this);
            updateSelectAllState();
            updateSelectedCount();
            updateSubmitButton();
        });
    });

    function toggleRowInputs(checkbox) {
        const row = checkbox.closest('.disposal-row');
        const inputs = row.querySelectorAll('.disposal-input');
        
        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                    input.value = '';
                }
            }
        });
    }

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        const totalCount = assetCheckboxes.length;
        
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        selectAllCheckbox.checked = checkedCount === totalCount;
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        selectedCountSpan.textContent = `${checkedCount} disposal record${checkedCount !== 1 ? 's' : ''} selected`;
    }

    function updateSubmitButton() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        submitBtn.disabled = checkedCount === 0;
    }

    // Form submission handling
    document.getElementById('bulkCreateForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one asset to dispose of.');
            return;
        }

        // Validate that all selected disposal records have required fields filled
        let hasErrors = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.disposal-row');
            const disposalDate = row.querySelector('input[name*="[disposal_date]"]').value;
            const disposalType = row.querySelector('select[name*="[disposal_type]"]').value;

            if (!disposalDate || !disposalType) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields for selected disposal records.');
            return;
        }

        // Final confirmation
        if (!confirm(`Are you sure you want to dispose of ${checkedBoxes.length} asset(s)? This action will change their status to "Disposed" and cannot be easily undone.`)) {
            e.preventDefault();
            return false;
        }
    });

    // Auto-clear disposal value for certain types
    document.addEventListener('change', function(e) {
        if (e.target.name && e.target.name.includes('[disposal_type]')) {
            const disposalValue = e.target.closest('tr').querySelector('input[name*="[disposal_value]"]');
            const disposalType = e.target.value;
            
            if (['Donated', 'Recycled', 'Destroyed', 'Lost', 'Stolen'].includes(disposalType)) {
                disposalValue.value = '';
                disposalValue.placeholder = 'N/A for ' + disposalType.toLowerCase();
            } else {
                disposalValue.placeholder = '0.00';
            }
        }
    });
});
</script>
@endsection
