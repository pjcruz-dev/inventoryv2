<x-layouts.base>
    <div class="w-full px-6 py-6 mx-auto">
        <!-- Breadcrumb -->
        <nav class="w-full rounded-md">
            <ol class="list-reset flex">
                <li><a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-600">Dashboard</a></li>
                <li><span class="text-slate-400 mx-2">/</span></li>
                <li><a href="{{ route('assets.index') }}" class="text-slate-500 hover:text-slate-600">Assets</a></li>
                <li><span class="text-slate-400 mx-2">/</span></li>
                <li class="text-slate-400">{{ $asset->name }}</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="flex-none w-full max-w-full px-3">
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div>
                        <h6 class="mb-0 font-bold capitalize">Asset: {{ $asset->name }}</h6>
                        <p class="mb-0 text-sm leading-normal text-slate-400">View and manage asset details</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('assets.edit', $asset) }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                            <i class="fas fa-edit mr-2"></i>Edit Asset
                        </a>
                        <a href="{{ route('assets.index') }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Assets
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 lg:w-8/12 lg:flex-none">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <div class="flex flex-wrap items-center justify-between">
                            <h6 class="mb-0 font-bold">Asset Information</h6>
                            <span class="inline-block px-2 py-1 text-xs font-bold text-white uppercase bg-gradient-to-tl {{ 
                                $asset->status === 'active' ? 'from-green-600 to-lime-400' : 
                                ($asset->status === 'deployed' ? 'from-blue-600 to-violet-600' : 
                                ($asset->status === 'inactive' ? 'from-red-600 to-rose-400' : 
                                ($asset->status === 'problematic' ? 'from-red-600 to-rose-400' : 
                                ($asset->status === 'disposed' ? 'from-slate-600 to-slate-400' : 
                                ($asset->status === 'maintenance' ? 'from-orange-500 to-yellow-500' : 
                                ($asset->status === 'pending_confirm' ? 'from-blue-600 to-cyan-400' : 
                                ($asset->status === 'returned' ? 'from-slate-600 to-slate-400' : 
                                ($asset->status === 'new_arrived' ? 'from-green-600 to-lime-400' : 'from-orange-500 to-yellow-500')))))))))
                            }} rounded-lg">
                                {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-6">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                    <dl class="space-y-4">
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Asset Tag:</dt>
                                            <dd class="w-2/3">
                                                <code class="px-2 py-1 text-sm bg-slate-100 rounded">{{ $asset->asset_tag }}</code>
                                            </dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Name:</dt>
                                            <dd class="w-2/3 text-sm">{{ $asset->name }}</dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Category:</dt>
                                            <dd class="w-2/3">
                                                @if($asset->category)
                                                    <span class="inline-block px-2 py-1 text-xs font-bold text-white uppercase bg-gradient-to-tl from-blue-600 to-cyan-400 rounded-lg">{{ $asset->category->name }}</span>
                                                @else
                                                    <span class="text-slate-400">No Category</span>
                                                @endif
                                            </dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Model:</dt>
                                            <dd class="w-2/3 text-sm">{{ $asset->model ?? 'Not specified' }}</dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Serial Number:</dt>
                                            <dd class="w-2/3">
                                                @if($asset->serial_number)
                                                    <code class="px-2 py-1 text-sm bg-slate-100 rounded">{{ $asset->serial_number }}</code>
                                                @else
                                                    <span class="text-slate-400">Not specified</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                                
                                <div class="w-full max-w-full px-3 md:w-6/12 md:flex-none">
                                    <dl class="space-y-4">
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Vendor:</dt>
                                            <dd class="w-2/3">
                                                @if($asset->vendor)
                                                    <a href="{{ route('vendors.show', $asset->vendor) }}" class="text-blue-600 hover:text-blue-800 no-underline">
                                                        {{ $asset->vendor->name }}
                                                    </a>
                                                @else
                                                    <span class="text-slate-400">Not specified</span>
                                                @endif
                                            </dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Purchase Date:</dt>
                                            <dd class="w-2/3 text-sm">
                                                {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'Not specified' }}
                                            </dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Purchase Cost:</dt>
                                            <dd class="w-2/3">
                                                @if($asset->cost)
                                                    <strong class="text-green-600">₱{{ number_format($asset->cost, 2) }}</strong>
                                                @else
                                                    <span class="text-slate-400">Not specified</span>
                                                @endif
                                            </dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Assigned To:</dt>
                                            <dd class="w-2/3">
                                                @if($asset->assignedUser)
                                                    <a href="{{ route('users.show', $asset->assignedUser) }}" class="text-blue-600 hover:text-blue-800 no-underline">
                                                        <i class="fas fa-user mr-1"></i>
                                                        {{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}
                                                    </a>
                                                @else
                                                    <span class="text-slate-400">Unassigned</span>
                                                @endif
                                            </dd>
                                        </div>
                                        
                                        <div class="flex">
                                            <dt class="w-1/3 text-sm font-medium text-slate-600">Location:</dt>
                                            <dd class="w-2/3 text-sm">
                                                @if($asset->location)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $asset->location }}
                                                @else
                                                    <span class="text-slate-400">Not specified</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            
                            @if($asset->notes)
                                <hr class="my-6 border-slate-200">
                                <div class="w-full">
                                    <h6 class="mb-3 font-bold">Notes:</h6>
                                    <div class="p-4 bg-slate-50 rounded-lg">
                                        {{ $asset->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Asset History/Timeline -->
                <div class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 font-bold">Asset Timeline</h6>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-6">
                            <div class="relative pl-8">
                                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                                
                                <div class="relative mb-8">
                                    <div class="absolute left-[-23px] top-1 w-4 h-4 bg-gradient-to-tl from-green-600 to-lime-400 rounded-full border-3 border-white shadow-lg"></div>
                                    <div class="bg-slate-50 p-4 rounded-lg border-l-4 border-green-500">
                                        <h6 class="mb-1 font-semibold">Asset Created</h6>
                                        <p class="mb-2 text-sm text-slate-600">
                                            Asset was added to the system
                                        </p>
                                        <small class="text-slate-400">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $asset->created_at->format('M d, Y \a\t g:i A') }}
                                        </small>
                                    </div>
                                </div>
                                
                                @if($asset->updated_at != $asset->created_at)
                                    <div class="relative mb-8">
                                        <div class="absolute left-[-23px] top-1 w-4 h-4 bg-gradient-to-tl from-blue-600 to-cyan-400 rounded-full border-3 border-white shadow-lg"></div>
                                        <div class="bg-slate-50 p-4 rounded-lg border-l-4 border-blue-500">
                                            <h6 class="mb-1 font-semibold">Asset Updated</h6>
                                            <p class="mb-2 text-sm text-slate-600">
                                                Asset information was last modified
                                            </p>
                                            <small class="text-slate-400">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $asset->updated_at->format('M d, Y \a\t g:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="w-full max-w-full px-3 lg:w-4/12 lg:flex-none">
                <!-- Quick Actions -->
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 font-bold">Quick Actions</h6>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('assets.edit', $asset) }}" class="inline-block w-full px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                    <i class="fas fa-edit mr-2"></i>Edit Asset
                                </a>
                                
                                @if($asset->assigned_to)
                                    <button class="inline-block w-full px-6 py-3 text-xs font-bold text-center text-orange-500 uppercase align-middle transition-all bg-transparent border border-orange-500 border-solid rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs hover:bg-orange-500 hover:text-white" onclick="unassignAsset({{ $asset->id }})">
                                        <i class="fas fa-user-times mr-2"></i>Unassign User
                                    </button>
                                    <button class="inline-block w-full px-6 py-3 text-xs font-bold text-center text-blue-500 uppercase align-middle transition-all bg-transparent border border-blue-500 border-solid rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs hover:bg-blue-500 hover:text-white" data-bs-toggle="modal" data-bs-target="#reassignModal">
                                        <i class="fas fa-exchange-alt mr-2"></i>Reassign User
                                    </button>
                                @else
                                    <button class="inline-block w-full px-6 py-3 text-xs font-bold text-center text-green-500 uppercase align-middle transition-all bg-transparent border border-green-500 border-solid rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs hover:bg-green-500 hover:text-white" data-bs-toggle="modal" data-bs-target="#assignModal">
                                        <i class="fas fa-user-plus mr-2"></i>Assign User
                                    </button>
                                @endif
                                
                                <button class="inline-block w-full px-6 py-3 text-xs font-bold text-center text-blue-500 uppercase align-middle transition-all bg-transparent border border-blue-500 border-solid rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs hover:bg-blue-500 hover:text-white" onclick="printAssetLabel()">
                                    <i class="fas fa-print mr-2"></i>Print Label
                                </button>
                                
                                <hr class="my-4 border-slate-200">
                                
                                <form method="POST" action="{{ route('assets.destroy', $asset) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-block w-full px-6 py-3 text-xs font-bold text-center text-red-500 uppercase align-middle transition-all bg-transparent border border-red-500 border-solid rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs hover:bg-red-500 hover:text-white">
                                        <i class="fas fa-trash mr-2"></i>Delete Asset
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Asset Statistics -->
                <div class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 font-bold">Asset Statistics</h6>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-6">
                            <div class="flex text-center">
                                <div class="w-1/2">
                                    <div class="border-r border-slate-200">
                                        <h4 class="mb-0 font-bold text-blue-600">{{ $asset->created_at->diffInDays(now()) }}</h4>
                                        <small class="text-slate-400">Days Old</small>
                                    </div>
                                </div>
                                <div class="w-1/2">
                                    <h4 class="mb-0 font-bold text-green-600">
                                        @if($asset->cost)
                                        ₱{{ number_format($asset->cost, 0) }}
                                        @else
                                            N/A
                                        @endif
                                    </h4>
                                    <small class="text-slate-400">Value</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Information -->
                @if($asset->category || $asset->vendor || $asset->assignedUser)
                    <div class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                            <h6 class="mb-0 font-bold">Related Information</h6>
                        </div>
                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-6">
                                @if($asset->category)
                                    <div class="mb-3">
                                        <strong class="text-sm font-medium text-slate-600">Category:</strong>
                                        <a href="{{ route('assets.index', ['category' => $asset->category->id]) }}" class="block text-blue-600 hover:text-blue-800 no-underline">
                                            View all {{ $asset->category->name }} assets
                                        </a>
                                    </div>
                                @endif
                                
                                @if($asset->vendor)
                                    <div class="mb-3">
                                        <strong class="text-sm font-medium text-slate-600">Vendor:</strong>
                                        <a href="{{ route('vendors.show', $asset->vendor) }}" class="block text-blue-600 hover:text-blue-800 no-underline">
                                            View {{ $asset->vendor->name }} details
                                        </a>
                                    </div>
                                @endif
                                
                                @if($asset->assignedUser)
                                    <div class="mb-3">
                                        <strong class="text-sm font-medium text-slate-600">Assigned User:</strong>
                                        <a href="{{ route('users.show', $asset->assignedUser) }}" class="block text-blue-600 hover:text-blue-800 no-underline">
                                            View user profile
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Assign User Modal -->
    <div class="fixed inset-0 z-50 hidden overflow-y-auto" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative w-full max-w-md mx-auto bg-white rounded-2xl shadow-soft-xl">
                <div class="p-6 border-b border-slate-200">
                    <h5 class="mb-0 font-bold" id="assignModalLabel">Assign User to Asset</h5>
                    <button type="button" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="assignForm" method="POST" action="{{ route('assets.assign', $asset) }}">
                    @csrf
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="assigned_to" class="block mb-2 text-sm font-medium text-slate-600">Select User</label>
                            <select class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="assigned_to" name="assigned_to" required>
                                <option value="">Choose a user...</option>
                                @foreach(\App\Models\User::where('status', 'active')->orderBy('first_name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="assigned_date" class="block mb-2 text-sm font-medium text-slate-600">Assignment Date</label>
                            <input type="date" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="assigned_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block mb-2 text-sm font-medium text-slate-600">Notes (Optional)</label>
                            <textarea class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="notes" name="notes" rows="3" placeholder="Any additional notes about this assignment..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 p-6 border-t border-slate-200">
                        <button type="button" class="px-6 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="inline-block px-6 py-2 text-sm font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-green-600 to-lime-400 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                            <i class="fas fa-user-plus mr-2"></i>Assign User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reassign User Modal -->
    <div class="fixed inset-0 z-50 hidden overflow-y-auto" id="reassignModal" tabindex="-1" aria-labelledby="reassignModalLabel" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative w-full max-w-md mx-auto bg-white rounded-2xl shadow-soft-xl">
                <div class="p-6 border-b border-slate-200">
                    <h5 class="mb-0 font-bold" id="reassignModalLabel">Reassign Asset to Another User</h5>
                    <button type="button" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="reassignForm" method="POST" action="{{ route('assets.reassign', $asset) }}">
                    @csrf
                    <div class="p-6">
                        <div class="p-4 mb-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Currently assigned to: <strong>{{ $asset->assignedUser ? $asset->assignedUser->first_name . ' ' . $asset->assignedUser->last_name : 'Unassigned' }}</strong>
                        </div>
                        
                        <div class="mb-4">
                            <label for="user_search_reassign" class="block mb-2 text-sm font-medium text-slate-600">Search Users</label>
                            <input type="text" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="user_search_reassign" placeholder="Search by name or department..." onkeyup="filterReassignUsers()">
                        </div>
                        
                        <div class="mb-4">
                            <label for="new_assigned_to" class="block mb-2 text-sm font-medium text-slate-600">Select New User</label>
                            <select class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="new_assigned_to" name="new_assigned_to" required>
                                <option value="">Choose a user...</option>
                                @foreach(\App\Models\User::where('status', 'active')->orderBy('first_name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->department->name ?? 'No Department' }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="reassign_date" class="block mb-2 text-sm font-medium text-slate-600">Assignment Date</label>
                            <input type="date" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="reassign_date" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="reassign_notes" class="block mb-2 text-sm font-medium text-slate-600">Notes (Optional)</label>
                            <textarea class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="reassign_notes" name="notes" rows="3" placeholder="Reason for reassignment or additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 p-6 border-t border-slate-200">
                        <button type="button" class="px-6 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="inline-block px-6 py-2 text-sm font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-violet-600 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                            <i class="fas fa-exchange-alt mr-2"></i>Reassign Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function unassignAsset(assetId) {
            if (confirm('Are you sure you want to unassign this asset?')) {
                // Add your unassign logic here
                console.log('Unassigning asset:', assetId);
            }
        }

        function printAssetLabel() {
            window.print();
        }

        function filterReassignUsers() {
            const searchTerm = document.getElementById('user_search_reassign').value.toLowerCase();
            const select = document.getElementById('new_assigned_to');
            const options = select.getElementsByTagName('option');
            
            for (let i = 1; i < options.length; i++) {
                const optionText = options[i].textContent.toLowerCase();
                if (optionText.includes(searchTerm)) {
                    options[i].style.display = '';
                } else {
                    options[i].style.display = 'none';
                }
            }
        }
    </script>
</x-layouts.base>