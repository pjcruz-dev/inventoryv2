@extends('layouts.base')

@section('title', 'Edit Asset')

@section('content')
<div class="flex flex-wrap -mx-3">
    <!-- Breadcrumb -->
    <div class="w-full max-w-full px-3 mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('assets.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Assets</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit {{ $asset->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    
    <!-- Page Header -->
    <div class="w-full max-w-full px-3 mb-6">
        <div class="flex flex-wrap items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Edit Asset: {{ $asset->name }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-soft-xl">
                    <i class="fas fa-eye mr-2"></i>View Asset
                </a>
                <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white text-sm font-medium rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 shadow-soft-xl">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Assets
                </a>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3 lg:w-8/12 lg:flex-none">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="text-lg font-bold text-gray-900">Asset Information</h6>
            </div>
            <div class="flex-auto px-6 pt-0 pb-6">
                <form method="POST" action="{{ route('assets.update', $asset) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                            <div class="mb-4">
                                <label for="asset_tag" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset Tag <span class="text-red-500">*</span></label>
                                <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('asset_tag') border-red-300 @enderror" 
                                       id="asset_tag" name="asset_tag" value="{{ old('asset_tag', $asset->asset_tag) }}" required>
                                @error('asset_tag')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                            <div class="mb-4">
                                <label for="name" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset Name <span class="text-red-500">*</span></label>
                                <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('name') border-red-300 @enderror" 
                                       id="name" name="name" value="{{ old('name', $asset->name) }}" required>
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
                                        <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
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
                                    <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $asset->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="deployed" {{ old('status', $asset->status) == 'deployed' ? 'selected' : '' }}>Deployed</option>
                                    <option value="problematic" {{ old('status', $asset->status) == 'problematic' ? 'selected' : '' }}>Problematic</option>
                                    <option value="pending_confirm" {{ old('status', $asset->status) == 'pending_confirm' ? 'selected' : '' }}>Pending Confirm</option>
                                    <option value="returned" {{ old('status', $asset->status) == 'returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                    <option value="new_arrived" {{ old('status', $asset->status) == 'new_arrived' ? 'selected' : '' }}>New Arrived</option>
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
                                       id="model" name="model" value="{{ old('model', $asset->model) }}">
                                @error('model')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                            <div class="mb-4">
                                <label for="serial_number" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Serial Number</label>
                                <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('serial_number') border-red-300 @enderror" 
                                       id="serial_number" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}">
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
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id', $asset->vendor_id) == $vendor->id ? 'selected' : '' }}>
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
                                       id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}">
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
                                           id="cost" name="cost" value="{{ old('cost', $asset->cost) }}">
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
                                        <option value="{{ $user->id }}" {{ old('assigned_to', $asset->assigned_to) == $user->id ? 'selected' : '' }}>
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
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                            <div class="mb-4">
                                <label for="location" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Location</label>
                                <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('location') border-red-300 @enderror" 
                                       id="location" name="location" value="{{ old('location', $asset->location) }}" 
                                       placeholder="e.g., Office 101, Warehouse A">
                                @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                            <div class="mb-4">
                                <label for="notes" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Notes</label>
                                <textarea class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('notes') border-red-300 @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional information about this asset...">{{ old('notes', $asset->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="{{ route('assets.show', $asset) }}" class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">Cancel</a>
                        <button type="submit" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                            <i class="fas fa-save mr-2"></i>Update Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="w-full max-w-full px-3 lg:w-4/12 lg:flex-none">
        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-4 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="mb-0 font-bold">Asset Details</h6>
            </div>
            <div class="flex-auto p-4">
                <div class="mb-4">
                    <span class="text-xs font-bold text-slate-700">Created:</span>
                    <p class="text-sm text-slate-600">{{ $asset->created_at->format('M d, Y') }}</p>
                </div>
                
                <div class="mb-4">
                    <span class="text-xs font-bold text-slate-700">Last Updated:</span>
                    <p class="text-sm text-slate-600">{{ $asset->updated_at->format('M d, Y') }}</p>
                </div>
                
                <div class="mb-4">
                    <span class="text-xs font-bold text-slate-700">Current Status:</span>
                    <span class="inline-block px-2 py-1 text-xs font-bold text-white uppercase bg-gradient-to-tl {{ $asset->status === 'active' ? 'from-green-600 to-lime-400' : ($asset->status === 'inactive' ? 'from-red-600 to-rose-400' : 'from-orange-500 to-yellow-500') }} rounded-lg">
                        {{ ucfirst($asset->status) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-4 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h6 class="mb-0 font-bold">Quick Actions</h6>
            </div>
            <div class="flex-auto p-4">
                <div class="space-y-3">
                    <a href="{{ route('assets.show', $asset) }}" class="inline-block w-full px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-violet-600 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                        <i class="fas fa-eye mr-2"></i>View Details
                    </a>
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-block w-full px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-red-600 to-rose-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                            <i class="fas fa-trash mr-2"></i>Delete Asset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any edit-specific JavaScript here
</script>
@endsection