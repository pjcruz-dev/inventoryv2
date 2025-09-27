@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Bulk Create Peripherals</h4>
                    <a href="{{ route('peripherals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if (session('warning'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No available assets found for peripheral creation. Please ensure you have assets in the "Peripherals" category that are not already assigned to peripherals.
                        </div>
                        <a href="{{ route('peripherals.index') }}" class="btn btn-secondary">Back to Peripherals</a>
                    @else
                        <form action="{{ route('peripherals.bulk-store') }}" method="POST" id="bulkCreateForm">
                            @csrf
                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Bulk Create Instructions</h6>
                                    <ul class="mb-0">
                                        <li>Found <strong>{{ $assets->count() }}</strong> available peripheral assets</li>
                                        <li>Check the assets you want to create peripheral records for</li>
                                        <li>Fill in the <strong>Type</strong> and <strong>Interface</strong> for each selected asset</li>
                                        <li>Use the "Select All" checkbox to quickly select all assets</li>
                                        <li>All fields are required for selected assets</li>
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
                                            <th width="25%">Asset Details</th>
                                            <th width="20%">Peripheral Type <span class="text-danger">*</span></th>
                                            <th width="20%">Interface <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $index => $asset)
                                        <tr class="peripheral-row">
                                            <td>
                                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->name }}</strong><br>
                                                <small class="text-muted">Tag: {{ $asset->asset_tag }}</small><br>
                                                <small class="text-muted">Serial: {{ $asset->serial_number }}</small>
                                                <input type="hidden" name="peripherals[{{ $index }}][asset_id]" value="{{ $asset->id }}" class="peripheral-input" disabled>
                                            </td>
                                            <td>
                                                <select name="peripherals[{{ $index }}][type]" class="form-select peripheral-input" disabled>
                                                    <option value="">Select Type</option>
                                                    <option value="Mouse">Mouse</option>
                                                    <option value="Keyboard">Keyboard</option>
                                                    <option value="Headset">Headset</option>
                                                    <option value="Speaker">Speaker</option>
                                                    <option value="Webcam">Webcam</option>
                                                    <option value="Microphone">Microphone</option>
                                                    <option value="USB Hub">USB Hub</option>
                                                    <option value="External Drive">External Drive</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                @error("peripherals.{$index}.type")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="peripherals[{{ $index }}][interface]" class="form-select peripheral-input" disabled>
                                                    <option value="">Select Interface</option>
                                                    <option value="USB">USB</option>
                                                    <option value="Bluetooth">Bluetooth</option>
                                                    <option value="Wireless">Wireless</option>
                                                    <option value="Wired">Wired</option>
                                                    <option value="USB-C">USB-C</option>
                                                    <option value="Lightning">Lightning</option>
                                                </select>
                                                @error("peripherals.{$index}.interface")
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
                                    <span id="selectedCount" class="text-muted">0 peripherals selected</span>
                                </div>
                                <div>
                                    <a href="{{ route('peripherals.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="fas fa-plus"></i> Create Selected Peripherals
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
        const row = checkbox.closest('.peripheral-row');
        const inputs = row.querySelectorAll('.peripheral-input');
        
        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                if (input.tagName === 'SELECT') {
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
        selectedCountSpan.textContent = `${checkedCount} peripheral${checkedCount !== 1 ? 's' : ''} selected`;
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
            alert('Please select at least one asset to create peripherals for.');
            return;
        }

        // Validate that all selected peripherals have required fields filled
        let hasErrors = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.peripheral-row');
            const type = row.querySelector('select[name*="[type]"]').value;
            const interface = row.querySelector('select[name*="[interface]"]').value;

            if (!type || !interface) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields for selected peripherals.');
            return;
        }
    });
});
</script>
@endsection