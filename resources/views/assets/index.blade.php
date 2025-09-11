@extends('layouts.app')

@section('title', 'Assets')
@section('page-title', 'Assets Management')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ route('import-export.template', 'assets') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Template
            </a>
            <a href="{{ route('import-export.export', 'assets') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-file-export me-1"></i>Export
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i>Import
            </button>
        </div>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Asset
        </a>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Assets</h5>
                <small class="text-muted">{{ $assets->total() }} total assets</small>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i>Filters
                    @if(request()->hasAny(['search', 'category', 'status', 'movement', 'assignment']))
                        <span class="badge bg-primary ms-1">{{ collect(request()->only(['search', 'category', 'status', 'movement', 'assignment']))->filter()->count() }}</span>
                    @endif
                </button>
            </div>
        </div>
        
        <!-- Advanced Filters -->
        <div class="collapse mt-3" id="filterCollapse">
            <div class="card card-body bg-light">
                <form method="GET" action="{{ route('assets.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search assets..." value="{{ request('search') }}">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Category</label>
                            <div class="dropdown">
                                <input type="text" 
                                       class="form-control dropdown-toggle" 
                                       id="categorySearch" 
                                       placeholder="Search categories..." 
                                       data-bs-toggle="dropdown" 
                                       autocomplete="off"
                                       value="{{ $categories->where('id', request('category'))->first()->name ?? '' }}">
                                <input type="hidden" name="category" id="categoryValue" value="{{ request('category') }}">
                                <ul class="dropdown-menu w-100" id="categoryDropdown">
                                    <li><a class="dropdown-item" href="#" data-value="">All Categories</a></li>
                                    @foreach($categories as $category)
                                        <li><a class="dropdown-item {{ request('category') == $category->id ? 'active' : '' }}" 
                                               href="#" 
                                               data-value="{{ $category->id }}">{{ $category->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ str_replace('Deployed Tagged', 'Deployed', $status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Movement Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Movement</label>
                            <select name="movement" class="form-select">
                                <option value="">All Movements</option>
                                @foreach($movements as $movement)
                                    <option value="{{ $movement }}" {{ request('movement') == $movement ? 'selected' : '' }}>
                                        {{ str_replace('Deployed Tagged', 'Deployed', $movement) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Assignment Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Assignment</label>
                            <select name="assignment" class="form-select">
                                <option value="">All Assets</option>
                                <option value="assigned" {{ request('assignment') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="unassigned" {{ request('assignment') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                            </select>
                        </div>
                        
                        <!-- Filter Actions -->
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($assets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="fw-semibold">Asset Tag</th>
                            <th class="fw-semibold">Name</th>
                            <th class="fw-semibold">Category</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold">Assigned To</th>
                            <th class="fw-semibold">Location</th>
                            <th class="fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr class="border-bottom">
                            <td class="fw-bold text-primary">{{ $asset->asset_tag }}</td>
                            <td>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $asset->name }}</div>
                                    @if($asset->model)
                                        <small class="text-muted">{{ $asset->model }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($asset->category)
                                    <span class="badge badge-enhanced bg-info px-2 py-1">{{ $asset->category->name }}</span>
                                @else
                                    <span class="text-muted fst-italic">No Category</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge badge-enhanced bg-{{ 
                                        $asset->status === 'Active' ? 'success' : 
                                        ($asset->status === 'Inactive' ? 'danger' : 
                                        ($asset->status === 'Under Maintenance' ? 'warning' : 
                                        ($asset->status === 'Issue Reported' ? 'danger' : 
                                        ($asset->status === 'Pending Confirmation' ? 'info' : 
                                        ($asset->status === 'Disposed' ? 'dark' : 'secondary')))))
                                    }} px-2 py-1">
                                        {{ $asset->status }}
                                    </span>
                                    <small class="text-muted">{{ str_replace('Deployed Tagged', 'Deployed', $asset->movement ?? 'New Arrival') }}</small>
                                </div>
                            </td>
                            <td>
                                @if($asset->assigned_to)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $asset->assignedUser->first_name ?? 'Unknown' }} {{ $asset->assignedUser->last_name ?? '' }}</div>
                                            <small class="text-muted">{{ $asset->assignedUser->employee_id ?? '' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->location)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                                        <span class="fw-medium">{{ $asset->location }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">No Location</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="View Asset" style="width: 32px; height: 32px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center" title="Edit Asset" style="width: 32px; height: 32px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" title="Delete Asset" style="width: 32px; height: 32px;">
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
            @if($assets->hasPages())
                <div class="pagination-wrapper">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            Showing {{ $assets->firstItem() }} to {{ $assets->lastItem() }} of {{ $assets->total() }} assets
                        </div>
                        <div>
                            {{ $assets->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-muted">No Assets Found</h5>
                <p class="text-muted">Get started by creating your first asset.</p>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Asset
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Assets
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'assets') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'assets') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status</li>
                            <li>Category and vendor must exist in the system</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Assets
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Category search functionality
    const categorySearch = $('#categorySearch');
    const categoryValue = $('#categoryValue');
    const categoryDropdown = $('#categoryDropdown');
    
    // Filter dropdown items based on search input
    categorySearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        categoryDropdown.find('li').each(function() {
            const text = $(this).find('a').text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Show dropdown if not already visible
        if (!categoryDropdown.hasClass('show')) {
            categoryDropdown.addClass('show');
        }
    });
    
    // Handle dropdown item selection
    categoryDropdown.on('click', 'a.dropdown-item', function(e) {
        e.preventDefault();
        
        const value = $(this).data('value');
        const text = $(this).text();
        
        categoryValue.val(value);
        categorySearch.val(value ? text : '');
        
        // Update active state
        categoryDropdown.find('a.dropdown-item').removeClass('active');
        $(this).addClass('active');
        
        // Hide dropdown
        categoryDropdown.removeClass('show');
        
        // Auto-submit form for immediate filtering
        $('#filterForm').submit();
    });
    
    // Show all items when dropdown is opened
    categorySearch.on('focus', function() {
        categoryDropdown.find('li').show();
    });
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            categoryDropdown.removeClass('show');
        }
    });
    
    // Clear search when clicking the clear button
    categorySearch.on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            categoryValue.val('');
            categoryDropdown.find('a.dropdown-item').removeClass('active');
            categoryDropdown.find('a[data-value=""]').addClass('active');
            categoryDropdown.removeClass('show');
        }
    });
});
</script>
@endsection