@extends('x-layouts.base')

@section('title', 'Peripheral Details')

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
                        <span class="ml-1 text-gray-500 md:ml-2">{{ $peripheral->asset->asset_tag }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between mb-6">
            <div class="flex flex-col">
                <h1 class="mb-0 font-bold text-transparent text-3xl bg-gradient-to-tl from-orange-500 to-yellow-500 bg-clip-text">
                    <i class="fas fa-mouse mr-3"></i>{{ $peripheral->asset->name }}
                </h1>
                <p class="mb-0 text-sm leading-normal text-gray-500">{{ $peripheral->type }} - {{ $peripheral->interface }}</p>
            </div>
            <div class="flex flex-wrap items-center space-x-2">
                <a href="{{ route('peripherals.edit', $peripheral) }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-orange-500 to-yellow-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('peripherals.index') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-gray-600 to-gray-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Peripheral Information -->
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 lg:w-8/12 lg:flex-none">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <div class="flex items-center justify-between">
                            <h6 class="mb-0 text-slate-700">Peripheral Information</h6>
                            @if($peripheral->asset->status)
                                <span class="inline-block px-2 py-1 text-xs font-bold leading-none text-center text-white uppercase align-baseline rounded-lg
                                    @if($peripheral->asset->status == 'Available') bg-gradient-to-tl from-green-600 to-lime-400
                                    @elseif($peripheral->asset->status == 'Assigned') bg-gradient-to-tl from-blue-600 to-cyan-400
                                    @elseif($peripheral->asset->status == 'Maintenance') bg-gradient-to-tl from-orange-600 to-yellow-400
                                    @elseif($peripheral->asset->status == 'Retired') bg-gradient-to-tl from-red-600 to-rose-400
                                    @else bg-gradient-to-tl from-gray-600 to-gray-400
                                    @endif">
                                    {{ $peripheral->asset->status }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-auto px-6 pt-0 pb-6">
                        <div class="flex flex-wrap -mx-3">
                            <!-- Asset Tag -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset Tag</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->asset_tag }}
                                    </div>
                                </div>
                            </div>

                            <!-- Asset Name -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Asset Name</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Type</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->type }}
                                    </div>
                                </div>
                            </div>

                            <!-- Interface -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Interface</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->interface }}
                                    </div>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Category</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->category->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Model -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Model</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->model ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Serial Number -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Serial Number</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->serial_number ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Vendor -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Vendor</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->vendor->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Purchase Date -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Purchase Date</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->purchase_date ? $peripheral->asset->purchase_date->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Purchase Cost -->
                            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Purchase Cost</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700">
                                        {{ $peripheral->asset->purchase_cost ? '$' . number_format($peripheral->asset->purchase_cost, 2) : 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($peripheral->asset->notes)
                            <div class="w-full max-w-full px-3">
                                <div class="mb-4">
                                    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Notes</label>
                                    <div class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full rounded-lg border border-solid border-gray-300 bg-gray-50 bg-clip-padding px-3 py-2 font-normal text-gray-700 min-h-[80px]">
                                        {{ $peripheral->asset->notes }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="w-full max-w-full px-3 lg:w-4/12 lg:flex-none">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 text-slate-700">Quick Actions</h6>
                    </div>
                    <div class="flex-auto px-6 pt-0 pb-6">
                        <div class="flex flex-col space-y-3">
                            <a href="{{ route('peripherals.edit', $peripheral) }}" class="inline-block px-4 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-orange-500 to-yellow-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                <i class="fas fa-edit mr-2"></i>Edit Peripheral
                            </a>
                            
                            @if($peripheral->asset->status == 'Available')
                                <button type="button" class="inline-block px-4 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                                    <i class="fas fa-user-plus mr-2"></i>Assign to User
                                </button>
                            @elseif($peripheral->asset->status == 'Assigned')
                                <button type="button" class="inline-block px-4 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-600 to-pink-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" data-bs-toggle="modal" data-bs-target="#reassignUserModal">
                                    <i class="fas fa-exchange-alt mr-2"></i>Reassign User
                                </button>
                                <button type="button" onclick="unassignAsset({{ $peripheral->asset->id }})" class="inline-block px-4 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-red-600 to-rose-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                    <i class="fas fa-user-minus mr-2"></i>Unassign User
                                </button>
                            @endif
                            
                            <button type="button" onclick="printAssetLabel({{ $peripheral->asset->id }})" class="inline-block px-4 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-green-600 to-lime-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                <i class="fas fa-print mr-2"></i>Print Label
                            </button>
                            
                            <form action="{{ route('peripherals.destroy', $peripheral) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this peripheral?')" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-block w-full px-4 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-red-600 to-rose-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                    <i class="fas fa-trash mr-2"></i>Delete Peripheral
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Assignment Information -->
                @if($peripheral->asset->assignedUser)
                <div class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 text-slate-700">Assignment Information</h6>
                    </div>
                    <div class="flex-auto px-6 pt-0 pb-6">
                        <div class="mb-3">
                            <label class="inline-block mb-1 ml-1 font-bold text-xs text-slate-700">Assigned To</label>
                            <div class="text-sm text-gray-700">{{ $peripheral->asset->assignedUser->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="inline-block mb-1 ml-1 font-bold text-xs text-slate-700">Department</label>
                            <div class="text-sm text-gray-700">{{ $peripheral->asset->assignedUser->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="inline-block mb-1 ml-1 font-bold text-xs text-slate-700">Assigned Date</label>
                            <div class="text-sm text-gray-700">{{ $peripheral->asset->assigned_date ? $peripheral->asset->assigned_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign User Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1" aria-labelledby="assignUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignUserModalLabel">Assign Peripheral to User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.assign', $peripheral->asset) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select User</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Choose a user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->department->name ?? 'No Department' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assignment Date</label>
                        <input type="date" name="assigned_date" id="assigned_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any additional notes about this assignment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Peripheral</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reassign User Modal -->
<div class="modal fade" id="reassignUserModal" tabindex="-1" aria-labelledby="reassignUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassignUserModalLabel">Reassign Peripheral</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.reassign', $peripheral->asset) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Currently Assigned To</label>
                        <input type="text" class="form-control" value="{{ $peripheral->asset->assignedUser->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="new_user_id" class="form-label">Reassign To</label>
                        <select name="user_id" id="new_user_id" class="form-select" required>
                            <option value="">Choose a user...</option>
                            @foreach($users as $user)
                                @if(!$peripheral->asset->assignedUser || $user->id != $peripheral->asset->assignedUser->id)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->department->name ?? 'No Department' }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reassigned_date" class="form-label">Reassignment Date</label>
                        <input type="date" name="assigned_date" id="reassigned_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="reassign_notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="reassign_notes" class="form-control" rows="3" placeholder="Reason for reassignment or additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reassign Peripheral</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function unassignAsset(assetId) {
    if (confirm('Are you sure you want to unassign this peripheral?')) {
        fetch(`/assets/${assetId}/unassign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error unassigning peripheral: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while unassigning the peripheral.');
        });
    }
}

function printAssetLabel(assetId) {
    window.open(`/assets/${assetId}/label`, '_blank');
}

// Filter reassign users based on search
function filterReassignUsers() {
    const searchTerm = document.getElementById('reassignUserSearch').value.toLowerCase();
    const select = document.getElementById('new_user_id');
    const options = select.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') return; // Skip the default option
        
        const text = option.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}
</script>
@endpush