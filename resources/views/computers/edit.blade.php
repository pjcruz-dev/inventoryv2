@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Computer</h4>
                    <div>
                        <a href="{{ route('computers.show', $computer) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('computers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('computers.update', $computer) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Asset Selection -->
                        <div class="form-group">
                            <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                            <select name="asset_id" id="asset_id" class="form-control searchable-select @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select an Asset</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                            {{ (old('asset_id', $computer->asset_id) == $asset->id) ? 'selected' : '' }}>
                                        {{ $asset->asset_tag }} - {{ $asset->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Processor -->
                        <div class="form-group">
                            <label for="processor" class="form-label">Processor <span class="text-danger">*</span></label>
                            <input type="text" name="processor" id="processor" 
                                   class="form-control @error('processor') is-invalid @enderror" 
                                   value="{{ old('processor', $computer->processor) }}" 
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
                                   value="{{ old('memory', $computer->memory) }}" 
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
                                   value="{{ old('storage', $computer->storage) }}" 
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
                                   value="{{ old('graphics_card', $computer->graphics_card) }}" 
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
                                <option value="Windows 11" {{ old('operating_system', $computer->operating_system) == 'Windows 11' ? 'selected' : '' }}>Windows 11</option>
                                <option value="Windows 10" {{ old('operating_system', $computer->operating_system) == 'Windows 10' ? 'selected' : '' }}>Windows 10</option>
                                <option value="macOS" {{ old('operating_system', $computer->operating_system) == 'macOS' ? 'selected' : '' }}>macOS</option>
                                <option value="Ubuntu" {{ old('operating_system', $computer->operating_system) == 'Ubuntu' ? 'selected' : '' }}>Ubuntu</option>
                                <option value="CentOS" {{ old('operating_system', $computer->operating_system) == 'CentOS' ? 'selected' : '' }}>CentOS</option>
                                <option value="Other Linux" {{ old('operating_system', $computer->operating_system) == 'Other Linux' ? 'selected' : '' }}>Other Linux</option>
                                <option value="Other" {{ old('operating_system', $computer->operating_system) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('operating_system')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Computer Type -->
                        <div class="form-group">
                            <label for="computer_type" class="form-label">Computer Type <span class="text-danger">*</span></label>
                            <select name="computer_type" id="computer_type" class="form-control @error('computer_type') is-invalid @enderror" required>
                                <option value="">Select Computer Type</option>
                                <option value="Desktop" {{ old('computer_type', $computer->computer_type) == 'Desktop' ? 'selected' : '' }}>Desktop</option>
                                <option value="Laptop" {{ old('computer_type', $computer->computer_type) == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                                <option value="Server" {{ old('computer_type', $computer->computer_type) == 'Server' ? 'selected' : '' }}>Server</option>
                                <option value="Workstation" {{ old('computer_type', $computer->computer_type) == 'Workstation' ? 'selected' : '' }}>Workstation</option>
                            </select>
                            @error('computer_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('computers.show', $computer) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('computers.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Computer
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <hr class="my-4">
                    <div class="text-center">
                        <form action="{{ route('computers.destroy', $computer) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this computer? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> Delete Computer
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
    // Auto-focus on first input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('asset_id').focus();
    });
</script>
@endpush