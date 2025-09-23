@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Search Results</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('search.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-search me-1"></i> Advanced Search
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('search.results') }}" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="Search assets, users, departments..."
                                   required>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="assets" {{ request('type') == 'assets' ? 'selected' : '' }}>Assets</option>
                                <option value="users" {{ request('type') == 'users' ? 'selected' : '' }}>Users</option>
                                <option value="departments" {{ request('type') == 'departments' ? 'selected' : '' }}>Departments</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="row">
        <div class="col-12">
            @if(isset($results))
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Search Results for "{{ request('q') }}"
                            <span class="badge bg-primary ms-2">{{ collect($results)->sum(function($result) { return $result->total(); }) }} results</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalResults = 0;
                            $hasResults = false;
                            foreach($results as $type => $collection) {
                                if($collection->count() > 0) {
                                    $hasResults = true;
                                    $totalResults += $collection->total();
                                }
                            }
                        @endphp
                        
                        @if($hasResults)
                            @foreach($results as $type => $collection)
                                @if($collection->count() > 0)
                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-{{ $type === 'assets' ? 'box' : ($type === 'users' ? 'users' : ($type === 'categories' ? 'tags' : ($type === 'departments' ? 'building' : 'truck'))) }} me-2"></i>
                                            {{ ucfirst($type) }} ({{ $collection->total() }} results)
                                        </h6>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Details</th>
                                                        @if($type === 'assets')
                                                            <th>Status</th>
                                                        @endif
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($collection as $item)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $item->name ?? $item->asset_tag ?? 'N/A' }}</strong>
                                                                @if(isset($item->asset_tag))
                                                                    <br><small class="text-muted">{{ $item->asset_tag }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($type === 'assets')
                                                                    @if(isset($item->category))
                                                                        <div>{{ $item->category->name ?? 'N/A' }}</div>
                                                                    @endif
                                                                    @if(isset($item->department))
                                                                        <div><small class="text-muted">{{ $item->department->name ?? 'N/A' }}</small></div>
                                                                    @endif
                                                                    @if(isset($item->serial_number))
                                                                        <div><small class="text-muted">SN: {{ $item->serial_number }}</small></div>
                                                                    @endif
                                                                @elseif($type === 'users')
                                                                    <div>{{ $item->email ?? 'N/A' }}</div>
                                                                    @if(isset($item->department))
                                                                        <div><small class="text-muted">{{ $item->department->name ?? 'N/A' }}</small></div>
                                                                    @endif
                                                                @else
                                                                    <div>{{ $item->description ?? 'N/A' }}</div>
                                                                @endif
                                                            </td>
                                                            @if($type === 'assets')
                                                                <td>
                                                                    @if(isset($item->status))
                                                                        <span class="badge bg-{{ $item->status == 'Active' ? 'success' : ($item->status == 'Issue Reported' ? 'danger' : 'warning') }}">
                                                                            {{ $item->status }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary">N/A</span>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                            <td>
                                                                @if($type === 'assets')
                                                                    <a href="{{ route('assets.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </a>
                                                                @elseif($type === 'users')
                                                                    <a href="{{ route('users.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No actions</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        @if($collection->hasPages())
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $collection->appends(request()->query())->links() }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5>No results found</h5>
                                <p class="text-muted">Try adjusting your search terms or filters.</p>
                                <a href="{{ route('search.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Try Advanced Search
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5>Enter your search terms</h5>
                        <p class="text-muted">Use the search form above to find assets, users, or departments.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

