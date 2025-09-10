@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Add New Computer</h4>
                    <a href="{{ route('computers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('computers.store') }}" method="POST">
                        @csrf

                        <!-- Asset Selection -->
                        <div class="form-group">
                            <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                            <select name="asset_id" id="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select an Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($assets->isEmpty())
                                <small class="form-text text-muted">
                                    No available computer assets found. Please create a computer asset first.
                                </small>
                            @endif
                        </div>

                        <!-- Processor -->
                        <div class="form-group">
                            <label for="processor" class="form-label">Processor <span class="text-danger">*</span></label>
                            <input type="text" name="processor" id="processor" 
                                   class="form-control @error('processor') is-invalid @enderror" 
                                   value="{{ old('processor') }}" 
                                   placeholder="e.g., Intel Core i7-12700K, AMD Ryzen 7 5800X" 
                                   required>
                            @error('processor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Memory -->
                        <div class="form-group">
                            <label for="memory" class="form-label">Memory (RAM) <span class="text-danger">*</span></label>
                            <input type="text" name="memory" id="memory" 
                                   class="form-control @error('memory') is-invalid @enderror" 
                                   value="{{ old('memory') }}" 
                                   placeholder="e.g., 16GB DDR4, 32GB DDR5" 
                                   required>
                            @error('memory')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Storage -->
                        <div class="form-group">
                            <label for="storage" class="form-label">Storage <span class="text-danger">*</span></label>
                            <input type="text" name="storage" id="storage" 
                                   class="form-control @error('storage') is-invalid @enderror" 
                                   value="{{ old('storage') }}" 
                                   placeholder="e.g., 512GB SSD, 1TB HDD + 256GB SSD" 
                                   required>
                            @error('storage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Graphics Card -->
                        <div class="form-group">
                            <label for="graphics_card" class="form-label">Graphics Card</label>
                            <input type="text" name="graphics_card" id="graphics_card" 
                                   class="form-control @error('graphics_card') is-invalid @enderror" 
                                   value="{{ old('graphics_card') }}" 
                                   placeholder="e.g., NVIDIA RTX 4070, AMD RX 7800 XT, Integrated">
                            @error('graphics_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Operating System -->
                        <div class="form-group">
                            <label for="operating_system" class="form-label">Operating System</label>
                            <select name="operating_system" id="operating_system" class="form-control @error('operating_system') is-invalid @enderror">
                                <option value="">Select Operating System</option>
                                <option value="Windows 11" {{ old('operating_system') == 'Windows 11' ? 'selected' : '' }}>Windows 11</option>
                                <option value="Windows 10" {{ old('operating_system') == 'Windows 10' ? 'selected' : '' }}>Windows 10</option>
                                <option value="macOS" {{ old('operating_system') == 'macOS' ? 'selected' : '' }}>macOS</option>
                                <option value="Ubuntu" {{ old('operating_system') == 'Ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                                <option value="CentOS" {{ old('operating_system') == 'CentOS' ? 'selected' : '' }}>CentOS</option>
                                <option value="Other Linux" {{ old('operating_system') == 'Other Linux' ? 'selected' : '' }}>Other Linux</option>
                                <option value="Other" {{ old('operating_system') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('operating_system')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('computers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" {{ $assets->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Create Computer
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

@push('scripts')
<script>
    // Auto-focus on first input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('asset_id').focus();
    });
</script>
@endpush