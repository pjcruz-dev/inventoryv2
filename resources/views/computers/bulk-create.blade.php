@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Bulk Create Computers</h4>
                    <a href="{{ route('computers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No available assets found for computer creation. Please ensure you have assets in the "Computer Hardware" category that are not already assigned to computers.
                        </div>
                        <a href="{{ route('computers.index') }}" class="btn btn-secondary">Back to Computers</a>
                    @else
                        <form action="{{ route('computers.bulk-store') }}" method="POST" id="bulkCreateForm">
                            @csrf
                            <div class="mb-3">
                                <p class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Found {{ $assets->count() }} available assets for computer creation. Select the assets you want to create computers for and fill in the required details.
                                </p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th width="20%">Asset</th>
                                            <th width="15%">Processor</th>
                                            <th width="10%">Memory</th>
                                            <th width="10%">Storage</th>
                                            <th width="20%">Operating System</th>
                                            <th width="10%">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $index => $asset)
                                        <tr class="computer-row">
                                            <td>
                                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->name }}</strong><br>
                                                <small class="text-muted">{{ $asset->brand }} {{ $asset->model }}</small><br>
                                                <small class="text-muted">Serial: {{ $asset->serial_number }}</small>
                                                <input type="hidden" name="computers[{{ $index }}][asset_id]" value="{{ $asset->id }}" class="computer-input" disabled>
                                            </td>
                                            <td>
                                                <input type="text" name="computers[{{ $index }}][processor]" class="form-control computer-input" placeholder="e.g., Intel i5, Ryzen 5" disabled>
                                                @error("computers.{$index}.processor")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="computers[{{ $index }}][memory]" class="form-control computer-input" placeholder="e.g., 16GB" disabled>
                                                @error("computers.{$index}.memory")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="computers[{{ $index }}][storage]" class="form-control computer-input" placeholder="e.g., 512GB" disabled>
                                                @error("computers.{$index}.storage")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" name="computers[{{ $index }}][operating_system]" class="form-control computer-input" placeholder="e.g., Windows 11 Pro" disabled>
                                                @error("computers.{$index}.operating_system")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="computers[{{ $index }}][computer_type]" class="form-select computer-input" disabled>
                                                    <option value="">Select Type</option>
                                                    <option value="Desktop">Desktop</option>
                                                    <option value="Laptop">Laptop</option>
                                                    <option value="Server">Server</option>
                                                    <option value="Workstation">Workstation</option>
                                                </select>
                                                @error("computers.{$index}.computer_type")
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
                                    <span id="selectedCount" class="text-muted">0 computers selected</span>
                                </div>
                                <div>
                                    <a href="{{ route('computers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="fas fa-plus"></i> Create Selected Computers
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
        const row = checkbox.closest('.computer-row');
        const inputs = row.querySelectorAll('.computer-input');
        
        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                if (input.type === 'text') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
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
        selectedCountSpan.textContent = `${checkedCount} computer${checkedCount !== 1 ? 's' : ''} selected`;
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
            alert('Please select at least one asset to create computers for.');
            return;
        }

        // Validate that all selected computers have required fields filled
        let hasErrors = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.computer-row');
            const processor = row.querySelector('input[name*="[processor]"]').value.trim();
            const memory = row.querySelector('input[name*="[memory]"]').value.trim();
            const storage = row.querySelector('input[name*="[storage]"]').value.trim();
            const os = row.querySelector('input[name*="[operating_system]"]').value.trim();
            const type = row.querySelector('select[name*="[computer_type]"]').value;

            if (!processor || !memory || !storage || !os || !type) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields for selected computers.');
            return;
        }
    });
});
</script>
@endsection


