@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Monitors</h4>
                    <div class="d-flex gap-2">
                        <div class="btn-group" role="group">
                            <a href="{{ route('import-export.template', 'monitors') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i>Template
                            </a>
                            <a href="{{ route('import-export.export', 'monitors') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-export me-1"></i>Export
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import me-1"></i>Import
                            </button>
                        </div>
                        <a href="{{ route('monitors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Monitor
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('monitors.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by asset name, tag, size, or resolution..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="panel_type" class="form-control">
                                        <option value="">All Panel Types</option>
                                        <option value="LCD" {{ request('panel_type') == 'LCD' ? 'selected' : '' }}>LCD</option>
                                        <option value="LED" {{ request('panel_type') == 'LED' ? 'selected' : '' }}>LED</option>
                                        <option value="OLED" {{ request('panel_type') == 'OLED' ? 'selected' : '' }}>OLED</option>
                                        <option value="CRT" {{ request('panel_type') == 'CRT' ? 'selected' : '' }}>CRT</option>
                                        <option value="Plasma" {{ request('panel_type') == 'Plasma' ? 'selected' : '' }}>Plasma</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Monitors Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Asset Name</th>
                                    <th>Size</th>
                                    <th>Resolution</th>
                                    <th>Panel Type</th>
                                    <th>Status</th>
                                    <th>Movement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monitors as $monitor)
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary">{{ $monitor->asset->asset_tag }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $monitor->asset->name }}</strong>
                                            @if($monitor->asset->assignedUser)
                                                <br><small class="text-muted">Assigned to: {{ $monitor->asset->assignedUser->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-tv"></i> {{ $monitor->size }}
                                        </td>
                                        <td>{{ $monitor->resolution }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $monitor->panel_type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $monitor->asset->status == 'Available' ? 'success' : ($monitor->asset->status == 'In Use' ? 'primary' : 'warning') }}">
                                                {{ $monitor->asset->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $monitor->asset->movement }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('monitors.edit', $monitor) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('monitors.destroy', $monitor) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this monitor?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-tv fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No monitors found.</p>
                                            <a href="{{ route('monitors.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add First Monitor
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $monitors->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Monitors
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'monitors') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'monitors') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status, size, resolution, panel_type, refresh_rate</li>
                            <li>Category and vendor must exist in the system</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Monitors
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection