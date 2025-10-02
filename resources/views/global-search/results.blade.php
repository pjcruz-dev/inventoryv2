@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Enhanced Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <div class="search-icon-large me-3">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-1 text-dark">
                            Search Results
                        </h1>
                        <p class="text-muted mb-0">
                            Results for: <span class="search-query-highlight">"{{ $query }}"</span>
                            <span class="badge bg-primary ms-2">{{ $results->count() }} found</span>
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            
            <!-- Enhanced Search Again Section -->
            <div class="search-again-container">
                <form action="{{ route('search.results') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" name="q" class="form-control" 
                               placeholder="Search..." 
                               value="{{ $query }}" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($results->isEmpty())
        <!-- Enhanced No Results -->
        <div class="row">
            <div class="col-12">
                <div class="no-results-card">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Results Found</h4>
                    <p class="text-muted mb-4">
                        No items found matching your search for <span class="search-query-highlight">"{{ $query }}"</span>
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i>Go Back
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Enhanced Search Results -->
        <div class="row">
            @foreach($groupedResults as $category => $categoryResults)
                <div class="col-12 mb-4">
                    <div class="search-category-card">
                        <div class="search-category-header">
                            <div class="d-flex align-items-center">
                                <div class="category-icon me-3">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-white">{{ $category }}</h5>
                                    <small class="text-white-50">{{ $categoryResults->count() }} results</small>
                                </div>
                            </div>
                        </div>
                        <div class="search-results-list">
                            @foreach($categoryResults as $result)
                                <a href="{{ $result['url'] }}" class="search-result-item">
                                    <div class="search-result-icon {{ strtolower($category) }}">
                                        <i class="{{ $result['icon'] }}"></i>
                                    </div>
                                    <div class="search-result-content">
                                        <div class="search-result-title">{{ $result['title'] }}</div>
                                        <div class="search-result-description">{{ $result['description'] }}</div>
                                    </div>
                                    <div class="search-result-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
/* Enhanced Search Results Page Styles */
.search-icon-large {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.search-query-highlight {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
}

.search-again-container {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.search-form .input-group {
    max-width: 500px;
}

.search-category-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.search-category-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.search-category-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    color: white;
}

.category-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.search-results-list {
    background: #fff;
}

.search-result-item {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.search-result-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(4px);
    text-decoration: none;
    color: inherit;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
    flex-shrink: 0;
}

.search-result-icon.assets {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.search-result-icon.users {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.search-result-icon.categories {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.search-result-icon.departments {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.search-result-icon.vendors {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.search-result-icon.assignments {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
}

.search-result-icon.maintenance {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #333;
}

.search-result-icon.disposals {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    color: #333;
}

.search-result-content {
    flex: 1;
    min-width: 0;
}

.search-result-title {
    font-weight: 600;
    font-size: 16px;
    color: #2d3748;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.search-result-description {
    font-size: 14px;
    color: #718096;
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.search-result-arrow {
    color: #cbd5e0;
    font-size: 14px;
    margin-left: 10px;
    transition: all 0.2s ease;
}

.search-result-item:hover .search-result-arrow {
    color: #667eea;
    transform: translateX(3px);
}

/* No Results Styling */
.no-results-card {
    background: #fff;
    border-radius: 12px;
    padding: 60px 40px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.no-results-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #6c757d;
    font-size: 32px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-icon-large {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .search-result-item {
        padding: 12px 16px;
    }
    
    .search-result-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .search-result-title {
        font-size: 15px;
    }
    
    .search-result-description {
        font-size: 13px;
    }
}
</style>
@endsection
