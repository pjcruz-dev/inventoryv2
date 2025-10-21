@props([
    'filters' => [],
    'route'
])

@if(count($filters) > 0 || request()->filled('search'))
<div class="alert alert-info alert-dismissible fade show mb-3" role="alert" style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); border: 2px solid #3b82f6; border-radius: 12px;">
    <div class="d-flex align-items-start">
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2">
                <i class="fas fa-info-circle me-2"></i>Active Filters
            </h6>
            <div class="d-flex flex-wrap gap-2">
                @if(request()->filled('search'))
                    <span class="badge bg-primary" style="padding: 0.5rem 0.75rem;">
                        <i class="fas fa-search me-1"></i>Search: "{{ request('search') }}"
                    </span>
                @endif
                
                @foreach($filters as $filter)
                    <span class="badge {{ $filter['class'] ?? 'bg-info' }}" style="padding: 0.5rem 0.75rem;">
                        <i class="{{ $filter['icon'] ?? 'fas fa-filter' }} me-1"></i>{{ $filter['label'] }}: {{ $filter['value'] }}
                    </span>
                @endforeach
            </div>
        </div>
        <a href="{{ $route }}" class="btn btn-sm btn-outline-danger ms-3" title="Clear all filters">
            <i class="fas fa-times"></i>
        </a>
    </div>
</div>
@endif

