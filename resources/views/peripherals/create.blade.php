@extends('x-layouts.base')

@section('title', 'Add New Peripheral')

@section('content')
<div class="flex flex-wrap -mx-3">
    <div class="flex-none w-full max-w-full px-3">
        <!-- Breadcrumb -->
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-blue-600">
                        <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('peripherals.index') }}" class="ml-1 text-gray-500 hover:text-blue-600 md:ml-2">Peripherals</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Add New</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between mb-6">
            <div class="flex flex-col">
                <h1 class="mb-0 font-bold text-transparent text-3xl bg-gradient-to-tl from-purple-700 to-pink-500 bg-clip-text">
                    <i class="fas fa-plus mr-3"></i>Add New Peripheral
                </h1>
                <p class="mb-0 text-sm leading-normal text-gray-500">Create a new peripheral record in your inventory</p>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ route('peripherals.index') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-gray-600 to-gray-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Create Form -->
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 text-slate-700">Peripheral Information</h6>
                        <p class="text-sm leading-normal text-slate-400">Fill in the details for the new peripheral</p>
                    </div>
                    <div class="flex-auto px-6 pt-0 pb-6">
                        <form action="{{ route('peripherals.store') }}" method="POST" class="mt-4">
                            @csrf
                            
                            <div class="flex flex-wrap -mx-3">
                                <!-- Asset Selection -->
                                <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                    <div class="mb-4">
                                        <label for="asset_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset <span class="text-red-500">*</span></label>
                                        <select name="asset_id" id="asset_id" required class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('asset_id') border-red-500 @enderror">
                                            <option value="">Select an Asset</option>
                                            @foreach($assets as $asset)
                                                <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                                    {{ $asset->asset_tag }} - {{ $asset->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('asset_id')
                                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if($assets->isEmpty())
                                            <p class="mt-2 text-xs text-orange-500">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                No available peripheral assets found. <a href="{{ route('assets.create') }}" class="text-blue-500 hover:text-blue-700">Create a new asset</a> first.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Type -->
                                <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                    <div class="mb-4">
                                        <label for="type" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Type <span class="text-red-500">*</span></label>
                                        <select name="type" id="type" required class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('type') border-red-500 @enderror">
                                            <option value="">Select Type</option>
                                            <option value="Mouse" {{ old('type') == 'Mouse' ? 'selected' : '' }}>Mouse</option>
                                            <option value="Keyboard" {{ old('type') == 'Keyboard' ? 'selected' : '' }}>Keyboard</option>
                                            <option value="Webcam" {{ old('type') == 'Webcam' ? 'selected' : '' }}>Webcam</option>
                                            <option value="Headset" {{ old('type') == 'Headset' ? 'selected' : '' }}>Headset</option>
                                            <option value="Speaker" {{ old('type') == 'Speaker' ? 'selected' : '' }}>Speaker</option>
                                            <option value="Microphone" {{ old('type') == 'Microphone' ? 'selected' : '' }}>Microphone</option>
                                            <option value="Drawing Tablet" {{ old('type') == 'Drawing Tablet' ? 'selected' : '' }}>Drawing Tablet</option>
                                            <option value="Game Controller" {{ old('type') == 'Game Controller' ? 'selected' : '' }}>Game Controller</option>
                                            <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('type')
                                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Interface -->
                                <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                    <div class="mb-4">
                                        <label for="interface" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Interface <span class="text-red-500">*</span></label>
                                        <select name="interface" id="interface" required class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('interface') border-red-500 @enderror">
                                            <option value="">Select Interface</option>
                                            <option value="USB" {{ old('interface') == 'USB' ? 'selected' : '' }}>USB</option>
                                            <option value="Wireless" {{ old('interface') == 'Wireless' ? 'selected' : '' }}>Wireless</option>
                                            <option value="Bluetooth" {{ old('interface') == 'Bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                                            <option value="PS/2" {{ old('interface') == 'PS/2' ? 'selected' : '' }}>PS/2</option>
                                            <option value="USB-C" {{ old('interface') == 'USB-C' ? 'selected' : '' }}>USB-C</option>
                                            <option value="3.5mm Jack" {{ old('interface') == '3.5mm Jack' ? 'selected' : '' }}>3.5mm Jack</option>
                                            <option value="HDMI" {{ old('interface') == 'HDMI' ? 'selected' : '' }}>HDMI</option>
                                            <option value="Other" {{ old('interface') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('interface')
                                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex flex-wrap items-center justify-end mt-6 pt-6 border-t border-gray-200">
                                <a href="{{ route('peripherals.index') }}" class="inline-block px-6 py-3 mr-3 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                    <i class="fas fa-save mr-2"></i>Create Peripheral
                                </button>
                            </div>
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
// Auto-populate type based on asset name if possible
document.getElementById('asset_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const assetName = selectedOption.text.toLowerCase();
    const typeSelect = document.getElementById('type');
    
    // Auto-suggest type based on asset name
    if (assetName.includes('mouse')) {
        typeSelect.value = 'Mouse';
    } else if (assetName.includes('keyboard')) {
        typeSelect.value = 'Keyboard';
    } else if (assetName.includes('webcam') || assetName.includes('camera')) {
        typeSelect.value = 'Webcam';
    } else if (assetName.includes('headset') || assetName.includes('headphone')) {
        typeSelect.value = 'Headset';
    } else if (assetName.includes('speaker')) {
        typeSelect.value = 'Speaker';
    } else if (assetName.includes('microphone') || assetName.includes('mic')) {
        typeSelect.value = 'Microphone';
    }
});

// Auto-suggest interface based on type
document.getElementById('type').addEventListener('change', function() {
    const interfaceSelect = document.getElementById('interface');
    const selectedType = this.value;
    
    // Common interface suggestions
    if (selectedType === 'Mouse' || selectedType === 'Keyboard') {
        if (!interfaceSelect.value) {
            interfaceSelect.value = 'USB'; // Default suggestion
        }
    } else if (selectedType === 'Headset') {
        if (!interfaceSelect.value) {
            interfaceSelect.value = '3.5mm Jack'; // Default suggestion
        }
    } else if (selectedType === 'Webcam') {
        if (!interfaceSelect.value) {
            interfaceSelect.value = 'USB'; // Default suggestion
        }
    }
});
</script>
@endpush