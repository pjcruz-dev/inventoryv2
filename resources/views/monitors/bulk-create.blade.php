@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Bulk Create Monitors</h4>
                    <a href="{{ route('monitors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No available assets found for monitor creation. Please ensure you have assets in the "Monitors" category that are not already assigned to monitors.
                        </div>
                        <a href="{{ route('monitors.index') }}" class="btn btn-secondary">Back to Monitors</a>
                    @else
                        <form action="{{ route('monitors.bulk-store') }}" method="POST" id="bulkCreateForm">
                            @csrf
                            <div class="mb-3">
                                <p class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Found {{ $assets->count() }} available assets for monitor creation. Select the assets you want to create monitors for and fill in the required details.
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
                                            <th width="15%">Size</th>
                                            <th width="20%">Resolution</th>
                                            <th width="15%">Panel Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $index => $asset)
                                        <tr class="monitor-row">
                                            <td>
                                                <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->name }}</strong><br>
                                                <small class="text-muted">{{ $asset->brand }} {{ $asset->model }}</small><br>
                                                <small class="text-muted">Serial: {{ $asset->serial_number }}</small>
                                                <input type="hidden" name="monitors[{{ $index }}][asset_id]" value="{{ $asset->id }}" class="monitor-input" disabled>
                                            </td>
                                            <td>
                                                <input type="text" name="monitors[{{ $index }}][size]" class="form-control monitor-input" placeholder="e.g., 24 inch, 27&quot;" disabled>
                                                @error("monitors.{$index}.size")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="monitors[{{ $index }}][resolution]" class="form-select monitor-input" disabled>
                                                    <option value="">Select Resolution</option>
                                                    <option value="1920x1080">1920x1080 (Full HD)</option>
                                                    <option value="2560x1440">2560x1440 (QHD)</option>
                                                    <option value="3840x2160">3840x2160 (4K UHD)</option>
                                                    <option value="1366x768">1366x768 (HD)</option>
                                                    <option value="1680x1050">1680x1050 (WSXGA+)</option>
                                                    <option value="2560x1080">2560x1080 (UltraWide)</option>
                                                    <option value="3440x1440">3440x1440 (UltraWide QHD)</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <input type="text" name="monitors[{{ $index }}][custom_resolution]" class="form-control mt-2 custom-resolution" placeholder="Enter custom resolution" style="display: none;" disabled>
                                                @error("monitors.{$index}.resolution")
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <select name="monitors[{{ $index }}][panel_type]" class="form-select monitor-input" disabled>
                                                    <option value="">Select Panel Type</option>
                                                    <option value="LCD">LCD</option>
                                                    <option value="LED">LED</option>
                                                    <option value="OLED">OLED</option>
                                                    <option value="CRT">CRT</option>
                                                    <option value="Plasma">Plasma</option>
                                                </select>
                                                @error("monitors.{$index}.panel_type")
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
                                    <span id="selectedCount" class="text-muted">0 monitors selected</span>
                                </div>
                                <div>
                                    <a href="{{ route('monitors.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="fas fa-plus"></i> Create Selected Monitors
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

    // Handle resolution dropdown changes
    document.querySelectorAll('select[name*="[resolution]"]').forEach(select => {
        select.addEventListener('change', function() {
            const customInput = this.parentNode.querySelector('.custom-resolution');
            if (this.value === 'Other') {
                customInput.style.display = 'block';
                customInput.disabled = false;
                customInput.required = true;
            } else {
                customInput.style.display = 'none';
                customInput.disabled = true;
                customInput.required = false;
                customInput.value = '';
            }
        });
    });

    function toggleRowInputs(checkbox) {
        const row = checkbox.closest('.monitor-row');
        const inputs = row.querySelectorAll('.monitor-input');
        
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

        // Handle custom resolution input
        const resolutionSelect = row.querySelector('select[name*="[resolution]"]');
        const customResolution = row.querySelector('.custom-resolution');
        if (!checkbox.checked) {
            customResolution.style.display = 'none';
            customResolution.disabled = true;
            customResolution.value = '';
        }
    }

    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        const totalCount = assetCheckboxes.length;
        
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        selectAllCheckbox.checked = checkedCount === totalCount;
    }

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        selectedCountSpan.textContent = `${checkedCount} monitor${checkedCount !== 1 ? 's' : ''} selected`;
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
            alert('Please select at least one asset to create monitors for.');
            return;
        }

        // Validate that all selected monitors have required fields filled
        let hasErrors = false;
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.monitor-row');
            const size = row.querySelector('input[name*="[size]"]').value.trim();
            const resolution = row.querySelector('select[name*="[resolution]"]').value;
            const panelType = row.querySelector('select[name*="[panel_type]"]').value;
            const customResolution = row.querySelector('.custom-resolution');

            if (!size || !resolution || !panelType) {
                hasErrors = true;
            }

            if (resolution === 'Other' && !customResolution.value.trim()) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fill in all required fields for selected monitors.');
            return;
        }

        // Handle custom resolution
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('.monitor-row');
            const resolutionSelect = row.querySelector('select[name*="[resolution]"]');
            const customResolution = row.querySelector('.custom-resolution');
            
            if (resolutionSelect.value === 'Other' && customResolution.value.trim()) {
                resolutionSelect.value = customResolution.value.trim();
            }
        });
    });
});
</script>
@endsection