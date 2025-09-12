@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Peripheral') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('peripherals.update', $peripheral->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="asset_id" class="col-md-4 col-form-label text-md-end">{{ __('Asset') }}</label>

                            <div class="col-md-6">
                                <select id="asset_id" class="form-control @error('asset_id') is-invalid @enderror" name="asset_id" required>
                                    <option value="">Select an Asset</option>
                                    @if($assets->count() > 0)
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" {{ (old('asset_id') ?? $peripheral->asset_id) == $asset->id ? 'selected' : '' }}>
                                                {{ $asset->tag }} - {{ $asset->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No available assets found</option>
                                    @endif
                                </select>

                                @error('asset_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="type" class="col-md-4 col-form-label text-md-end">{{ __('Type') }}</label>

                            <div class="col-md-6">
                                <select id="type" class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="Mouse" {{ (old('type') ?? $peripheral->type) == 'Mouse' ? 'selected' : '' }}>Mouse</option>
                                    <option value="Keyboard" {{ (old('type') ?? $peripheral->type) == 'Keyboard' ? 'selected' : '' }}>Keyboard</option>
                                    <option value="Webcam" {{ (old('type') ?? $peripheral->type) == 'Webcam' ? 'selected' : '' }}>Webcam</option>
                                    <option value="Headset" {{ (old('type') ?? $peripheral->type) == 'Headset' ? 'selected' : '' }}>Headset</option>
                                    <option value="Speaker" {{ (old('type') ?? $peripheral->type) == 'Speaker' ? 'selected' : '' }}>Speaker</option>
                                    <option value="Microphone" {{ (old('type') ?? $peripheral->type) == 'Microphone' ? 'selected' : '' }}>Microphone</option>
                                    <option value="USB Hub" {{ (old('type') ?? $peripheral->type) == 'USB Hub' ? 'selected' : '' }}>USB Hub</option>
                                    <option value="External Drive" {{ (old('type') ?? $peripheral->type) == 'External Drive' ? 'selected' : '' }}>External Drive</option>
                                    <option value="Other" {{ (old('type') ?? $peripheral->type) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>

                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="interface" class="col-md-4 col-form-label text-md-end">{{ __('Interface') }}</label>

                            <div class="col-md-6">
                                <select id="interface" class="form-control @error('interface') is-invalid @enderror" name="interface" required>
                                    <option value="">Select Interface</option>
                                    <option value="USB" {{ (old('interface') ?? $peripheral->interface) == 'USB' ? 'selected' : '' }}>USB</option>
                                    <option value="Bluetooth" {{ (old('interface') ?? $peripheral->interface) == 'Bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                                    <option value="Wireless" {{ (old('interface') ?? $peripheral->interface) == 'Wireless' ? 'selected' : '' }}>Wireless</option>
                                    <option value="Wired" {{ (old('interface') ?? $peripheral->interface) == 'Wired' ? 'selected' : '' }}>Wired</option>
                                </select>

                                @error('interface')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Peripheral') }}
                                </button>
                                <a href="{{ route('peripherals.index') }}" class="btn btn-secondary ms-2">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form method="POST" action="{{ route('peripherals.destroy', $peripheral->id) }}" class="mt-3" onsubmit="return confirm('Are you sure you want to delete this peripheral?')">
                        @csrf
                        @method('DELETE')
                        <div class="row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-danger">
                                    {{ __('Delete Peripheral') }}
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