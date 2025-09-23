@extends('layouts.app')

@section('title', 'Advanced Search')
@section('page-title', 'Advanced Search')

@section('page-actions')
    <a href="{{ route('assets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Assets
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>Search Everything
                    </h5>
                </div>
                <div class="card-body">
                    <form id="searchForm" method="GET" action="{{ route('search.results') }}">
                        <!-- Main Search Input -->
                        <div class="mb-4">
                            <label for="searchQuery" class="form-label">Search Query</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="searchQuery" 
                                       name="q" 
                                       placeholder="Search assets, users, categories, departments, vendors..."
                                       value="{{ request('q') }}"
                                       autocomplete="off">
                                <div class="position-absolute top-50 end-0 translate-middle-y pe-3">
                                    <i class="fas fa-search text-muted"></i>
                                </div>
                                <!-- Search Suggestions Dropdown -->
                                <div id="searchSuggestions" class="dropdown-menu w-100" style="display: none;">
                                    <div id="suggestionsContent">
                                        <!-- Suggestions will be loaded here -->
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-lightbulb me-1"></i>
                                Try searching for asset names, tags, serial numbers, user names, or categories
                            </div>
                        </div>

                        <!-- Advanced Filters -->
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-outline-secondary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false">
                                    <i class="fas fa-filter me-1"></i>Advanced Filters
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <div class="collapse" id="advancedFilters">
                            <div class="card card-body bg-light">
                                <div class="row">
                                    <!-- Status Filter -->
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status[]" multiple>
                                            @foreach($filterOptions['statuses'] as $status)
                                                <option value="{{ $status }}" 
                                                    {{ in_array($status, (array) request('status', [])) ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Category Filter -->
                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id[]" multiple>
                                            @foreach($filterOptions['categories'] as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ in_array($category->id, (array) request('category_id', [])) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Department Filter -->
                                    <div class="col-md-6 mb-3">
                                        <label for="department_id" class="form-label">Department</label>
                                        <select class="form-select" id="department_id" name="department_id[]" multiple>
                                            @foreach($filterOptions['departments'] as $department)
                                                <option value="{{ $department->id }}" 
                                                    {{ in_array($department->id, (array) request('department_id', [])) ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Vendor Filter -->
                                    <div class="col-md-6 mb-3">
                                        <label for="vendor_id" class="form-label">Vendor</label>
                                        <select class="form-select" id="vendor_id" name="vendor_id[]" multiple>
                                            @foreach($filterOptions['vendors'] as $vendor)
                                                <option value="{{ $vendor->id }}" 
                                                    {{ in_array($vendor->id, (array) request('vendor_id', [])) ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="col-md-6 mb-3">
                                        <label for="date_from" class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="date_to" class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                    </div>

                                    <!-- Cost Range -->
                                    <div class="col-md-6 mb-3">
                                        <label for="cost_min" class="form-label">Min Cost (₱)</label>
                                        <input type="number" class="form-control" id="cost_min" name="cost_min" value="{{ request('cost_min') }}" step="0.01">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="cost_max" class="form-label">Max Cost (₱)</label>
                                        <input type="number" class="form-control" id="cost_max" name="cost_max" value="{{ request('cost_max') }}" step="0.01">
                                    </div>
                                </div>

                                <!-- Filter Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                            <i class="fas fa-times me-1"></i>Clear Filters
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSearch()">
                                            <i class="fas fa-save me-1"></i>Save Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="clearSearch()">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            @if(request('q'))
                <div id="searchResults">
                    <!-- Results will be loaded here -->
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Quick Search Tips -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Search Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Use quotes for exact phrases: "Dell Laptop"
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Search by asset tag: LAP-001
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Search by serial number: SN123456
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Search by user name: John Doe
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Use filters to narrow down results
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Popular Searches -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Popular Searches
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularSearches as $search)
                            <button class="btn btn-outline-primary btn-sm" onclick="quickSearch('{{ $search }}')">
                                {{ $search }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Search Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Search Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary mb-0" id="totalAssets">-</div>
                            <small class="text-muted">Total Assets</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-info mb-0" id="totalUsers">-</div>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.search-suggestion {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-suggestion:hover {
    background-color: #f8f9fa;
}

.search-suggestion:last-child {
    border-bottom: none;
}

.search-suggestion-icon {
    width: 20px;
    text-align: center;
    margin-right: 0.75rem;
}

.search-suggestion-text {
    flex: 1;
}

.search-suggestion-subtext {
    font-size: 0.875rem;
    color: #6c757d;
}

.quick-search-btn {
    transition: all 0.2s;
}

.quick-search-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Dark mode styles */
[data-theme="dark"] .search-suggestion:hover {
    background-color: #343a40;
}

[data-theme="dark"] .card-body.bg-light {
    background-color: #343a40 !important;
}
</style>
@endpush

@push('scripts')
<script>
class AdvancedSearch {
    constructor() {
        this.searchInput = document.getElementById('searchQuery');
        this.suggestionsDropdown = document.getElementById('searchSuggestions');
        this.suggestionsContent = document.getElementById('suggestionsContent');
        this.searchForm = document.getElementById('searchForm');
        this.searchResults = document.getElementById('searchResults');
        
        this.init();
    }
    
    init() {
        this.setupSearchInput();
        this.setupFormSubmission();
        this.loadInitialData();
    }
    
    setupSearchInput() {
        let searchTimeout;
        
        this.searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    this.loadSuggestions(query);
                }, 300);
            } else {
                this.hideSuggestions();
            }
        });
        
        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.trim().length >= 2) {
                this.loadSuggestions(this.searchInput.value.trim());
            }
        });
        
        this.searchInput.addEventListener('blur', () => {
            // Delay hiding to allow clicking on suggestions
            setTimeout(() => {
                this.hideSuggestions();
            }, 200);
        });
        
        // Keyboard navigation
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
                e.preventDefault();
                this.handleKeyboardNavigation(e);
            }
        });
    }
    
    setupFormSubmission() {
        this.searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.performSearch();
        });
    }
    
    async loadSuggestions(query) {
        try {
            const response = await fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.displaySuggestions(data.suggestions);
            }
        } catch (error) {
            console.error('Error loading suggestions:', error);
        }
    }
    
    displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            this.hideSuggestions();
            return;
        }
        
        this.suggestionsContent.innerHTML = suggestions.map(suggestion => `
            <div class="search-suggestion d-flex align-items-center" onclick="selectSuggestion('${suggestion.text}', '${suggestion.url}')">
                <div class="search-suggestion-icon">
                    <i class="${suggestion.icon}"></i>
                </div>
                <div class="search-suggestion-text">
                    <div class="fw-medium">${suggestion.text}</div>
                    <div class="search-suggestion-subtext">${suggestion.subtext}</div>
                </div>
            </div>
        `).join('');
        
        this.suggestionsDropdown.style.display = 'block';
    }
    
    hideSuggestions() {
        this.suggestionsDropdown.style.display = 'none';
    }
    
    handleKeyboardNavigation(e) {
        const suggestions = this.suggestionsContent.querySelectorAll('.search-suggestion');
        const activeSuggestion = this.suggestionsContent.querySelector('.search-suggestion.active');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.classList.remove('active');
                const next = activeSuggestion.nextElementSibling;
                if (next) {
                    next.classList.add('active');
                } else {
                    suggestions[0].classList.add('active');
                }
            } else {
                suggestions[0].classList.add('active');
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.classList.remove('active');
                const prev = activeSuggestion.previousElementSibling;
                if (prev) {
                    prev.classList.add('active');
                } else {
                    suggestions[suggestions.length - 1].classList.add('active');
                }
            } else {
                suggestions[suggestions.length - 1].classList.add('active');
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.click();
            } else {
                this.performSearch();
            }
        }
    }
    
    async performSearch() {
        const formData = new FormData(this.searchForm);
        const query = formData.get('q');
        
        if (!query.trim()) {
            return;
        }
        
        this.hideSuggestions();
        
        try {
            const response = await fetch('{{ route("search") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.results, data.stats);
            }
        } catch (error) {
            console.error('Error performing search:', error);
        }
    }
    
    displayResults(results, stats) {
        // Update statistics
        document.getElementById('totalAssets').textContent = stats.assets || 0;
        document.getElementById('totalUsers').textContent = stats.users || 0;
        
        // Display results
        this.searchResults.innerHTML = this.buildResultsHTML(results);
    }
    
    buildResultsHTML(results) {
        let html = '<div class="card"><div class="card-header"><h5 class="mb-0">Search Results</h5></div><div class="card-body">';
        
        // Assets
        if (results.assets && results.assets.length > 0) {
            html += '<h6 class="text-primary mb-3"><i class="fas fa-desktop me-2"></i>Assets (' + results.assets.length + ')</h6>';
            html += '<div class="row">';
            
            results.assets.forEach(asset => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        ${asset.hasImage ? 
                                            `<img src="${asset.getImageUrl()}" alt="${asset.getImageAlt()}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">` :
                                            `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fas fa-desktop text-muted"></i></div>`
                                        }
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">
                                            <a href="${asset.show_url}" class="text-decoration-none">${asset.name}</a>
                                        </h6>
                                        <p class="card-text text-muted small mb-1">${asset.asset_tag}</p>
                                        <span class="badge bg-${this.getStatusColor(asset.status)}">${asset.status}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
        }
        
        // Users
        if (results.users && results.users.length > 0) {
            html += '<h6 class="text-info mb-3 mt-4"><i class="fas fa-user me-2"></i>Users (' + results.users.length + ')</h6>';
            html += '<div class="row">';
            
            results.users.forEach(user => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <a href="${user.show_url}" class="text-decoration-none">${user.first_name} ${user.last_name}</a>
                                        </h6>
                                        <p class="card-text text-muted small mb-0">${user.email}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
        }
        
        html += '</div></div>';
        
        return html;
    }
    
    getStatusColor(status) {
        const colors = {
            'Active': 'success',
            'Inactive': 'danger',
            'Under Maintenance': 'warning',
            'Issue Reported': 'danger',
            'Pending Confirmation': 'info',
            'Disposed': 'dark'
        };
        return colors[status] || 'secondary';
    }
    
    async loadInitialData() {
        // Load initial statistics
        try {
            const response = await fetch('{{ route("search") }}?q=*');
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('totalAssets').textContent = data.stats.assets || 0;
                document.getElementById('totalUsers').textContent = data.stats.users || 0;
            }
        } catch (error) {
            console.error('Error loading initial data:', error);
        }
    }
}

// Global functions
function selectSuggestion(text, url) {
    document.getElementById('searchQuery').value = text;
    document.getElementById('searchSuggestions').style.display = 'none';
    
    if (url) {
        window.location.href = url;
    } else {
        document.getElementById('searchForm').submit();
    }
}

function quickSearch(query) {
    document.getElementById('searchQuery').value = query;
    document.getElementById('searchForm').submit();
}

function clearSearch() {
    document.getElementById('searchQuery').value = '';
    document.getElementById('searchResults').innerHTML = '';
    document.getElementById('totalAssets').textContent = '-';
    document.getElementById('totalUsers').textContent = '-';
}

function clearFilters() {
    document.querySelectorAll('#advancedFilters select, #advancedFilters input').forEach(element => {
        if (element.type === 'checkbox' || element.type === 'radio') {
            element.checked = false;
        } else {
            element.value = '';
        }
    });
}

function saveSearch() {
    // This would save the current search for later use
    alert('Search saved! (Feature coming soon)');
}

// Initialize search when page loads
document.addEventListener('DOMContentLoaded', function() {
    new AdvancedSearch();
});
</script>
@endpush

