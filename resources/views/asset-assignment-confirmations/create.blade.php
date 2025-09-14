@extends('layouts.app')

@section('title', 'Create Asset Assignment Confirmation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create Asset Assignment Confirmation
                    </h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('asset-assignment-confirmations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Asset Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                    <select name="asset_id" id="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                        <option value="">Select Asset</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
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
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                           value="{{ old('assigned_at', now()->format('Y-m-d\TH:i')) }}" required>
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
                                           value="{{ old('expected_return_date') }}">
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
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="declined" {{ old('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                                        <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
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
                                        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Add any additional notes or instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmation Options -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Confirmation Options</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="send_notification" id="send_notification" 
                                                   class="form-check-input" value="1" 
                                                   {{ old('send_notification', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="send_notification">
                                                Send Email Notification
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="require_confirmation" id="require_confirmation" 
                                                   class="form-check-input" value="1" 
                                                   {{ old('require_confirmation', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="require_confirmation">
                                                Require User Confirmation
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="auto_reminder" id="auto_reminder" 
                                                   class="form-check-input" value="1" 
                                                   {{ old('auto_reminder') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_reminder">
                                                Enable Auto Reminders
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="update_asset_status" id="update_asset_status" 
                                                   class="form-check-input" value="1" 
                                                   {{ old('update_asset_status', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="update_asset_status">
                                                Update Asset Status to Assigned
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('asset-assignment-confirmations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Create Confirmation
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
    // Auto-set expected return date to 30 days from assigned date
    $('#assigned_at').on('change', function() {
        const assignedDate = new Date(this.value);
        if (assignedDate) {
            const expectedReturn = new Date(assignedDate);
            expectedReturn.setDate(expectedReturn.getDate() + 30);
            $('#expected_return_date').val(expectedReturn.toISOString().split('T')[0]);
        }
    });

    // Validate dates
    $('#expected_return_date').on('change', function() {
        const assignedDate = new Date($('#assigned_at').val());
        const returnDate = new Date(this.value);
        
        if (assignedDate && returnDate && returnDate <= assignedDate) {
            alert('Expected return date must be after the assigned date.');
            this.value = '';
        }
    });

    // Toggle confirmation options based on status
    $('#status').on('change', function() {
        const status = this.value;
        const confirmationOptions = $('.card:has(#send_notification)');
        
        if (status === 'confirmed' || status === 'declined') {
            $('#require_confirmation').prop('checked', false).prop('disabled', true);
            $('#auto_reminder').prop('checked', false).prop('disabled', true);
        } else {
            $('#require_confirmation').prop('disabled', false);
            $('#auto_reminder').prop('disabled', false);
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
</script>
@endpush