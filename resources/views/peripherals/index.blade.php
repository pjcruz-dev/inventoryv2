@extends('x-layouts.base')

@section('title', 'Peripherals Management')

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
                        <span class="ml-1 text-gray-500 md:ml-2">Peripherals</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between mb-6">
            <div class="flex flex-col">
                <h1 class="mb-0 font-bold text-transparent text-3xl bg-gradient-to-tl from-blue-600 to-cyan-400 bg-clip-text">
                    <i class="fas fa-mouse mr-3"></i>Peripherals Management
                </h1>
                <p class="mb-0 text-sm leading-normal text-gray-500">Manage and track all peripheral assets in your inventory</p>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ route('import-export.template', 'peripherals') }}" class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                    <i class="fas fa-download mr-2"></i>Template
                </a>
                <a href="{{ route('import-export.export', 'peripherals') }}" class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-green-600 to-lime-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                    <i class="fas fa-file-export mr-2"></i>Export
                </a>
                <button type="button" class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-orange-500 to-yellow-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import mr-2"></i>Import
                </button>
                <a href="{{ route('peripherals.create') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                    <i class="fas fa-plus mr-2"></i>Add New Peripheral
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full max-w-full px-3">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <h6 class="mb-0 text-slate-700">Search & Filters</h6>
                    </div>
                    <div class="flex-auto px-6 pt-0 pb-6">
                        <form method="GET" action="{{ route('peripherals.index') }}" class="mt-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-full max-w-full px-3 md:w-4/12 md:flex-none">
                                    <div class="mb-4">
                                        <label for="search" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Search</label>
                                        <input type="text" name="search" id="search" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow" 
                                               placeholder="Search by asset tag, name, type..." 
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="w-full max-w-full px-3 md:w-3/12 md:flex-none">
                                    <div class="mb-4">
                                        <label for="type" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Type</label>
                                        <select name="type" id="type" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow">
                                            <option value="">All Types</option>
                                            <option value="Mouse" {{ request('type') == 'Mouse' ? 'selected' : '' }}>Mouse</option>
                                            <option value="Keyboard" {{ request('type') == 'Keyboard' ? 'selected' : '' }}>Keyboard</option>
                                            <option value="Webcam" {{ request('type') == 'Webcam' ? 'selected' : '' }}>Webcam</option>
                                            <option value="Headset" {{ request('type') == 'Headset' ? 'selected' : '' }}>Headset</option>
                                            <option value="Speaker" {{ request('type') == 'Speaker' ? 'selected' : '' }}>Speaker</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-full max-w-full px-3 md:w-3/12 md:flex-none">
                                    <div class="mb-4">
                                        <label for="interface" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Interface</label>
                                        <select name="interface" id="interface" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow">
                                            <option value="">All Interfaces</option>
                                            <option value="USB" {{ request('interface') == 'USB' ? 'selected' : '' }}>USB</option>
                                            <option value="Wireless" {{ request('interface') == 'Wireless' ? 'selected' : '' }}>Wireless</option>
                                            <option value="Bluetooth" {{ request('interface') == 'Bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                                            <option value="PS/2" {{ request('interface') == 'PS/2' ? 'selected' : '' }}>PS/2</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-full max-w-full px-3 md:w-2/12 md:flex-none">
                                    <div class="mb-4">
                                        <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">&nbsp;</label>
                                        <div class="flex space-x-2">
                                            <button type="submit" class="inline-block px-6 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <a href="{{ route('peripherals.index') }}" class="inline-block px-6 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-gray-600 to-gray-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peripherals Table -->
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-full max-w-full px-3">
                <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                        <div class="flex flex-wrap items-center justify-between">
                            <h6 class="mb-0 text-slate-700">Peripherals List</h6>
                            <div class="text-sm text-slate-500">
                                Total: {{ $peripherals->total() }} peripherals
                            </div>
                        </div>
                    </div>
                    <div class="flex-auto px-0 pt-0 pb-2">
                        @if($peripherals->count() > 0)
                        <div class="p-0 overflow-x-auto">
                            <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500">
                                <thead class="align-bottom">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Asset Tag</th>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Name</th>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Type</th>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Interface</th>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Status</th>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Assigned To</th>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peripherals as $peripheral)
                                    <tr>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <div class="flex px-2 py-1">
                                                <div class="flex flex-col justify-center">
                                                    <h6 class="mb-0 text-sm leading-normal text-slate-700">{{ $peripheral->asset->asset_tag }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <div class="flex px-2 py-1">
                                                <div class="flex flex-col justify-center">
                                                    <h6 class="mb-0 text-sm leading-normal text-slate-700">{{ $peripheral->asset->name }}</h6>
                                                    <p class="mb-0 text-xs leading-tight text-slate-400">{{ $peripheral->asset->description }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="inline-block px-2 py-1 font-semibold text-xs leading-tight text-center text-white uppercase bg-gradient-to-tl from-purple-700 to-pink-500 rounded-lg">
                                                {{ $peripheral->type }}
                                            </span>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <span class="inline-block px-2 py-1 font-semibold text-xs leading-tight text-center text-white uppercase bg-gradient-to-tl from-blue-600 to-cyan-400 rounded-lg">
                                                {{ $peripheral->interface }}
                                            </span>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            @if($peripheral->asset->status == 'active')
                                                <span class="inline-block px-2 py-1 font-semibold text-xs leading-tight text-center text-white uppercase bg-gradient-to-tl from-green-600 to-lime-400 rounded-lg">Active</span>
                                            @elseif($peripheral->asset->status == 'deployed')
                                                <span class="inline-block px-2 py-1 font-semibold text-xs leading-tight text-center text-white uppercase bg-gradient-to-tl from-blue-600 to-cyan-400 rounded-lg">Deployed</span>
                                            @elseif($peripheral->asset->status == 'maintenance')
                                                <span class="inline-block px-2 py-1 font-semibold text-xs leading-tight text-center text-white uppercase bg-gradient-to-tl from-orange-500 to-yellow-500 rounded-lg">Maintenance</span>
                                            @else
                                                <span class="inline-block px-2 py-1 font-semibold text-xs leading-tight text-center text-white uppercase bg-gradient-to-tl from-red-600 to-rose-400 rounded-lg">{{ ucfirst($peripheral->asset->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            @if($peripheral->asset->assignedUser)
                                                <div class="flex px-2 py-1">
                                                    <div class="flex flex-col justify-center">
                                                        <h6 class="mb-0 text-sm leading-normal text-slate-700">{{ $peripheral->asset->assignedUser->first_name }} {{ $peripheral->asset->assignedUser->last_name }}</h6>
                                                        <p class="mb-0 text-xs leading-tight text-slate-400">{{ $peripheral->asset->assignedUser->email }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('peripherals.show', $peripheral) }}" class="inline-block px-4 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('peripherals.edit', $peripheral) }}" class="inline-block px-4 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-orange-500 to-yellow-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('peripherals.destroy', $peripheral) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this peripheral?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-red-600 to-rose-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs" title="Delete">
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
                        @else
                        <div class="p-6 text-center">
                            <div class="flex flex-col items-center justify-center py-12">
                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gradient-to-tl from-purple-700 to-pink-500 rounded-full">
                                    <i class="fas fa-mouse text-white text-2xl"></i>
                                </div>
                                <h3 class="mb-2 text-xl font-semibold text-slate-700">No Peripherals Found</h3>
                                <p class="mb-6 text-slate-500">Get started by adding your first peripheral to the inventory.</p>
                                <a href="{{ route('peripherals.create') }}" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                                    <i class="fas fa-plus mr-2"></i>Add First Peripheral
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($peripherals->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $peripherals->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="modal-header p-6 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                <h5 class="modal-title font-bold text-slate-700" id="importModalLabel">
                    <i class="fas fa-file-import mr-2"></i>Import Peripherals
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'peripherals') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-6">
                    <div class="mb-4">
                        <label for="file" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700">Choose CSV File</label>
                        <input type="file" name="file" id="file" accept=".csv" required class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow">
                        <small class="text-slate-400">Please upload a CSV file with the correct format. <a href="{{ route('import-export.template', 'peripherals') }}" class="text-blue-500 hover:text-blue-700">Download template</a></small>
                    </div>
                </div>
                <div class="modal-footer flex flex-wrap items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b-2xl">
                    <button type="button" class="inline-block px-6 py-3 mr-3 font-bold text-center text-slate-500 uppercase align-middle transition-all rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85 hover:shadow-soft-xs">
                        <i class="fas fa-upload mr-2"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection