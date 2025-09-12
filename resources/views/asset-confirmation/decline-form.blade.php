<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Assignment - Not Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .decline-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }
        .asset-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .btn-submit {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
            color: white;
        }
        .btn-back {
            background: #6c757d;
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        .user-info {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .icon {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="decline-container">
        <div class="card">
            <div class="card-header text-center">
                <h2><i class="fas fa-exclamation-triangle me-2"></i>Asset Not Received</h2>
                <p class="mb-0">Please provide details about why you haven't received the asset</p>
            </div>
            <div class="card-body p-4">
                <div class="user-info">
                    <h5><i class="fas fa-user icon"></i>{{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}</h5>
                    <p class="mb-0"><i class="fas fa-envelope icon"></i>{{ $confirmation->user->email }}</p>
                </div>

                <div class="asset-summary">
                    <h6><i class="fas fa-laptop icon"></i>Asset: <strong>{{ $confirmation->asset->asset_tag }}</strong> - {{ $confirmation->asset->asset_name }}</h6>
                    <small class="text-muted">Assigned on: {{ $confirmation->assigned_at->format('F j, Y') }}</small>
                </div>

                <form action="{{ route('asset-confirmation.process-decline', $confirmation->confirmation_token) }}" method="POST">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="reason" class="form-label">
                            <i class="fas fa-comment-alt"></i> Reason for Declining *
                        </label>
                        <select class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                            <option value="">Please select a reason...</option>
                            
                            <optgroup label="Delivery Issues">
                                <option value="never_delivered" {{ old('reason') == 'never_delivered' ? 'selected' : '' }}>Asset was never delivered to me</option>
                                <option value="delivery_location" {{ old('reason') == 'delivery_location' ? 'selected' : '' }}>Delivered to wrong location</option>
                                <option value="incomplete_delivery" {{ old('reason') == 'incomplete_delivery' ? 'selected' : '' }}>Incomplete delivery (missing items/accessories)</option>
                            </optgroup>
                            
                            <optgroup label="Asset Issues">
                                <option value="wrong_asset" {{ old('reason') == 'wrong_asset' ? 'selected' : '' }}>This is not the correct asset for me</option>
                                <option value="damaged_asset" {{ old('reason') == 'damaged_asset' ? 'selected' : '' }}>Asset appears to be damaged</option>
                                <option value="incompatible_asset" {{ old('reason') == 'incompatible_asset' ? 'selected' : '' }}>Asset is incompatible with my requirements</option>
                            </optgroup>
                            
                            <optgroup label="Personal Reasons">
                                <option value="no_longer_needed" {{ old('reason') == 'no_longer_needed' ? 'selected' : '' }}>I no longer need this asset</option>
                                <option value="personal_preference" {{ old('reason') == 'personal_preference' ? 'selected' : '' }}>Personal preference for different asset</option>
                                <option value="temporary_unavailable" {{ old('reason') == 'temporary_unavailable' ? 'selected' : '' }}>I'm temporarily unavailable to receive it</option>
                            </optgroup>
                            
                            <optgroup label="Technical Issues">
                                <option value="technical_problems" {{ old('reason') == 'technical_problems' ? 'selected' : '' }}>Technical problems with the asset</option>
                                <option value="software_incompatibility" {{ old('reason') == 'software_incompatibility' ? 'selected' : '' }}>Software incompatibility issues</option>
                            </optgroup>
                            
                            <optgroup label="Other">
                                <option value="other_reason" {{ old('reason') == 'other_reason' ? 'selected' : '' }}>Other reason (please specify below)</option>
                            </optgroup>
                        </select>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="comments" class="form-label">
                            <i class="fas fa-comment icon"></i>Additional Comments
                        </label>
                        <textarea class="form-control @error('comments') is-invalid @enderror" id="comments" name="comments" rows="4" 
                                  maxlength="1000"
                                  placeholder="Please provide any additional details that might help us resolve this issue...">{{ old('comments') }}</textarea>
                        <small class="form-text text-muted">Optional: Provide more details about the situation (max 1000 characters)</small>
                        @error('comments')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_preference" class="form-label">
                            <i class="fas fa-phone icon"></i>Preferred Contact Method
                        </label>
                        <select class="form-control @error('contact_preference') is-invalid @enderror" id="contact_preference" name="contact_preference">
                            <option value="email" {{ old('contact_preference', 'email') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('contact_preference') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="in_person" {{ old('contact_preference') == 'in_person' ? 'selected' : '' }}>In Person</option>
                        </select>
                        @error('contact_preference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">How would you prefer to be contacted for follow-up?</small>
                    </div>

                    <div class="form-group" id="follow-up-section" style="display: none;">
                        <label class="form-label">
                            <i class="fas fa-tasks icon"></i>Additional Follow-up Actions Needed
                        </label>
                        <div class="follow-up-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="follow_up_actions[]" value="Schedule replacement delivery" id="action1">
                                <label class="form-check-label" for="action1">Schedule replacement delivery</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="follow_up_actions[]" value="Arrange asset pickup" id="action2">
                                <label class="form-check-label" for="action2">Arrange asset pickup</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="follow_up_actions[]" value="Technical support consultation" id="action3">
                                <label class="form-check-label" for="action3">Technical support consultation</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="follow_up_actions[]" value="Asset requirement review" id="action4">
                                <label class="form-check-label" for="action4">Asset requirement review</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Select any additional actions you think might be needed</small>
                    </div>

                    <div class="form-group" id="follow-up-date-section" style="display: none;">
                        <label for="follow_up_date" class="form-label">
                            <i class="fas fa-calendar icon"></i>Preferred Follow-up Date
                        </label>
                        <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" 
                               id="follow_up_date" name="follow_up_date" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               value="{{ old('follow_up_date') }}">
                        @error('follow_up_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">When would be a good time for us to follow up?</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What happens next?</strong><br>
                        After you submit this form, the IT department will be notified and will contact you to resolve the issue. 
                        The asset status will be updated accordingly.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-paper-plane me-2"></i>Submit Report
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ url('/asset-confirmation/show/' . $confirmation->confirmation_token) }}" class="btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Back to Confirmation
                    </a>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        This information will be used to resolve the asset delivery issue. All reports are confidential.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-resize textarea
        document.getElementById('comments').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Show/hide follow-up sections based on decline reason
        document.getElementById('reason').addEventListener('change', function() {
            const reason = this.value;
            const followUpSection = document.getElementById('follow-up-section');
            const followUpDateSection = document.getElementById('follow-up-date-section');
            
            // Reasons that require follow-up
            const followUpReasons = [
                'never_delivered', 
                'wrong_asset', 
                'damaged_asset', 
                'incomplete_delivery', 
                'delivery_location',
                'technical_problems',
                'software_incompatibility'
            ];
            
            if (followUpReasons.includes(reason)) {
                followUpSection.style.display = 'block';
                followUpDateSection.style.display = 'block';
            } else {
                followUpSection.style.display = 'none';
                followUpDateSection.style.display = 'none';
                // Clear follow-up selections when hidden
                const checkboxes = followUpSection.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = false);
                document.getElementById('follow_up_date').value = '';
            }
        });

        // Character counter for comments
        document.getElementById('comments').addEventListener('input', function() {
            const maxLength = 1000;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            let counter = document.getElementById('char-counter');
            if (!counter) {
                counter = document.createElement('small');
                counter.id = 'char-counter';
                counter.className = 'form-text';
                this.parentNode.appendChild(counter);
            }
            
            counter.textContent = `${remaining} characters remaining`;
            counter.className = remaining < 100 ? 'form-text text-warning' : 'form-text text-muted';
        });

        // Trigger change event on page load to handle old values
        document.addEventListener('DOMContentLoaded', function() {
            const reasonSelect = document.getElementById('reason');
            if (reasonSelect.value) {
                reasonSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>