@extends('layouts.app')

@section('title', 'Log Details')

@section('page-title', 'Log Details')

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Logs
        </a>
        <a href="{{ route('logs.export', ['id' => $log->id]) }}" class="btn btn-outline-success">
            <i class="fas fa-download me-1"></i>Export This Log
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Log Details Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Log Entry #{{ $log->id }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Basic Information</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date & Time:</label>
                            <div class="text-muted">
                                {{ $log->created_at ? $log->created_at->format('F d, Y \\a\\t H:i:s') : 'N/A' }}
                                @if($log->created_at)
                                    <small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category:</label>
                            <div>
                                <span class="badge bg-secondary fs-6">{{ $log->category }}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Event Type:</label>
                            <div>
                                <span class="badge fs-6
                                    @if(str_contains(strtolower($log->event_type), 'create')) bg-success
                                    @elseif(str_contains(strtolower($log->event_type), 'update') || str_contains(strtolower($log->event_type), 'edit')) bg-warning
                                    @elseif(str_contains(strtolower($log->event_type), 'delete')) bg-danger
                                    @elseif(str_contains(strtolower($log->event_type), 'assign')) bg-info
                                    @else bg-primary
                                    @endif">
                                    {{ $log->event_type }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">IP Address:</label>
                            <div class="text-muted">
                                {{ $log->ip_address ?: 'Not recorded' }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Related Information -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Related Information</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">User:</label>
                            <div>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($log->user->first_name, 0, 1) . substr($log->user->last_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">System Action</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Asset:</label>
                            <div>
                                @if($log->asset)
                                    <div>
                                        <div class="fw-bold">{{ $log->asset->name }}</div>
                                        <small class="text-muted">Tag: {{ $log->asset->asset_tag }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">No asset associated</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Department:</label>
                            <div>
                                @if($log->department)
                                    <span class="badge bg-info">{{ $log->department->name }}</span>
                                @else
                                    <span class="text-muted">No department associated</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                @if($log->description)
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description:</label>
                        <div class="bg-light p-3 rounded">
                            {{ $log->description }}
                        </div>
                    </div>
                @endif
                
                <!-- Additional Data -->
                @if($log->additional_data)
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Additional Data:</label>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0"><code>{{ json_encode(json_decode($log->additional_data), JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Actions Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                @if($log->user)
                    <a href="{{ route('users.show', $log->user) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="fas fa-user me-1"></i>View User Profile
                    </a>
                @endif
                
                @if($log->asset)
                    <a href="{{ route('assets.show', $log->asset) }}" class="btn btn-outline-success btn-sm w-100 mb-2">
                        <i class="fas fa-laptop me-1"></i>View Asset Details
                    </a>
                @endif
                
                @if($log->department)
                    <a href="{{ route('departments.show', $log->department) }}" class="btn btn-outline-info btn-sm w-100 mb-2">
                        <i class="fas fa-building me-1"></i>View Department
                    </a>
                @endif
                
                <hr>
                
                <a href="{{ route('logs.index', ['user_id' => $log->user_id]) }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                    <i class="fas fa-filter me-1"></i>Filter by This User
                </a>
                
                <a href="{{ route('logs.index', ['category' => $log->category]) }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                    <i class="fas fa-filter me-1"></i>Filter by Category
                </a>
                
                <a href="{{ route('logs.index', ['event_type' => $log->event_type]) }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-filter me-1"></i>Filter by Event Type
                </a>
            </div>
        </div>
        
        <!-- Related Logs Card -->
        @if($relatedLogs && $relatedLogs->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Related Logs</h6>
                </div>
                <div class="card-body">
                    @foreach($relatedLogs as $relatedLog)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $relatedLog->event_type }}</div>
                                <small class="text-muted">{{ $relatedLog->created_at ? $relatedLog->created_at->format('M d, Y H:i') : 'N/A' }}</small>
                                @if($relatedLog->description)
                                    <div class="small text-muted mt-1">{{ Str::limit($relatedLog->description, 50) }}</div>
                                @endif
                            </div>
                            <a href="{{ route('logs.show', $relatedLog) }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    @endforeach
                    
                    @if($log->asset)
                        <div class="text-center">
                            <a href="{{ route('logs.index', ['asset_id' => $log->asset_id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Asset Logs
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Copy additional data to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check me-1"></i>Copied to clipboard!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toast);
            });
        });
    }
    
    // Add click handler for additional data
    document.addEventListener('DOMContentLoaded', function() {
        const additionalDataPre = document.querySelector('pre code');
        if (additionalDataPre) {
            additionalDataPre.style.cursor = 'pointer';
            additionalDataPre.title = 'Click to copy';
            additionalDataPre.addEventListener('click', function() {
                copyToClipboard(this.textContent);
            });
        }
    });
</script>
@endsection