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
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('assets.index') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search assets..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($assets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Asset Tag</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr>
                            <td>
                                <strong>{{ $asset->asset_tag }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $asset->name }}</strong>
                                    @if($asset->model)
                                        <br><small class="text-muted">{{ $asset->model }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($asset->category)
                                    <span class="badge bg-info">{{ $asset->category->name }}</span>
                                @else
                                    <span class="text-muted">No Category</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $asset->status === 'active' ? 'success' : 
                                    ($asset->status === 'deployed' ? 'primary' : 
                                    ($asset->status === 'inactive' ? 'danger' : 
                                    ($asset->status === 'problematic' ? 'danger' : 
                                    ($asset->status === 'disposed' ? 'dark' : 
                                    ($asset->status === 'maintenance' ? 'warning' : 
                                    ($asset->status === 'pending_confirm' ? 'info' : 
                                    ($asset->status === 'returned' ? 'secondary' : 
                                    ($asset->status === 'new_arrived' ? 'success' : 'warning'))))))))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($asset->assigned_to)
                                    {{ $asset->assignedUser->first_name ?? 'Unknown' }} {{ $asset->assignedUser->last_name ?? '' }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                {{ $asset->location ?? 'Not specified' }}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this asset?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
            <div class="d-flex justify-content-center">
                {{ $assets->links() }}
            </div>
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
    // Add any asset-specific JavaScript here
</script>
@endsection