@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-plus"></i> Add New Printer
                    </h4>
                    <a href="{{ route('printers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('printers.store') }}" method="POST">
                        @csrf

                        <!-- Asset Selection -->
                        <div class="form-group">
                            <label for="asset_id" class="required">Asset <span class="text-danger">*</span></label>
                            <select name="asset_id" id="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select an Asset</option>
                                @foreach($availableAssets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }}
                                        @if($asset->user)
                                            (Assigned to: {{ $asset->user->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Only assets not currently assigned to other equipment are shown.
                            </small>
                        </div>

                        <!-- Printer Type -->
                        <div class="form-group">
                            <label for="type" class="required">Printer Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Printer Type</option>
                                <option value="Inkjet" {{ old('type') == 'Inkjet' ? 'selected' : '' }}>Inkjet</option>
                                <option value="Laser" {{ old('type') == 'Laser' ? 'selected' : '' }}>Laser</option>
                                <option value="Dot Matrix" {{ old('type') == 'Dot Matrix' ? 'selected' : '' }}>Dot Matrix</option>
                                <option value="Thermal" {{ old('type') == 'Thermal' ? 'selected' : '' }}>Thermal</option>
                                <option value="3D" {{ old('type') == '3D' ? 'selected' : '' }}>3D Printer</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Color Support -->
                        <div class="form-group">
                            <label class="required">Color Support <span class="text-danger">*</span></label>
                            <div class="form-check-container mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('color_support') is-invalid @enderror" 
                                           type="radio" name="color_support" id="color_yes" value="1" 
                                           {{ old('color_support') == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="color_yes">
                                        <i class="fas fa-palette text-success"></i> Color Printing
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('color_support') is-invalid @enderror" 
                                           type="radio" name="color_support" id="color_no" value="0" 
                                           {{ old('color_support') == '0' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="color_no">
                                        <i class="fas fa-circle text-secondary"></i> Monochrome Only
                                    </label>
                                </div>
                            </div>
                            @error('color_support')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Network Enabled -->
                        <div class="form-group">
                            <label class="required">Network Connectivity <span class="text-danger">*</span></label>
                            <div class="form-check-container mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('network_enabled') is-invalid @enderror" 
                                           type="radio" name="network_enabled" id="network_yes" value="1" 
                                           {{ old('network_enabled') == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="network_yes">
                                        <i class="fas fa-wifi text-success"></i> Network Enabled
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('network_enabled') is-invalid @enderror" 
                                           type="radio" name="network_enabled" id="network_no" value="0" 
                                           {{ old('network_enabled') == '0' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="network_no">
                                        <i class="fas fa-times text-secondary"></i> Local Connection Only
                                    </label>
                                </div>
                            </div>
                            @error('network_enabled')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duplex Printing -->
                        <div class="form-group">
                            <label class="required">Duplex Printing <span class="text-danger">*</span></label>
                            <div class="form-check-container mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('duplex_printing') is-invalid @enderror" 
                                           type="radio" name="duplex_printing" id="duplex_yes" value="1" 
                                           {{ old('duplex_printing') == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="duplex_yes">
                                        <i class="fas fa-copy text-success"></i> Duplex Supported
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('duplex_printing') is-invalid @enderror" 
                                           type="radio" name="duplex_printing" id="duplex_no" value="0" 
                                           {{ old('duplex_printing') == '0' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="duplex_no">
                                        <i class="fas fa-times text-secondary"></i> Single-sided Only
                                    </label>
                                </div>
                            </div>
                            @error('duplex_printing')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Print Speed -->
                        <div class="form-group">
                            <label for="print_speed">Print Speed (pages per minute)</label>
                            <div class="input-group">
                                <input type="number" name="print_speed" id="print_speed" 
                                       class="form-control @error('print_speed') is-invalid @enderror" 
                                       value="{{ old('print_speed') }}" min="1" max="1000" step="1"
                                       placeholder="e.g., 20">
                                <div class="input-group-append">
                                    <span class="input-group-text">ppm</span>
                                </div>
                                @error('print_speed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Optional: Specify the printer's speed in pages per minute.
                            </small>
                        </div>

                        <!-- Max Paper Size -->
                        <div class="form-group">
                            <label for="max_paper_size">Maximum Paper Size</label>
                            <select name="max_paper_size" id="max_paper_size" class="form-control @error('max_paper_size') is-invalid @enderror">
                                <option value="">Select Maximum Paper Size</option>
                                <option value="A4" {{ old('max_paper_size') == 'A4' ? 'selected' : '' }}>A4</option>
                                <option value="A3" {{ old('max_paper_size') == 'A3' ? 'selected' : '' }}>A3</option>
                                <option value="Letter" {{ old('max_paper_size') == 'Letter' ? 'selected' : '' }}>Letter</option>
                                <option value="Legal" {{ old('max_paper_size') == 'Legal' ? 'selected' : '' }}>Legal</option>
                                <option value="Tabloid" {{ old('max_paper_size') == 'Tabloid' ? 'selected' : '' }}>Tabloid</option>
                                <option value="A2" {{ old('max_paper_size') == 'A2' ? 'selected' : '' }}>A2</option>
                                <option value="A1" {{ old('max_paper_size') == 'A1' ? 'selected' : '' }}>A1</option>
                                <option value="A0" {{ old('max_paper_size') == 'A0' ? 'selected' : '' }}>A0</option>
                            </select>
                            @error('max_paper_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group mb-0">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('printers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Printer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.required {
    font-weight: 600;
}

.form-check-container {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

.form-check-inline {
    margin-right: 2rem;
}

.form-check-label {
    cursor: pointer;
    font-weight: 500;
}

.form-check-label i {
    margin-right: 5px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-focus first input
    $('#asset_id').focus();
    
    // Add some interactivity for better UX
    $('input[type="radio"]').change(function() {
        $(this).closest('.form-check-container').find('.form-check').removeClass('selected');
        $(this).closest('.form-check').addClass('selected');
    });
});
</script>
@endpush