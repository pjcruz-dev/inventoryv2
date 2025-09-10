@extends('layouts.base')

@section('title', 'Create Asset')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Create New Asset</h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-home w-4 h-4"></i>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('assets.index') }}" class="text-gray-500 hover:text-gray-700">Assets</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-900">Create</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('assets.index') }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-slate-700 uppercase align-middle transition-all border border-gray-200 rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft bg-white hover:scale-102 active:opacity-85">
            <i class="fas fa-arrow-left mr-2"></i>Back to Assets
        </a>
    </div>

    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 lg:w-8/12 lg:flex-none">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                    <h6 class="text-slate-700 font-bold">Asset Information</h6>
                </div>
                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-6 pt-0">
                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf
                    
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="asset_tag" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset Tag <span class="text-red-500">*</span></label>
                                    <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('asset_tag') border-red-300 @enderror" 
                                           id="asset_tag" name="asset_tag" value="{{ old('asset_tag') }}" required>
                                    @error('asset_tag')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="name" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset Name <span class="text-red-500">*</span></label>
                                    <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('name') border-red-300 @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="category_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Category</label>
                                    <select class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('category_id') border-red-300 @enderror" id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="status" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Current Status <span class="text-red-500">*</span></label>
                                    <select class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('status') border-red-300 @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="deployed" {{ old('status') == 'deployed' ? 'selected' : '' }}>Deployed</option>
                                        <option value="problematic" {{ old('status') == 'problematic' ? 'selected' : '' }}>Problematic</option>
                                        <option value="pending_confirm" {{ old('status') == 'pending_confirm' ? 'selected' : '' }}>Pending Confirm</option>
                                        <option value="returned" {{ old('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                        <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                        <option value="new_arrived" {{ old('status') == 'new_arrived' ? 'selected' : '' }}>New Arrived</option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="model" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Model</label>
                                    <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('model') border-red-300 @enderror" 
                                           id="model" name="model" value="{{ old('model') }}">
                                    @error('model')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="serial_number" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Serial Number</label>
                                    <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('serial_number') border-red-300 @enderror" 
                                           id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                                    @error('serial_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="vendor_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Vendor</label>
                                    <select class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('vendor_id') border-red-300 @enderror" id="vendor_id" name="vendor_id">
                                        <option value="">Select Vendor</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="purchase_date" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Purchase Date</label>
                                    <input type="date" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('purchase_date') border-red-300 @enderror" 
                                           id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
                                    @error('purchase_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="cost" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Purchase Cost</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500">₱</span>
                                        <input type="number" step="0.01" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding pl-8 pr-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('cost') border-red-300 @enderror" 
                                               id="cost" name="cost" value="{{ old('cost') }}">
                                        @error('cost')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label for="assigned_to" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Assigned To</label>
                                    <select class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('assigned_to') border-red-300 @enderror" id="assigned_to" name="assigned_to">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="mb-4">
                            <label for="location" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Location</label>
                            <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('location') border-red-300 @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" 
                                   placeholder="e.g., Office 101, Warehouse A">
                            @error('location')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    
                        <div class="mb-4">
                            <label for="notes" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Notes</label>
                            <textarea class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('notes') border-red-300 @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Additional information about this asset...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    
                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('assets.index') }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-slate-700 uppercase align-middle transition-all border border-gray-200 rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft bg-white hover:scale-102 active:opacity-85">Cancel</a>
                            <button type="submit" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                <i class="fas fa-save mr-2"></i>Create Asset
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="w-full max-w-full px-3 lg:w-4/12 lg:flex-none">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                    <h6 class="text-slate-700 font-bold">Tips</h6>
                </div>
                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-6 pt-0">
                        <ul class="list-none mb-0">
                            <li class="mb-3 flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 mr-3 mt-1"></i>
                                <span class="text-sm text-gray-600">Asset tags should be unique identifiers</span>
                            </li>
                            <li class="mb-3 flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 mr-3 mt-1"></i>
                                <span class="text-sm text-gray-600">Use descriptive names for easy identification</span>
                            </li>
                            <li class="mb-3 flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 mr-3 mt-1"></i>
                                <span class="text-sm text-gray-600">Keep serial numbers for warranty tracking</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 mr-3 mt-1"></i>
                                <span class="text-sm text-gray-600">Regular status updates help with maintenance</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate asset tag based on category and current date
    document.getElementById('category_id').addEventListener('change', function() {
        const assetTagField = document.getElementById('asset_tag');
        if (!assetTagField.value && this.value) {
            const categoryText = this.options[this.selectedIndex].text.substring(0, 3).toUpperCase();
            const date = new Date();
            const timestamp = date.getFullYear().toString().substr(-2) + 
                            String(date.getMonth() + 1).padStart(2, '0') + 
                            String(date.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            assetTagField.value = categoryText + '-' + timestamp + '-' + random;
        }
    });
</script>
@endsection