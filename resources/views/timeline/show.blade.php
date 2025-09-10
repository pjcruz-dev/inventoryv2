@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Asset Timeline: {{ $asset->name }}</h4>
                        <small class="text-muted">Asset Tag: {{ $asset->asset_tag }}</small>
                    </div>
                    <div>
                        <a href="{{ route('timeline.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Back to Timeline
                        </a>
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Asset
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Asset Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Current Asset Information</h6>
                                    <p><strong>Name:</strong> {{ $asset->name }}</p>
                                    <p><strong>Category:</strong> {{ $asset->category->name ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $asset->status == 'active' ? 'success' : ($asset->status == 'inactive' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </p>
                                    @if($asset->assignedUser)
                                        <p><strong>Currently Assigned To:</strong> {{ $asset->assignedUser->name }}</p>
                                    @endif
                                    @if($asset->department)
                                        <p><strong>Department:</strong> {{ $asset->department->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Timeline Summary</h6>
                                    <p><strong>Total Entries:</strong> {{ $timeline->total() }}</p>
                                    <p><strong>Created:</strong> {{ $asset->created_at->format('M d, Y h:i A') }}</p>
                                    @if($timeline->count() > 0)
                                        <p><strong>Last Activity:</strong> {{ $timeline->first()->performed_at->format('M d, Y h:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="timeline">
                        @forelse($timeline as $entry)
                            <div class="timeline-item mb-4">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <div class="timeline-badge bg-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : 'secondary')) }}">
                                            <i class="fas fa-{{ $entry->action == 'created' ? 'plus' : ($entry->action == 'assigned' ? 'user-plus' : ($entry->action == 'transferred' ? 'exchange-alt' : 'edit')) }}"></i>
                                        </div>
                                        <small class="text-muted">{{ $entry->performed_at->format('M d, Y') }}</small><br>
                                        <small class="text-muted">{{ $entry->performed_at->format('h:i A') }}</small>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="card border-left-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : 'secondary')) }}">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <span class="badge bg-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst($entry->action) }}
                                                    </span>
                                                    <small class="text-muted ms-2">{{ $entry->performed_at->diffForHumans() }}</small>
                                                </h6>
                                                
                                                @if($entry->action == 'transferred')
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            @if($entry->fromUser)
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <i class="fas fa-user-minus text-danger me-2"></i>
                                                                    <span><strong>From:</strong> {{ $entry->fromUser->name }}</span>
                                                                </div>
                                                            @endif
                                                            @if($entry->fromDepartment)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-building text-danger me-2"></i>
                                                                    <span><strong>From Dept:</strong> {{ $entry->fromDepartment->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            @if($entry->toUser)
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <i class="fas fa-user-plus text-success me-2"></i>
                                                                    <span><strong>To:</strong> {{ $entry->toUser->name }}</span>
                                                                </div>
                                                            @endif
                                                            @if($entry->toDepartment)
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-building text-success me-2"></i>
                                                                    <span><strong>To Dept:</strong> {{ $entry->toDepartment->name }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($entry->action == 'assigned')
                                                    @if($entry->toUser)
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-user-plus text-primary me-2"></i>
                                                            <span><strong>Assigned to:</strong> {{ $entry->toUser->name }}</span>
                                                        </div>
                                                    @endif
                                                    @if($entry->toDepartment)
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-building text-primary me-2"></i>
                                                            <span><strong>Department:</strong> {{ $entry->toDepartment->name }}</span>
                                                        </div>
                                                    @endif
                                                @elseif($entry->action == 'unassigned')
                                                    @if($entry->fromUser)
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-user-minus text-warning me-2"></i>
                                                            <span><strong>Unassigned from:</strong> {{ $entry->fromUser->name }}</span>
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                @if($entry->notes)
                                                    <div class="mt-3">
                                                        <i class="fas fa-sticky-note text-muted me-2"></i>
                                                        <span>{{ $entry->notes }}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user text-muted me-1"></i>
                                                        Performed by: {{ $entry->performedBy->name }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No timeline entries found</h5>
                                <p class="text-muted">This asset has no recorded timeline activities.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $timeline->links() }}
                    </div>
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

.timeline-item:last-child {
    margin-bottom: 0 !important;
}
</style>
@endsection