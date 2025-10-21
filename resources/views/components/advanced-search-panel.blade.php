@props([
    'route',
    'placeholder' => 'Search...',
    'searchValue' => '',
    'showAdvanced' => false,
    'activeFiltersCount' => 0,
])

<!-- Search Section -->
<div class="mt-3">
    <div class="row">
        <div class="col-md-8">
            <form method="GET" action="{{ $route }}" id="searchForm">
                <div class="input-group">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="{{ $placeholder }}" 
                           value="{{ $searchValue }}" 
                           style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                    <button class="btn btn-primary" 
                            type="submit" 
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" 
                            class="btn {{ $showAdvanced ? 'btn-primary' : '' }}" 
                            id="toggleAdvancedSearch" 
                            style="border-radius: 0 6px 6px 0; color: white; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                        <i class="fas fa-filter"></i> 
                        <span id="advancedBtnText">{{ $showAdvanced ? 'Hide Filters' : 'Advanced' }}</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            @if($activeFiltersCount > 0)
                <span class="badge bg-primary me-2" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                    <i class="fas fa-filter me-1"></i>{{ $activeFiltersCount }} Active Filter{{ $activeFiltersCount > 1 ? 's' : '' }}
                </span>
                <a href="{{ $route }}" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-times"></i> Clear All
                </a>
            @endif
        </div>
    </div>
    
    <!-- Advanced Search Panel -->
    <div id="advancedSearchPanel" class="mt-3" style="display: {{ $showAdvanced ? 'block' : 'none' }};">
        <div class="card" style="border: 2px solid #667eea; border-radius: 12px;">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px 10px 0 0;">
                <h6 class="mb-0 text-white">
                    <i class="fas fa-sliders-h me-2"></i>Advanced Search Filters
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ $route }}" id="advancedSearchForm">
                    @if($searchValue)
                        <input type="hidden" name="search" value="{{ $searchValue }}">
                    @endif
                    
                    {{ $slot }}
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="button" 
                                    class="btn btn-outline-secondary" 
                                    onclick="document.getElementById('advancedSearchForm').reset(); window.location.href='{{ $route }}';">
                                <i class="fas fa-redo me-1"></i>Reset All
                            </button>
                            <button type="submit" 
                                    class="btn btn-primary" 
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="fas fa-search me-1"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#toggleAdvancedSearch').on('click', function() {
        const panel = $('#advancedSearchPanel');
        const btnText = $('#advancedBtnText');
        
        panel.slideToggle(300, function() {
            if (panel.is(':visible')) {
                btnText.text('Hide Filters');
            } else {
                btnText.text('Advanced');
            }
        });
    });
});
</script>
@endpush

