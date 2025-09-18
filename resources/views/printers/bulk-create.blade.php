@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Bulk Create Printers</h4>
                    <a href="{{ route('printers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No available assets found for printer creation. Please ensure you have assets in the "Printers" category that are not already assigned to printers.
                        </div>
                        <a href="{{ route('printers.index') }}" class="btn btn-secondary">Back to Printers</a>
                    @else
                        <form action="{{ route('printers.bulk-store') }}" method="POST" id="bulkCreateForm">
                            @csrf
                            <div class="mb-3">
                                <p class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Found {{ $assets->count() }} available assets for printer creation. Select the assets you want to create printers for and fill in the required details.
                                </p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th width="25%">Asset</th>
                                            <th width="20%">Type</th>
                                            <th width="15%">Color Support</th>
                                            <th width="15%">Duplex</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $index => $asset)
                                        <tr class="printer-row">
                                            <td>
                                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->name }}</strong><br>
                                                <small class="text-muted">{{ $asset->brand }} {{ $asset->model }}</small><br>
                                                <small class="text-muted">Serial: {{ $asset->serial_number }}</small>
                                                <input type="hidden" name="printers[{{ $index }}][asset_id]" value="{{ $asset->id }}" class="printer-input" disabled>
                                            </td>
                                            <td>
                                                <select name="printers[{{ $index }}][type]" class="form-select printer-input" disabled>
                                                    <option value="">Select Type</option>
                                                    <option value="Inkjet">Inkjet</option>
                                                    <option value="Laser">Laser</option>
                                                    <option value="Dot Matrix">Dot Matrix</option>
                                                    <option value="Thermal">Thermal</option>
                                                    <option value="3D">3D</option>
                                                </select>
                                                @error("printers.{$index}.type")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="printers[{{ $index }}][color_support]" class="form-select printer-input" disabled>
                                                    <option value="">Select Color Support</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                                @error("printers.{$index}.color_support")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="printers[{{ $index }}][duplex]" class="form-select printer-input" disabled>
                                                    <option value="">Select Duplex</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                                @error("printers.{$index}.duplex")
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
                                    <span id="selectedCount" class="text-muted">0 printers selected</span>
                                </div>
                                <div>
                                    <a href="{{ route('printers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="fas fa-plus"></i> Create Selected Printers
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
        const row = checkbox.closest('.printer-row');
        const inputs = row.querySelectorAll('.printer-input');
        
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
        selectedCountSpan.textContent = `${checkedCount} printer${checkedCount !== 1 ? 's' : ''} selected`;
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
            alert('Please select at least one asset to create printers for.');
            return;
        }

        // Validate that all selected printers have required fields filled
        let hasErrors = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.printer-row');
            const type = row.querySelector('select[name*="[type]"]').value;
            const colorSupport = row.querySelector('select[name*="[color_support]"]').value;
            const duplexSupport = row.querySelector('select[name*="[duplex_support]"]').value;

            if (!type || colorSupport === '' || duplexSupport === '') {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields for selected printers.');
            return;
        }
    });
});
</script>
@endsection