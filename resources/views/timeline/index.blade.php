@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Asset Timeline</h4>
                    <a href="{{ route('timeline.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Timeline Entry
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('timeline.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="asset_id" class="form-label">Asset</label>
                                <select name="asset_id" id="asset_id" class="form-select">
                                    <option value="">All Assets</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->name }} ({{ $asset->asset_tag }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="action" class="form-label">Action</label>
                                <select name="action" id="action" class="form-select">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('timeline.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Timeline -->
                    <div class="timeline">
                        @forelse($timeline as $entry)
                            <div class="timeline-item mb-4">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <div class="timeline-badge bg-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : ($entry->action == 'unassigned' ? 'danger' : ($entry->action == 'updated' ? 'info' : 'secondary')))) }}">
                                            <i class="fas fa-{{ $entry->action == 'created' ? 'plus' : ($entry->action == 'assigned' ? 'user-plus' : ($entry->action == 'transferred' ? 'exchange-alt' : ($entry->action == 'unassigned' ? 'user-minus' : ($entry->action == 'updated' ? 'edit' : 'cog')))) }}"></i>
                                        </div>
                                        <small class="text-muted">{{ $entry->performed_at->format('M d, Y') }}</small><br>
                                        <small class="text-muted">{{ $entry->performed_at->format('h:i A') }}</small>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="card border-left-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : ($entry->action == 'unassigned' ? 'danger' : ($entry->action == 'updated' ? 'info' : 'secondary')))) }}">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="{{ route('timeline.show', $entry->asset) }}" class="text-decoration-none">
                                                        {{ $entry->asset->name }} ({{ $entry->asset->asset_tag }})
                                                    </a>
                                                    <span class="badge bg-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : ($entry->action == 'unassigned' ? 'danger' : ($entry->action == 'updated' ? 'info' : 'secondary')))) }} ms-2">
                                                        {{ ucfirst($entry->action) }}
                                                    </span>
                                                </h6>
                                                
                                                <div class="row">
                                                    @if($entry->fromUser || $entry->toUser)
                                                        <div class="col-md-6">
                                                            @if($entry->fromUser)
                                                                <strong>From:</strong> {{ $entry->fromUser->name }}<br>
                                                            @endif
                                                            @if($entry->toUser)
                                                                <strong>To:</strong> {{ $entry->toUser->name }}<br>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    
                                                    @if($entry->fromDepartment || $entry->toDepartment)
                                                        <div class="col-md-6">
                                                            @if($entry->fromDepartment)
                                                                <strong>From Dept:</strong> {{ $entry->fromDepartment->name }}<br>
                                                            @endif
                                                            @if($entry->toDepartment)
                                                                <strong>To Dept:</strong> {{ $entry->toDepartment->name }}<br>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if($entry->notes)
                                                    <p class="card-text mt-2">{{ $entry->notes }}</p>
                                                @endif
                                                
                                                <small class="text-muted">
                                                    Performed by: {{ $entry->performedBy->name }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No timeline entries found</h5>
                                <p class="text-muted">Timeline entries will appear here when assets are created or modified.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Pagination -->
                    @if($timeline->hasPages())
                        <div class="pagination-wrapper">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Showing {{ $timeline->firstItem() }} to {{ $timeline->lastItem() }} of {{ $timeline->total() }} timeline entries
                                </div>
                                <div>
                                    {{ $timeline->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-badge {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto 10px;
}

.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.border-left-secondary {
    border-left: 4px solid #6c757d !important;
}
</style>
@endsection