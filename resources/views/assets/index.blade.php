<x-layouts.base>
    <div class="w-full px-6 py-6 mx-auto">
        <!-- Page Header -->
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-full max-w-full px-3">
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div>
                        <h6 class="mb-0 font-bold capitalize">Assets Management</h6>
                        <p class="mb-0 text-sm leading-normal text-slate-400">Manage your organization's assets</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('import-export.template', 'assets') }}" class="inline-block px-4 py-2 text-xs font-bold text-center text-green-600 uppercase align-middle transition-all bg-transparent border border-green-600 rounded-lg cursor-pointer leading-pro ease-soft-in hover:scale-102 hover:shadow-soft-xs active:opacity-85">
                                <i class="fas fa-download mr-1"></i>Template
                            </a>
                            <a href="{{ route('import-export.export', 'assets') }}" class="inline-block px-4 py-2 text-xs font-bold text-center text-blue-600 uppercase align-middle transition-all bg-transparent border border-blue-600 rounded-lg cursor-pointer leading-pro ease-soft-in hover:scale-102 hover:shadow-soft-xs active:opacity-85">
                                <i class="fas fa-file-export mr-1"></i>Export
                            </a>
                            <button type="button" class="inline-block px-4 py-2 text-xs font-bold text-center text-orange-600 uppercase align-middle transition-all bg-transparent border border-orange-600 rounded-lg cursor-pointer leading-pro ease-soft-in hover:scale-102 hover:shadow-soft-xs active:opacity-85" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import mr-1"></i>Import
                            </button>
                        </div>
                        <a href="{{ route('assets.create') }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all bg-gradient-fuchsia rounded-lg cursor-pointer leading-pro ease-soft-in hover:scale-102 hover:shadow-soft-xs active:opacity-85">
                            <i class="fas fa-plus mr-2"></i>Add New Asset
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Search and Filters -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="flex-none w-full max-w-full px-3">
                <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <div class="flex flex-wrap items-center justify-between">
                            <div>
                                <h6 class="mb-0 font-bold">All Assets</h6>
                                <p class="mb-0 text-sm leading-normal text-slate-400">Manage and track your organization's assets</p>
                            </div>
                            <div class="relative max-w-sm">
                                <form method="GET" action="{{ route('assets.index') }}" class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-search text-slate-400"></i>
                                    </div>
                                    <input type="text" name="search" class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search assets, tags, categories..." value="{{ request('search') }}">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assets Table -->
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-full max-w-full px-3">
                <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0">Assets Table</h6>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        @if($assets->count() > 0)
                            <div class="p-0 overflow-x-auto">
                                <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                                    <thead class="align-bottom">
                                        <tr>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-tag mr-2"></i>Asset Tag</th>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-cube mr-2"></i>Name</th>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-folder mr-2"></i>Category</th>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-circle mr-2"></i>Status</th>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-user mr-2"></i>Assigned To</th>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-map-marker-alt mr-2"></i>Location</th>
                                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"><i class="fas fa-cogs mr-2"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assets as $asset)
                                        <tr>
                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <div class="flex px-2 py-1">
                                                    <div class="flex flex-col justify-center">
                                                        <h6 class="mb-0 text-sm leading-normal">{{ $asset->asset_tag }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <div class="flex px-2 py-1">
                                                    <div class="flex flex-col justify-center">
                                                        <h6 class="mb-0 text-sm leading-normal">{{ $asset->name }}</h6>
                                                        @if($asset->model)
                                                            <p class="mb-0 text-xs leading-tight text-slate-400">{{ $asset->model }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                @if($asset->category)
                                                    <span class="bg-gradient-to-tl from-blue-600 to-cyan-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                                        <i class="fas fa-folder mr-1"></i>
                                                        {{ $asset->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400"><i class="fas fa-question-circle mr-1"></i>No Category</span>
                                                @endif
                                            </td>
                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <span class="bg-gradient-to-tl {{ 
                                                    $asset->status === 'active' ? 'from-green-600 to-lime-400' : 
                                                    ($asset->status === 'deployed' ? 'from-blue-600 to-cyan-400' : 
                                                    ($asset->status === 'inactive' ? 'from-red-600 to-rose-400' : 
                                                    ($asset->status === 'problematic' ? 'from-red-600 to-rose-400' : 
                                                    ($asset->status === 'disposed' ? 'from-slate-600 to-slate-400' : 
                                                    ($asset->status === 'maintenance' ? 'from-orange-500 to-yellow-500' : 
                                                    ($asset->status === 'pending_confirm' ? 'from-blue-600 to-cyan-400' : 
                                                    ($asset->status === 'returned' ? 'from-slate-600 to-slate-400' : 
                                                    ($asset->status === 'new_arrived' ? 'from-green-600 to-lime-400' : 'from-orange-500 to-yellow-500')))))))))
                                                }} px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                                    <i class="fas fa-{{ 
                                                        $asset->status === 'active' ? 'check-circle' : 
                                                        ($asset->status === 'deployed' ? 'rocket' : 
                                                        ($asset->status === 'inactive' ? 'times-circle' : 
                                                        ($asset->status === 'problematic' ? 'exclamation-triangle' : 
                                                        ($asset->status === 'disposed' ? 'trash' : 
                                                        ($asset->status === 'maintenance' ? 'wrench' : 
                                                        ($asset->status === 'pending_confirm' ? 'clock' : 
                                                        ($asset->status === 'returned' ? 'undo' : 
                                                        ($asset->status === 'new_arrived' ? 'star' : 'question-circle')))))))))
                                                    }} mr-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                                </span>
                                            </td>
                                             <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                 <div class="flex px-2 py-1">
                                                     <div class="flex flex-col justify-center">
                                                         @if($asset->assigned_to)
                                                             <h6 class="mb-0 text-sm leading-normal">{{ $asset->assignedUser->first_name ?? 'Unknown' }} {{ $asset->assignedUser->last_name ?? '' }}</h6>
                                                         @else
                                                             <span class="text-slate-400">Unassigned</span>
                                                         @endif
                                                     </div>
                                                 </div>
                                             </td>
                                             <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                 <div class="flex px-2 py-1">
                                                     <div class="flex flex-col justify-center">
                                                         <h6 class="mb-0 text-sm leading-normal">{{ $asset->location ?? 'Not specified' }}</h6>
                                                     </div>
                                                 </div>
                                             </td>
                                             <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                 <div class="flex space-x-2">
                                                     <a href="{{ route('assets.show', $asset) }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" title="View Details">
                                                         <i class="fas fa-eye"></i>
                                                     </a>
                                                     <a href="{{ route('assets.edit', $asset) }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" title="Edit Asset">
                                                         <i class="fas fa-edit"></i>
                                                     </a>
                                                     <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="inline" onsubmit="return confirm('⚠️ Are you sure you want to delete this asset? This action cannot be undone.')">
                                                         @csrf
                                                         @method('DELETE')
                                                         <button type="submit" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-red-600 to-rose-400 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" title="Delete Asset">
                                                             <i class="fas fa-trash"></i>
                                                         </button>
                                                     </form>
                                                 </div>
                                             </td>
                                         </tr>
                                         @endforeach
                                     </tbody>
                                 </table>
                             </div>
                             
                             <!-- Pagination -->
                             <div class="flex justify-center mt-6">
                                 {{ $assets->links() }}
                             </div>
                         @else
                             <div class="p-16 text-center">
                                 <div class="text-6xl text-slate-400 mb-6 opacity-60">
                                     <i class="fas fa-boxes"></i>
                                 </div>
                                 <h4 class="text-slate-400 mb-3 text-lg font-semibold">No Assets Found</h4>
                                 @if(request('search'))
                                     <p class="text-slate-400 mb-4">No assets match your search criteria "<strong>{{ request('search') }}</strong>"</p>
                                     <div class="flex gap-2 justify-center">
                                         <a href="{{ route('assets.index') }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-slate-700 uppercase align-middle transition-all border border-gray-200 rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft bg-white hover:scale-102 active:opacity-85">
                                             <i class="fas fa-times mr-2"></i>Clear Search
                                         </a>
                                         <a href="{{ route('assets.create') }}" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                             <i class="fas fa-plus mr-2"></i>Add New Asset
                                         </a>
                                     </div>
                                 @else
                                     <p class="text-slate-400 mb-4">Get started by creating your first asset to begin tracking your organization's inventory.</p>
                                     <a href="{{ route('assets.create') }}" class="inline-block px-8 py-4 text-sm font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                         <i class="fas fa-plus mr-2"></i>Add Your First Asset
                                     </a>
                                 @endif
                             </div>
                         @endif
                     </div>
                 </div>
             </div>
         </div>

<!-- Import Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="importModal" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white p-6 rounded-t-2xl -m-6 mb-6">
                <h3 class="text-lg font-medium leading-6 text-white" id="importModalLabel">
                    <i class="fas fa-file-import mr-2"></i>Import Assets
                </h3>
                <button type="button" class="absolute top-4 right-4 text-white hover:text-gray-200" onclick="document.getElementById('importModal').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('import-export.import', 'assets') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-6">
                    <div class="mb-6">
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="csv_file" name="csv_file" accept=".csv" required>
                        <p class="mt-2 text-sm text-gray-500">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'assets') }}" class="text-blue-600 hover:text-blue-500">
                                Download template
                            </a> if you need the correct format.
                        </p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h6 class="text-blue-800 font-semibold mb-2"><i class="fas fa-info-circle mr-2"></i>Import Guidelines:</h6>
                        <ul class="text-blue-700 text-sm space-y-1">
                            <li>• CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status</li>
                            <li>• Category and vendor must exist in the system</li>
                            <li>• Asset tags must be unique</li>
                            <li>• Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" class="inline-block px-6 py-3 text-xs font-bold text-center text-slate-700 uppercase align-middle transition-all border border-gray-200 rounded-lg cursor-pointer leading-pro ease-soft-in tracking-tight-soft bg-white hover:scale-102 active:opacity-85" onclick="document.getElementById('importModal').classList.add('hidden')">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="inline-block px-6 py-3 text-xs font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-orange-500 to-yellow-500 leading-pro ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                        <i class="fas fa-file-import mr-2"></i>Import Assets
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any asset-specific JavaScript here
</script>
@endsection