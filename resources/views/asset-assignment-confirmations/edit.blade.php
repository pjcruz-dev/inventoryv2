@extends('layouts.app')

@section('title', 'Edit Asset Assignment Confirmation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Asset Assignment Confirmation
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-assignment-confirmations.update', $confirmation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Asset Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select name="asset_id" id="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                        <option value="">Select Asset</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" 
                                                {{ (old('asset_id', $confirmation->asset_id) == $asset->id) ? 'selected' : '' }}>
                                                {{ $asset->name }} ({{ $asset->asset_tag }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- User Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ (old('user_id', $confirmation->user_id) == $user->id) ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Assigned Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assigned_at" class="form-label">Assigned Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="assigned_at" id="assigned_at" 
                                           class="form-control @error('assigned_at') is-invalid @enderror" 
                                           value="{{ old('assigned_at', $confirmation->assigned_at ? $confirmation->assigned_at->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('assigned_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Expected Return Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_return_date" class="form-label">Expected Return Date</label>
                                    <input type="date" name="expected_return_date" id="expected_return_date" 
                                           class="form-control @error('expected_return_date') is-invalid @enderror" 
                                           value="{{ old('expected_return_date', $confirmation->expected_return_date ? $confirmation->expected_return_date->format('Y-m-d') : '') }}">
                                    @error('expected_return_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $confirmation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ old('status', $confirmation->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="declined" {{ old('status', $confirmation->status) == 'declined' ? 'selected' : '' }}>Declined</option>
                                        <option value="expired" {{ old('status', $confirmation->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
                                        <option value="low" {{ old('priority', $confirmation->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $confirmation->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $confirmation->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $confirmation->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Dates -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmed_at" class="form-label">Confirmed At</label>
                                    <input type="datetime-local" name="confirmed_at" id="confirmed_at" 
                                           class="form-control @error('confirmed_at') is-invalid @enderror" 
                                           value="{{ old('confirmed_at', $confirmation->confirmed_at ? $confirmation->confirmed_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('confirmed_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty for pending confirmations</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="declined_at" class="form-label">Declined At</label>
                                    <input type="datetime-local" name="declined_at" id="declined_at" 
                                           class="form-control @error('declined_at') is-invalid @enderror" 
                                           value="{{ old('declined_at', $confirmation->declined_at ? $confirmation->declined_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('declined_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty for non-declined confirmations</small>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Add any additional notes or instructions...">{{ old('notes', $confirmation->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Decline Reason (if applicable) -->
                        @if($confirmation->status == 'declined' || old('status') == 'declined')
                        <div class="mb-3" id="decline-reason-section">
                            <label for="decline_reason" class="form-label">Decline Reason</label>
                            <textarea name="decline_reason" id="decline_reason" rows="3" 
                                      class="form-control @error('decline_reason') is-invalid @enderror" 
                                      placeholder="Reason for declining the assignment...">{{ old('decline_reason', $confirmation->decline_reason) }}</textarea>
                            @error('decline_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Confirmation History -->
                        @if($confirmation->reminder_count > 0 || $confirmation->confirmed_at || $confirmation->declined_at)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Confirmation History</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h4 text-info">{{ $confirmation->reminder_count ?? 0 }}</div>
                                            <small class="text-muted">Reminders Sent</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h4 text-{{ $confirmation->confirmed_at ? 'success' : 'muted' }}">
                                                {{ $confirmation->confirmed_at ? $confirmation->confirmed_at->format('M d, Y') : 'Not Confirmed' }}
                                            </div>
                                            <small class="text-muted">Confirmation Date</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <div class="h4 text-{{ $confirmation->declined_at ? 'danger' : 'muted' }}">
                                                {{ $confirmation->declined_at ? $confirmation->declined_at->format('M d, Y') : 'Not Declined' }}
                                            </div>
                                            <small class="text-muted">Decline Date</small>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($confirmation->confirmation_token)
                                <div class="mt-3">
                                    <label class="form-label">Confirmation Token</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $confirmation->confirmation_token }}" readonly>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $confirmation->confirmation_token }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('asset-assignment-confirmations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                            <div>
                                @if($confirmation->status == 'pending')
                                    @can('manage_assignment_confirmations')
                                    <a href="{{ route('asset-assignment-confirmations.send-reminder', $confirmation) }}" 
                                       class="btn btn-outline-warning me-2">
                                        <i class="fas fa-bell me-1"></i>Send Reminder
                                    </a>
                                    @endcan
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Confirmation
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validate dates
    $('#expected_return_date').on('change', function() {
        const assignedDate = new Date($('#assigned_at').val());
        const returnDate = new Date(this.value);
        
        if (assignedDate && returnDate && returnDate <= assignedDate) {
            alert('Expected return date must be after the assigned date.');
            this.value = '';
        }
    });

    // Handle status changes
    $('#status').on('change', function() {
        const status = this.value;
        const declineSection = $('#decline-reason-section');
        
        // Show/hide decline reason section
        if (status === 'declined') {
            if (declineSection.length === 0) {
                const declineHtml = `
                    <div class="mb-3" id="decline-reason-section">
                        <label for="decline_reason" class="form-label">Decline Reason</label>
                        <textarea name="decline_reason" id="decline_reason" rows="3" 
                                  class="form-control" 
                                  placeholder="Reason for declining the assignment..."></textarea>
                    </div>
                `;
                $('#notes').closest('.mb-3').after(declineHtml);
            } else {
                declineSection.show();
            }
            
            // Auto-set declined_at if not set
            if (!$('#declined_at').val()) {
                $('#declined_at').val(new Date().toISOString().slice(0, 16));
            }
            $('#confirmed_at').val('');
        } else if (status === 'confirmed') {
            declineSection.hide();
            
            // Auto-set confirmed_at if not set
            if (!$('#confirmed_at').val()) {
                $('#confirmed_at').val(new Date().toISOString().slice(0, 16));
            }
            $('#declined_at').val('');
        } else {
            declineSection.hide();
            $('#confirmed_at').val('');
            $('#declined_at').val('');
        }
    });

    // Initialize Select2 for better dropdowns
    if (typeof $.fn.select2 !== 'undefined') {
        $('#asset_id, #user_id').select2({
            theme: 'bootstrap-5',
            placeholder: function() {
                return $(this).data('placeholder');
            }
        });
    }
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">Token copied to clipboard!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    });
}
</script>
@endpush