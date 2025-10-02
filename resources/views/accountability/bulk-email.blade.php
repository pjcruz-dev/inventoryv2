@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">Bulk Send Email with Signed Forms</h5>
                            <small class="text-white-50">Send emails with signed accountability forms to multiple recipients</small>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('accountability.index') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                <i class="fas fa-arrow-left me-1"></i>Back to Accountability
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($assets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No assets with signed forms found. Please upload signed forms first before sending bulk emails.
                        </div>
                    @else
                        <form action="{{ route('accountability.bulk-email.send') }}" method="POST" id="bulkEmailForm">
                            @csrf
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                                <ul class="mb-0">
                                    <li>Select the assets you want to include in the bulk email</li>
                                    <li>Enter recipient email addresses (comma-separated)</li>
                                    <li>Customize the email subject and description if needed</li>
                                    <li>Each selected asset will send an individual email with its signed form attached</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recipients" class="form-label">Recipients <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('recipients') is-invalid @enderror" 
                                               id="recipients" name="recipients" 
                                               value="{{ $defaultRecipients }}"
                                               placeholder="email1@example.com, email2@example.com" required>
                                        @error('recipients')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Separate multiple emails with commas</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Email Subject</label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                               id="subject" name="subject" 
                                               value="{{ $defaultSubject }}"
                                               placeholder="Asset Accountability Forms - Confirmed & Signed">
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Leave blank to use default subject</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Description field removed - each recipient gets personalized description automatically -->

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6>Select Assets to Include</h6>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                            <i class="fas fa-check-square me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="selectNone">
                                            <i class="fas fa-square me-1"></i>Select None
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Global Search -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="globalSearch" 
                                               placeholder="Search by asset tag, name, assigned user, or category...">
                                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">
                                        <span id="searchResults">Showing all {{ $assets->count() }} assets</span>
                                    </small>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="5%">
                                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                                </th>
                                                <th width="15%">Asset Tag</th>
                                                <th width="20%">Asset Name</th>
                                                <th width="20%">Assigned To</th>
                                                <th width="15%">Category</th>
                                                <th width="15%">Upload Date</th>
                                                <th width="10%">Email Count</th>
                                                <th width="10%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assets as $asset)
                                            <tr data-email="{{ $asset->assignedUser->email }}">
                                                <td>
                                                    <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" 
                                                           class="form-check-input asset-checkbox">
                                                </td>
                                                <td><strong>{{ $asset->asset_tag }}</strong></td>
                                                <td>{{ $asset->name }}</td>
                                                <td>{{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</td>
                                                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($asset->currentAssignment && $asset->currentAssignment->signed_form_uploaded_at)
                                                        {{ $asset->currentAssignment->signed_form_uploaded_at->format('M j, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($asset->currentAssignment && $asset->currentAssignment->signed_form_email_count > 0)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-paper-plane me-1"></i>
                                                            {{ $asset->currentAssignment->signed_form_email_count }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-minus me-1"></i>
                                                            0
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Ready</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('accountability.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="sendBtn">
                                    <i class="fas fa-paper-plane me-1"></i>Send Emails
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAll');
    const selectNoneBtn = document.getElementById('selectNone');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    const sendBtn = document.getElementById('sendBtn');
    const form = document.getElementById('bulkEmailForm');

    // Select all functionality
    selectAllBtn.addEventListener('click', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        selectAllCheckbox.checked = true;
        updateSendButton();
         updateRecipients();
    });

    // Select none functionality
    selectNoneBtn.addEventListener('click', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        updateSendButton();
        updateRecipients();
    });

    // Select all checkbox functionality
    selectAllCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSendButton();
        updateRecipients();
    });

    // Individual checkbox functionality
    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateSendButton();
            updateRecipients();
        });
    });

    function updateSelectAllCheckbox() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === assetCheckboxes.length;
    }

    function updateSendButton() {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        if (checkedCount > 0) {
            sendBtn.innerHTML = `<i class="fas fa-paper-plane me-1"></i>Send Emails (${checkedCount})`;
            sendBtn.disabled = false;
        } else {
            sendBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send Emails';
            sendBtn.disabled = true;
        }
    }

    function updateRecipients() {
        const checkedCheckboxes = document.querySelectorAll('.asset-checkbox:checked');
        const emails = [];
        const selectedAssets = [];
        
        checkedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const email = row.getAttribute('data-email');
            const assetTag = row.querySelector('td:nth-child(2)').textContent.trim();
            const userName = row.querySelector('td:nth-child(4)').textContent.trim();
            
            if (email && !emails.includes(email)) {
                emails.push(email);
            }
            
            selectedAssets.push({
                assetTag: assetTag,
                userName: userName,
                email: email
            });
        });
        
        const recipientsField = document.getElementById('recipients');
        recipientsField.value = emails.join(', ');
        
        // Description field removed - each recipient gets personalized description automatically
    }

    // updateDescription function removed - each recipient gets personalized description automatically

    // Handle form submission
    form.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.asset-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one asset to send emails for.');
            return;
        }

        // Debug: Log selected assets
        const selectedAssets = [];
        document.querySelectorAll('.asset-checkbox:checked').forEach(checkbox => {
            const row = checkbox.closest('tr');
            const assetTag = row.querySelector('td:nth-child(2)').textContent.trim();
            const userEmail = row.getAttribute('data-email');
            selectedAssets.push({
                id: checkbox.value,
                asset_tag: assetTag,
                email: userEmail
            });
        });
        
        console.log('Selected assets for bulk email:', selectedAssets);
        console.log('Recipients field value:', document.getElementById('recipients').value);

        // Show loading state
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
        sendBtn.disabled = true;
    });

    // Initialize button state
    updateSendButton();

    // Global Search Functionality
    const globalSearch = document.getElementById('globalSearch');
    const clearSearch = document.getElementById('clearSearch');
    const searchResults = document.getElementById('searchResults');
    const tableRows = document.querySelectorAll('tbody tr');

    globalSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
            const assetTag = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const assetName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const assignedTo = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const category = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            const matches = assetTag.includes(searchTerm) || 
                          assetName.includes(searchTerm) || 
                          assignedTo.includes(searchTerm) || 
                          category.includes(searchTerm);

            if (matches || searchTerm === '') {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update search results text
        if (searchTerm === '') {
            searchResults.textContent = `Showing all ${tableRows.length} assets`;
        } else {
            searchResults.textContent = `Showing ${visibleCount} of ${tableRows.length} assets`;
        }

        // Update recipients based on visible selected assets
        updateRecipients();
    });

    // Clear search functionality
    clearSearch.addEventListener('click', function() {
        globalSearch.value = '';
        globalSearch.dispatchEvent(new Event('input'));
    });

    // Handle Enter key in search
    globalSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});
</script>
@endpush
