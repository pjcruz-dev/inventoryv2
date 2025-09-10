@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Computers</h4>
                    <div class="d-flex gap-2">
                        <div class="btn-group" role="group">
                            <a href="{{ route('import-export.template', 'computers') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i>Template
                            </a>
                            <a href="{{ route('import-export.export', 'computers') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-export me-1"></i>Export
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import me-1"></i>Import
                            </button>
                        </div>
                        <a href="{{ route('computers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Computer
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('computers.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by asset name, tag, processor, or memory..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="processor" class="form-control">
                                        <option value="">All Processors</option>
                                        <option value="Intel" {{ request('processor') == 'Intel' ? 'selected' : '' }}>Intel</option>
                                        <option value="AMD" {{ request('processor') == 'AMD' ? 'selected' : '' }}>AMD</option>
                                        <option value="Apple" {{ request('processor') == 'Apple' ? 'selected' : '' }}>Apple</option>
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

                    <!-- Computers Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Asset Name</th>
                                    <th>Processor</th>
                                    <th>Memory</th>
                                    <th>Storage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($computers as $computer)
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary">{{ $computer->asset->asset_tag }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $computer->asset->name }}</strong>
                                            @if($computer->asset->user)
                                                <br><small class="text-muted">Assigned to: {{ $computer->asset->user->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $computer->processor }}</td>
                                        <td>{{ $computer->memory }}</td>
                                        <td>{{ $computer->storage }}</td>
                                        <td>
                                            <span class="badge badge-{{ $computer->asset->status == 'Available' ? 'success' : ($computer->asset->status == 'In Use' ? 'primary' : 'warning') }}">
                                                {{ $computer->asset->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('computers.show', $computer) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('computers.edit', $computer) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('computers.destroy', $computer) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this computer?')">
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
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-desktop fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No computers found.</p>
                                            <a href="{{ route('computers.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add First Computer
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $computers->appends(request()->query())->links() }}
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
                    <i class="fas fa-file-import me-2"></i>Import Computers
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'computers') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'computers') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status, processor, ram, storage, os</li>
                            <li>Category and vendor must exist in the system</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Computers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection