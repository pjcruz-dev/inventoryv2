@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Computers</h5>
                            <small class="text-white-50">{{ $computers->total() }} total computers</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                @can('create_computers')
                                <a href="{{ route('computers.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus me-1"></i>Add Computer
                                </a>
                                <a href="{{ route('computers.bulk-create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-layer-group me-1"></i>Bulk Create
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('computers.index') }}" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search by asset name, tag, processor, or memory..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                                        <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Computers Table -->
            <!-- Skeleton Loading State -->
            <div id="skeleton-loading" class="d-none">
                    <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="fw-semibold">Asset Tag</th>
                                <th class="fw-semibold">Asset Name</th>
                                <th class="fw-semibold">Processor</th>
                                <th class="fw-semibold">Memory</th>
                                <th class="fw-semibold">Storage</th>
                                <th class="fw-semibold">Status</th>
                                <th class="fw-semibold">Movement</th>
                                <th class="fw-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 5; $i++)
                            <tr class="border-bottom">
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text medium"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text long"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text medium"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <div class="skeleton skeleton-button" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                        <div class="skeleton skeleton-button" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                        <div class="skeleton skeleton-button" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-responsive" id="main-table">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-semibold">Asset Tag</th>
                                    <th class="fw-semibold">Asset Name</th>
                                    <th class="fw-semibold">Processor</th>
                                    <th class="fw-semibold">Memory</th>
                                    <th class="fw-semibold">Storage</th>
                                    <th class="fw-semibold">Status</th>
                                    <th class="fw-semibold">Movement</th>
                                    <th class="fw-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                    <tbody id="computers-table-body">
                                @forelse($computers as $computer)
                                    <tr class="border-bottom">
                                        <td>
                                            <span class="badge bg-secondary text-white fw-bold px-2 py-1">{{ $computer->asset->asset_tag }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $computer->asset->name }}</div>
                                            @if($computer->asset->user)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-user me-1"></i>{{ $computer->asset->user->name }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $computer->processor }}</td>
                                        <td class="text-muted">{{ $computer->memory }}</td>
                                        <td class="text-muted">{{ $computer->storage }}</td>
                                        <td>
                                            <span class="badge bg-{{ $computer->asset->status == 'Available' ? 'success' : ($computer->asset->status == 'In Use' ? 'primary' : 'warning') }} px-2 py-1">
                                                {{ $computer->asset->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white px-2 py-1">
                                                {{ str_replace('Deployed Tagged', 'Deployed', $computer->asset->movement) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('view_computers')
                                                <a href="{{ route('computers.show', $computer) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" 
                                                   title="View Computer Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_computers')
                                                <a href="{{ route('computers.edit', $computer) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" 
                                                   title="Edit Computer">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete_computers')
                                                <form action="{{ route('computers.destroy', $computer) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to permanently delete this computer? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" 
                                                            title="Delete Computer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-desktop fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No computers found.</p>
                                            <a href="{{ route('computers.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Add First Computer
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($computers->hasPages())
                        <div class="pagination-wrapper mt-3">
                            {{ $computers->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Action Button Styles */
.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 14px;
    position: relative;
    overflow: hidden;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-btn-view {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    border-color: #4f46e5;
}

.action-btn-view:hover {
    background: linear-gradient(135deg, #3730a3 0%, #6d28d9 100%);
    color: white;
}

.action-btn-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    border-color: #f59e0b;
}

.action-btn-edit:hover {
    background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
    color: white;
}

.action-btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-color: #ef4444;
}

.action-btn-delete:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.action-btn-print {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-color: #10b981;
}

.action-btn-print:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
}

.action-btn-reminder {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border-color: #8b5cf6;
}

.action-btn-reminder:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
}

.action-btn-mark {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
    border-color: #06b6d4;
}

.action-btn-mark:hover {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
}

/* Loading state */
.action-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.action-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@section('scripts')
<script>
    // Enhanced Loading States for Computers
    class ComputerLoadingManager {
        constructor() {
            this.init();
        }
        
        init() {
            this.setupSearchLoading();
            this.setupFormLoading();
        }
        
        setupSearchLoading() {
            const searchForm = document.querySelector('form[method="GET"]');
            const searchBtn = document.querySelector('button[type="submit"]');
            
            if (searchForm && searchBtn) {
                searchForm.addEventListener('submit', (e) => {
                    this.showSkeletonLoading();
                    searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
                    searchBtn.disabled = true;
                });
            }
        }
        
        setupFormLoading() {
            // Only target main search form, not action button forms
            const searchForm = document.querySelector('form[method="GET"]');
            if (searchForm) {
                searchForm.addEventListener('submit', (e) => {
                    const submitBtn = searchForm.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        this.showButtonLoading(submitBtn);
                        
                        // Reset button state after search completes
                        setTimeout(() => {
                            this.hideButtonLoading(submitBtn);
                        }, 1000);
                    }
                });
            }
        }
        
        showSkeletonLoading() {
            const skeleton = document.getElementById('skeleton-loading');
            const mainTable = document.getElementById('main-table');
            if (skeleton && mainTable) {
                skeleton.classList.remove('d-none');
                mainTable.style.display = 'none';
            }
        }
        
        hideSkeletonLoading() {
            const skeleton = document.getElementById('skeleton-loading');
            const mainTable = document.getElementById('main-table');
            if (skeleton && mainTable) {
                skeleton.classList.add('d-none');
                mainTable.style.display = 'block';
            }
        }
        
        showButtonLoading(button) {
            // Store original content if not already stored
            if (!button.dataset.originalContent) {
                button.dataset.originalContent = button.innerHTML;
            }
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            button.disabled = true;
            button.classList.add('loading');
        }
        
        hideButtonLoading(button) {
            if (button.dataset.originalContent) {
                button.innerHTML = button.dataset.originalContent;
                button.disabled = false;
                button.classList.remove('loading');
            }
        }
    }
    
    // Initialize loading manager when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        window.computerLoadingManager = new ComputerLoadingManager();
        
        // Ensure skeleton loading is hidden by default
        const skeleton = document.getElementById('skeleton-loading');
        if (skeleton) {
            skeleton.classList.add('d-none');
        }
        
        // Reset any stuck loading states
        const loadingButtons = document.querySelectorAll('.btn.loading');
        loadingButtons.forEach(button => {
            if (button.dataset.originalContent) {
                button.innerHTML = button.dataset.originalContent;
                button.disabled = false;
                button.classList.remove('loading');
            }
        });
    });
</script>
@endsection