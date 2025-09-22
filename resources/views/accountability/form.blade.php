@extends('layouts.app')

@section('title', 'Accountability Form - ' . $formData['asset']->asset_tag)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-file-contract text-primary me-2"></i>
                        Asset Accountability Form
                    </h2>
                    <p class="text-muted mb-0">Form ID: {{ $formData['form_id'] }} • Generated on {{ $formData['generated_at']->format('F d, Y \a\t g:i A') }}</p>
                </div>
                <div>
                    <a href="{{ route('accountability.print', $formData['asset']->id) }}" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-1"></i>
                        Print Form
                    </a>
                    <a href="{{ route('accountability.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="card">
        <div class="card-body">
            <!-- Asset Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-desktop me-2"></i>
                        Asset Information
                    </h5>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Asset Tag</div>
                        <div class="info-value">{{ $formData['asset']->asset_tag }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Asset Name</div>
                        <div class="info-value">{{ $formData['asset']->name }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Serial Number</div>
                        <div class="info-value">{{ $formData['asset']->serial_number }}</div>
                    </div>
                </div>
                @if($formData['asset']->category && strtolower($formData['asset']->category->name) == 'mobile devices' && $formData['asset']->mobile_number)
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Mobile Number</div>
                        <div class="info-value">{{ $formData['asset']->mobile_number }}</div>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Category</div>
                        <div class="info-value">{{ $formData['asset']->category->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Vendor</div>
                        <div class="info-value">{{ $formData['asset']->vendor->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge bg-{{ $formData['asset']->status == 'Active' ? 'success' : ($formData['asset']->status == 'Assigned' ? 'primary' : 'warning') }}">
                                {{ $formData['asset']->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Assignment -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2"></i>
                        Current Assignment
                    </h5>
                </div>
                @if($formData['asset']->assignedUser)
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Assigned To</div>
                            <div class="info-value">
                                {{ $formData['asset']->assignedUser->first_name }} {{ $formData['asset']->assignedUser->last_name }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $formData['asset']->assignedUser->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $formData['asset']->assignedUser->department->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Assignment Date</div>
                            <div class="info-value">{{ $formData['asset']->assigned_date ? $formData['asset']->assigned_date->format('M d, Y g:i A') : 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <div class="info-label">Role</div>
                            <div class="info-value">{{ $formData['asset']->assignedUser->role->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This asset is not currently assigned to any user.
                        </div>
                    </div>
                @endif
            </div>

            <!-- Assignment History -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-history me-2"></i>
                        Assignment History
                    </h5>
                    @if($formData['assignments']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm" style="border: 2px solid #000;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Assigned To</th>
                                        <th>Assigned By</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formData['assignments'] as $assignment)
                                        <tr>
                                            <td>{{ $assignment->assigned_date->format('M d, Y g:i A') }}</td>
                                            <td>{{ $assignment->user->first_name }} {{ $assignment->user->last_name }}</td>
                                            <td>{{ $assignment->assignedBy->first_name ?? 'System' }} {{ $assignment->assignedBy->last_name ?? '' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $assignment->status == 'confirmed' ? 'success' : ($assignment->status == 'declined' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($assignment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $assignment->notes ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No assignment history found for this asset.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Confirmation History -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Confirmation History
                    </h5>
                    @if($formData['confirmations']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm" style="border: 2px solid #000;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Status</th>
                                        <th>Response Time</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formData['confirmations'] as $confirmation)
                                        <tr>
                                            <td>{{ $confirmation->created_at->format('M d, Y g:i A') }}</td>
                                            <td>{{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $confirmation->status == 'confirmed' ? 'success' : ($confirmation->status == 'declined' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($confirmation->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($confirmation->confirmed_at || $confirmation->declined_at)
                                                    {{ $confirmation->created_at->diffForHumans($confirmation->confirmed_at ?? $confirmation->declined_at) }}
                                                @else
                                                    Pending
                                                @endif
                                            </td>
                                            <td>
                                                @if($confirmation->decline_reason)
                                                    Reason: {{ $confirmation->getFormattedDeclineReason() }}
                                                @else
                                                    Confirmed via email
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No confirmation history found for this asset.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-timeline me-2"></i>
                        Activity Timeline
                    </h5>
                    @if($formData['timeline']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm" style="border: 2px solid #000;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Action</th>
                                        <th>From User</th>
                                        <th>To User</th>
                                        <th>Performed By</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formData['timeline']->take(10) as $entry)
                                        <tr>
                                            <td>{{ $entry->performed_at->format('M d, Y g:i A') }}</td>
                                            <td>
                                                <span class="badge bg-{{ strtolower($entry->action) == 'assigned' ? 'primary' : (strtolower($entry->action) == 'confirmed' ? 'success' : 'warning') }}">
                                                    {{ ucfirst($entry->action) }}
                                                </span>
                                            </td>
                                            <td>{{ $entry->fromUser->first_name ?? 'N/A' }} {{ $entry->fromUser->last_name ?? '' }}</td>
                                            <td>{{ $entry->toUser->first_name ?? 'N/A' }} {{ $entry->toUser->last_name ?? '' }}</td>
                                            <td>{{ $entry->performedBy->first_name ?? 'System' }} {{ $entry->performedBy->last_name ?? '' }}</td>
                                            <td>{{ $entry->notes ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No timeline entries found for this asset.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Signatures -->
            <div class="row">
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-signature me-2"></i>
                        Signatures & Approvals
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="border-bottom border-dark mb-2" style="height: 2px; width: 200px; margin: 0 auto;"></div>
                                <div class="fw-bold">{{ $formData['asset']->assignedUser->first_name ?? 'N/A' }} {{ $formData['asset']->assignedUser->last_name ?? '' }}</div>
                                <div class="text-muted small">Asset Custodian</div>
                                <div class="text-muted small">Signature & Date</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="border-bottom border-dark mb-2" style="height: 2px; width: 200px; margin: 0 auto;"></div>
                                <div class="fw-bold">{{ $formData['generated_by']->first_name }} {{ $formData['generated_by']->last_name }}</div>
                                <div class="text-muted small">IT Administrator</div>
                                <div class="text-muted small">Signature & Date</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="border-bottom border-dark mb-2" style="height: 2px; width: 200px; margin: 0 auto;"></div>
                                <div class="fw-bold">________________________</div>
                                <div class="text-muted small">ICT Director</div>
                                <div class="text-muted small">Signature & Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    border: 2px solid #000;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    background: #f8f9fa;
}

.info-label {
    font-weight: bold;
    color: #495057;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.info-value {
    color: #212529;
    font-size: 14px;
}
</style>
@endsection
