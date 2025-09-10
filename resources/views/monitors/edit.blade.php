@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Monitor</h4>
                    <div>
                        <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('monitors.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('monitors.update', $monitor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Asset Selection -->
                        <div class="form-group">
                            <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                            <select name="asset_id" id="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select an Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                            {{ (old('asset_id', $monitor->asset_id) == $asset->id) ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Size -->
                        <div class="form-group">
                            <label for="size" class="form-label">Size <span class="text-danger">*</span></label>
                            <input type="text" name="size" id="size" 
                                   class="form-control @error('size') is-invalid @enderror" 
                                   value="{{ old('size', $monitor->size) }}" 
                                   placeholder="e.g., 24 inch, 27\", 32 inch" 
                                   required>
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Resolution -->
                        <div class="form-group">
                            <label for="resolution" class="form-label">Resolution <span class="text-danger">*</span></label>
                            <select name="resolution" id="resolution" class="form-control @error('resolution') is-invalid @enderror" required>
                                <option value="">Select Resolution</option>
                                <option value="1920x1080" {{ old('resolution', $monitor->resolution) == '1920x1080' ? 'selected' : '' }}>1920x1080 (Full HD)</option>
                                <option value="2560x1440" {{ old('resolution', $monitor->resolution) == '2560x1440' ? 'selected' : '' }}>2560x1440 (QHD)</option>
                                <option value="3840x2160" {{ old('resolution', $monitor->resolution) == '3840x2160' ? 'selected' : '' }}>3840x2160 (4K UHD)</option>
                                <option value="1366x768" {{ old('resolution', $monitor->resolution) == '1366x768' ? 'selected' : '' }}>1366x768 (HD)</option>
                                <option value="1680x1050" {{ old('resolution', $monitor->resolution) == '1680x1050' ? 'selected' : '' }}>1680x1050 (WSXGA+)</option>
                                <option value="1920x1200" {{ old('resolution', $monitor->resolution) == '1920x1200' ? 'selected' : '' }}>1920x1200 (WUXGA)</option>
                                <option value="2560x1600" {{ old('resolution', $monitor->resolution) == '2560x1600' ? 'selected' : '' }}>2560x1600 (WQXGA)</option>
                                <option value="5120x2880" {{ old('resolution', $monitor->resolution) == '5120x2880' ? 'selected' : '' }}>5120x2880 (5K)</option>
                                @php
                                    $standardResolutions = ['1920x1080', '2560x1440', '3840x2160', '1366x768', '1680x1050', '1920x1200', '2560x1600', '5120x2880'];
                                    $isCustomResolution = !in_array(old('resolution', $monitor->resolution), $standardResolutions) && old('resolution', $monitor->resolution);
                                @endphp
                                <option value="Other" {{ $isCustomResolution ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('resolution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Custom Resolution Input -->
                        <div class="form-group" id="custom-resolution-group" style="display: {{ $isCustomResolution ? 'block' : 'none' }};">
                            <label for="custom_resolution" class="form-label">Custom Resolution</label>
                            <input type="text" name="custom_resolution" id="custom_resolution" 
                                   class="form-control" 
                                   value="{{ $isCustomResolution ? old('resolution', $monitor->resolution) : '' }}"
                                   placeholder="e.g., 3440x1440">
                        </div>

                        <!-- Panel Type -->
                        <div class="form-group">
                            <label for="panel_type" class="form-label">Panel Type <span class="text-danger">*</span></label>
                            <select name="panel_type" id="panel_type" class="form-control @error('panel_type') is-invalid @enderror" required>
                                <option value="">Select Panel Type</option>
                                <option value="LCD" {{ old('panel_type', $monitor->panel_type) == 'LCD' ? 'selected' : '' }}>LCD</option>
                                <option value="LED" {{ old('panel_type', $monitor->panel_type) == 'LED' ? 'selected' : '' }}>LED</option>
                                <option value="OLED" {{ old('panel_type', $monitor->panel_type) == 'OLED' ? 'selected' : '' }}>OLED</option>
                                <option value="CRT" {{ old('panel_type', $monitor->panel_type) == 'CRT' ? 'selected' : '' }}>CRT</option>
                                <option value="Plasma" {{ old('panel_type', $monitor->panel_type) == 'Plasma' ? 'selected' : '' }}>Plasma</option>
                            </select>
                            @error('panel_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('monitors.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Monitor
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <hr class="my-4">
                    <div class="text-center">
                        <form action="{{ route('monitors.destroy', $monitor) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this monitor? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> Delete Monitor
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resolutionSelect = document.getElementById('resolution');
        const customResolutionGroup = document.getElementById('custom-resolution-group');
        const customResolutionInput = document.getElementById('custom_resolution');
        
        // Show/hide custom resolution input
        resolutionSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                customResolutionGroup.style.display = 'block';
                customResolutionInput.required = true;
            } else {
                customResolutionGroup.style.display = 'none';
                customResolutionInput.required = false;
                customResolutionInput.value = '';
            }
        });
        
        // Handle form submission for custom resolution
        document.querySelector('form').addEventListener('submit', function(e) {
            if (resolutionSelect.value === 'Other' && customResolutionInput.value) {
                resolutionSelect.value = customResolutionInput.value;
            }
        });
        
        // Auto-focus on first input
        document.getElementById('asset_id').focus();
    });
</script>
@endpush