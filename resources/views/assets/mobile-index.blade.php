@extends('layouts.mobile')

@section('title', 'Assets - Mobile View')
@section('page-title', 'Assets')

@section('page-actions')
    @can('create_assets')
    <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>
        <span class="d-none d-sm-inline">Add Asset</span>
    </a>
    @endcan
    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-list me-1"></i>
        <span class="d-none d-sm-inline">Table View</span>
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Mobile Search Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               placeholder="Search assets..." 
                               id="mobileSearchInput"
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="button" id="mobileSearchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#mobileFilters">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    
                    <!-- Mobile Filters -->
                    <div class="collapse mt-3" id="mobileFilters">
                        <div class="row g-2">
                            <div class="col-6">
                                <select class="form-select form-select-sm" id="mobileStatusFilter">
                                    <option value="">All Status</option>
                                    @foreach(['Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-select form-select-sm" id="mobileCategoryFilter">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Asset Cards -->
    <div class="row" id="mobileAssetCards">
        @foreach($assets as $asset)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3 asset-card" data-asset-id="{{ $asset->id }}">
            <div class="card h-100 shadow-sm">
                <!-- Asset Image -->
                <div class="card-img-top position-relative" style="height: 150px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    @if($asset->hasImage())
                        <img src="{{ $asset->getImageUrl() }}" 
                             alt="{{ $asset->getImageAlt() }}" 
                             class="w-100 h-100 object-fit-cover rounded-top"
                             style="object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="fas fa-desktop fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <span class="position-absolute top-0 end-0 m-2 badge bg-{{ 
                        $asset->status === 'Active' ? 'success' : 
                        ($asset->status === 'Inactive' ? 'danger' : 
                        ($asset->status === 'Under Maintenance' ? 'warning' : 
                        ($asset->status === 'Issue Reported' ? 'danger' : 
                        ($asset->status === 'Pending Confirmation' ? 'info' : 
                        ($asset->status === 'Disposed' ? 'dark' : 'secondary')))))
                    }} rounded-pill">
                        {{ $asset->status }}
                    </span>
                </div>

                <!-- Card Body -->
                <div class="card-body p-3">
                    <h6 class="card-title mb-2 text-truncate" title="{{ $asset->name }}">
                        {{ $asset->name }}
                    </h6>
                    
                    <div class="mb-2">
                        <small class="text-muted d-block">Asset Tag:</small>
                        <code class="text-primary">{{ $asset->asset_tag }}</code>
                    </div>

                    @if($asset->category)
                    <div class="mb-2">
                        <small class="text-muted d-block">Category:</small>
                        <span class="badge bg-info">{{ $asset->category->name }}</span>
                    </div>
                    @endif

                    @if($asset->assignedUser)
                    <div class="mb-2">
                        <small class="text-muted d-block">Assigned to:</small>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                <i class="fas fa-user text-primary" style="font-size: 10px;"></i>
                            </div>
                            <small class="text-truncate">
                                {{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}
                            </small>
                        </div>
                    </div>
                    @endif

                    @if($asset->location)
                    <div class="mb-3">
                        <small class="text-muted d-block">Location:</small>
                        <small class="text-truncate d-block">
                            <i class="fas fa-map-marker-alt text-secondary me-1"></i>
                            {{ $asset->location }}
                        </small>
                    </div>
                    @endif
                </div>

                <!-- Card Actions -->
                <div class="card-footer bg-transparent border-0 p-3 pt-0">
                    <div class="btn-group w-100" role="group">
                        @can('view_assets')
                        <a href="{{ route('assets.show', $asset) }}" 
                           class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i>
                            <span class="d-none d-sm-inline">View</span>
                        </a>
                        @endcan
                        
                        @can('edit_assets')
                        <a href="{{ route('assets.edit', $asset) }}" 
                           class="btn btn-outline-warning btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i>
                            <span class="d-none d-sm-inline">Edit</span>
                        </a>
                        @endcan
                        
                        @can('view_assets')
                        <a href="{{ route('assets.qr-code', $asset) }}" 
                           class="btn btn-outline-info btn-sm flex-fill" 
                           target="_blank">
                            <i class="fas fa-qrcode me-1"></i>
                            <span class="d-none d-sm-inline">QR</span>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Mobile Pagination -->
    <div class="row">
        <div class="col-12">
            <nav aria-label="Asset pagination">
                <ul class="pagination justify-content-center">
                    @if($assets->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $assets->previousPageUrl() }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    @foreach($assets->getUrlRange(1, $assets->lastPage()) as $page => $url)
                        @if($page == $assets->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if($assets->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $assets->nextPageUrl() }}">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>

    <!-- Mobile Floating Action Button -->
    @can('create_assets')
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1000;">
        <button class="btn btn-primary rounded-circle shadow-lg" 
                style="width: 56px; height: 56px;"
                onclick="window.location.href='{{ route('assets.create') }}'"
                title="Add New Asset">
            <i class="fas fa-plus fa-lg"></i>
        </button>
    </div>
    @endcan
</div>

<!-- Mobile Asset Actions Modal -->
<div class="modal fade" id="mobileAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asset Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action" id="modalViewAsset">
                        <i class="fas fa-eye me-3"></i>View Details
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" id="modalEditAsset">
                        <i class="fas fa-edit me-3"></i>Edit Asset
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" id="modalQRAsset">
                        <i class="fas fa-qrcode me-3"></i>QR Code
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" id="modalAssignAsset">
                        <i class="fas fa-user-plus me-3"></i>Assign User
                    </a>
                    <a href="#" class="list-group-item list-group-item-action text-danger" id="modalDeleteAsset">
                        <i class="fas fa-trash me-3"></i>Delete Asset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Mobile-specific styles */
@media (max-width: 768px) {
    .asset-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .asset-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .card-img-top {
        border-radius: 0.375rem 0.375rem 0 0;
    }
    
    .btn-group .btn {
        font-size: 0.75rem;
        padding: 0.375rem 0.5rem;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    /* Touch-friendly spacing */
    .card-body {
        padding: 1rem;
    }
    
    .btn {
        min-height: 44px; /* iOS recommended touch target size */
    }
    
    /* Swipe gestures */
    .asset-card {
        touch-action: pan-y;
    }
    
    /* Mobile-optimized text */
    .card-title {
        font-size: 1rem;
        line-height: 1.2;
    }
    
    .text-truncate {
        max-width: 100%;
    }
}

/* Tablet-specific styles */
@media (min-width: 768px) and (max-width: 1024px) {
    .asset-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .asset-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
}

/* Dark mode mobile styles */
[data-theme="dark"] .asset-card {
    background-color: #2d2d2d;
    border-color: #404040;
}

[data-theme="dark"] .card-img-top {
    background: linear-gradient(135deg, #343a40 0%, #2d2d2d 100%);
}

[data-theme="dark"] .list-group-item {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e9ecef;
}

[data-theme="dark"] .list-group-item:hover {
    background-color: #343a40;
}

/* Touch feedback */
.touch-feedback {
    transition: all 0.1s ease;
}

.touch-feedback:active {
    transform: scale(0.98);
    opacity: 0.8;
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
class MobileAssetView {
    constructor() {
        this.searchInput = document.getElementById('mobileSearchInput');
        this.searchBtn = document.getElementById('mobileSearchBtn');
        this.statusFilter = document.getElementById('mobileStatusFilter');
        this.categoryFilter = document.getElementById('mobileCategoryFilter');
        this.assetCards = document.querySelectorAll('.asset-card');
        this.modal = new bootstrap.Modal(document.getElementById('mobileAssetModal'));
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupTouchGestures();
        this.setupInfiniteScroll();
    }
    
    setupEventListeners() {
        // Search functionality
        this.searchInput.addEventListener('input', (e) => {
            this.debounceSearch(e.target.value);
        });
        
        this.searchBtn.addEventListener('click', () => {
            this.performSearch();
        });
        
        // Filter functionality
        this.statusFilter.addEventListener('change', () => {
            this.applyFilters();
        });
        
        this.categoryFilter.addEventListener('change', () => {
            this.applyFilters();
        });
        
        // Asset card interactions
        this.assetCards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.btn')) {
                    this.showAssetModal(card);
                }
            });
            
            // Add touch feedback
            card.addEventListener('touchstart', () => {
                card.classList.add('touch-feedback');
            });
            
            card.addEventListener('touchend', () => {
                setTimeout(() => {
                    card.classList.remove('touch-feedback');
                }, 150);
            });
        });
    }
    
    setupTouchGestures() {
        // Swipe gestures for asset cards
        this.assetCards.forEach(card => {
            let startX = 0;
            let startY = 0;
            let endX = 0;
            let endY = 0;
            
            card.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            });
            
            card.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                endY = e.changedTouches[0].clientY;
                
                const deltaX = endX - startX;
                const deltaY = endY - startY;
                
                // Horizontal swipe
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                    if (deltaX > 0) {
                        this.handleSwipeRight(card);
                    } else {
                        this.handleSwipeLeft(card);
                    }
                }
            });
        });
    }
    
    setupInfiniteScroll() {
        let loading = false;
        
        window.addEventListener('scroll', () => {
            if (loading) return;
            
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
                loading = true;
                this.loadMoreAssets();
            }
        });
    }
    
    debounceSearch(query) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch();
        }, 500);
    }
    
    performSearch() {
        const query = this.searchInput.value;
        const status = this.statusFilter.value;
        const category = this.categoryFilter.value;
        
        const params = new URLSearchParams();
        if (query) params.append('search', query);
        if (status) params.append('status', status);
        if (category) params.append('category_id', category);
        
        window.location.href = `{{ route('assets.mobile') }}?${params.toString()}`;
    }
    
    applyFilters() {
        this.performSearch();
    }
    
    showAssetModal(card) {
        const assetId = card.dataset.assetId;
        
        // Update modal links
        document.getElementById('modalViewAsset').href = `{{ url('assets') }}/${assetId}`;
        document.getElementById('modalEditAsset').href = `{{ url('assets') }}/${assetId}/edit`;
        document.getElementById('modalQRAsset').href = `{{ url('assets') }}/${assetId}/qr-code`;
        document.getElementById('modalAssignAsset').href = `{{ url('assets') }}/${assetId}/assign`;
        document.getElementById('modalDeleteAsset').href = `{{ url('assets') }}/${assetId}`;
        
        this.modal.show();
    }
    
    handleSwipeRight(card) {
        // Show quick actions on swipe right
        console.log('Swipe right on asset:', card.dataset.assetId);
    }
    
    handleSwipeLeft(card) {
        // Show quick actions on swipe left
        console.log('Swipe left on asset:', card.dataset.assetId);
    }
    
    async loadMoreAssets() {
        // Implement infinite scroll loading
        console.log('Loading more assets...');
    }
}

// Initialize mobile view when page loads
document.addEventListener('DOMContentLoaded', function() {
    new MobileAssetView();
});
</script>
@endpush
