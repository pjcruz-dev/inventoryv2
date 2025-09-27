@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Bulk Create Maintenance Records</h4>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No available assets found for maintenance. Please ensure you have assets that are not already under maintenance.
                        </div>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Back to Maintenance</a>
                    @else
                        <form action="{{ route('maintenance.bulk-store') }}" method="POST" id="bulkCreateForm">
                            @csrf
                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Bulk Create Instructions</h6>
                                    <ul class="mb-0">
                                        <li>Found <strong>{{ $assets->count() }}</strong> available assets for maintenance</li>
                                        <li>Check the assets you want to create maintenance records for</li>
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
                                            <th width="15%">Issue Reported <span class="text-danger">*</span></th>
                                            <th width="15%">Vendor</th>
                                            <th width="10%">Cost</th>
                                            <th width="10%">Start Date <span class="text-danger">*</span></th>
                                            <th width="10%">End Date</th>
                                            <th width="10%">Status <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $index => $asset)
                                        <tr class="maintenance-row">
                                            <td>
                                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->name }}</strong><br>
                                                <small class="text-muted">Tag: {{ $asset->asset_tag }}</small><br>
                                                <small class="text-muted">Status: {{ $asset->status }}</small>
                                                <input type="hidden" name="maintenance[{{ $index }}][asset_id]" value="{{ $asset->id }}" class="maintenance-input" disabled>
                                            </td>
                                            <td>
                                                <textarea name="maintenance[{{ $index }}][issue_reported]" class="form-control maintenance-input" disabled rows="2" placeholder="Describe the issue..."></textarea>
                                                @error("maintenance.{$index}.issue_reported")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="maintenance[{{ $index }}][vendor_id]" class="form-select maintenance-input" disabled>
                                                    <option value="">Select Vendor</option>
                                                    @foreach($vendors as $vendor)
                                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error("maintenance.{$index}.vendor_id")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" name="maintenance[{{ $index }}][cost]" class="form-control maintenance-input" disabled step="0.01" min="0" max="999999.99" placeholder="0.00">
                                                @error("maintenance.{$index}.cost")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="datetime-local" name="maintenance[{{ $index }}][start_date]" class="form-control maintenance-input" disabled value="{{ now()->format('Y-m-d\TH:i') }}">
                                                @error("maintenance.{$index}.start_date")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="datetime-local" name="maintenance[{{ $index }}][end_date]" class="form-control maintenance-input" disabled>
                                                @error("maintenance.{$index}.end_date")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="maintenance[{{ $index }}][status]" class="form-select maintenance-input" disabled>
                                                    <option value="">Select Status</option>
                                                    <option value="Scheduled">Scheduled</option>
                                                    <option value="In Progress">In Progress</option>
                                                    <option value="Completed">Completed</option>
                                                    <option value="On Hold">On Hold</option>
                                                    <option value="Cancelled">Cancelled</option>
                                                </select>
                                                @error("maintenance.{$index}.status")
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
                                    <span id="selectedCount" class="text-muted">0 maintenance records selected</span>
                                </div>
                                <div>
                                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="fas fa-plus"></i> Create Selected Maintenance Records
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
        const row = checkbox.closest('.maintenance-row');
        const inputs = row.querySelectorAll('.maintenance-input');
        
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
        selectedCountSpan.textContent = `${checkedCount} maintenance record${checkedCount !== 1 ? 's' : ''} selected`;
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
            alert('Please select at least one asset to create maintenance records for.');
            return;
        }

        // Validate that all selected maintenance records have required fields filled
        let hasErrors = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.maintenance-row');
            const issueReported = row.querySelector('textarea[name*="[issue_reported]"]').value;
            const startDate = row.querySelector('input[name*="[start_date]"]').value;
            const status = row.querySelector('select[name*="[status]"]').value;

            if (!issueReported || !startDate || !status) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields for selected maintenance records.');
            return;
        }
    });
});
</script>
@endsection
