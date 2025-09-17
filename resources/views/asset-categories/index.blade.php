@extends('layouts.app')

@section('title', 'Asset Categories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tags me-2"></i>Asset Categories
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('asset-categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Category
                        </a>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i>Import/Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('asset-categories.export') }}">
                                    <i class="fas fa-file-excel me-2"></i>Export to Excel
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('asset-categories.import-form') }}">
                                    <i class="fas fa-file-import me-2"></i>Import from Excel
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('asset-categories.download-template') }}">
                                    <i class="fas fa-file-download me-2"></i>Download Template
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('asset-categories.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search categories..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('asset-categories.index') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Categories Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Assets Count</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            {{ Str::limit($category->description, 50) ?: 'No description' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->assets_count }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $category->created_at->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('view_asset_categories')
                                                <a href="{{ route('asset-categories.show', $category) }}" 
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_asset_categories')
                                                <a href="{{ route('asset-categories.edit', $category) }}" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete_asset_categories')
                                                @if($category->assets_count == 0)
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            title="Delete" data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $category->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            title="Cannot delete - has assets" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    @if($category->assets_count == 0)
                                        <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the category <strong>{{ $category->name }}</strong>?</p>
                                                        <p class="text-muted">This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('asset-categories.destroy', $category) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tags fa-3x mb-3"></i>
                                                <h5>No Categories Found</h5>
                                                <p>{{ request('search') ? 'No categories match your search criteria.' : 'No asset categories have been created yet.' }}</p>
                                                @if(!request('search'))
                                                    <a href="{{ route('asset-categories.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus me-1"></i>Create First Category
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($categories->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $categories->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush