@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">Bulk Upload Signed Forms</h5>
                            <small class="text-white-50">Upload multiple signed accountability forms at once</small>
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
                            No assigned assets found. Please ensure you have assets assigned to users before uploading signed forms.
                        </div>
                    @else
                        <form action="{{ route('accountability.bulk-upload.store') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                            @csrf
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                                <ul class="mb-0">
                                    <li>Select multiple PDF files to upload</li>
                                    <li>Each file will be matched to an asset based on the order you select them</li>
                                    <li>Files must be in PDF format and under 10MB each</li>
                                    <li>Make sure to select files in the same order as the assets listed below</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="signed_forms" class="form-label">Select PDF Files <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('signed_forms') is-invalid @enderror" 
                                               id="signed_forms" name="signed_forms[]" multiple accept=".pdf" required>
                                        @error('signed_forms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple files</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Selected Files</label>
                                        <div id="fileList" class="border rounded p-2" style="min-height: 100px; background-color: #f8f9fa;">
                                            <small class="text-muted">No files selected</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6>Assets Available for Upload</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Asset Tag</th>
                                                <th width="20%">Asset Name</th>
                                                <th width="20%">Assigned To</th>
                                                <th width="15%">Category</th>
                                                <th width="15%">Status</th>
                                                <th width="10%">Current Form</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assets as $index => $asset)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $asset->asset_tag }}</strong></td>
                                                <td>{{ $asset->name }}</td>
                                                <td>{{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</td>
                                                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ $asset->status }}</span>
                                                </td>
                                                <td>
                                                    @if($asset->currentAssignment && $asset->currentAssignment->signed_form_path)
                                                        <span class="badge bg-info">Uploaded</span>
                                                    @else
                                                        <span class="badge bg-secondary">Not Uploaded</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Hidden asset IDs -->
                            @foreach($assets as $asset)
                                <input type="hidden" name="asset_ids[]" value="{{ $asset->id }}">
                            @endforeach

                            <!-- Progress Bar -->
                            <div class="mb-3" id="progressContainer" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Upload Progress</span>
                                    <span id="progressText">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted" id="progressDetails">Ready to upload</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('accountability.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <div>
                                    <button type="button" class="btn btn-outline-primary me-2" id="startSessionBtn">
                                        <i class="fas fa-play me-1"></i>Start Session
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="uploadBtn" style="display: none;">
                                        <i class="fas fa-upload me-1"></i>Upload Files
                                    </button>
                                    <button type="button" class="btn btn-success" id="completeBtn" style="display: none;">
                                        <i class="fas fa-check me-1"></i>Complete Upload
                                    </button>
                                </div>
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
    // Only run if there are assets (form exists)
    @if(!$assets->isEmpty())
    const fileInput = document.getElementById('signed_forms');
    const fileList = document.getElementById('fileList');
    const uploadBtn = document.getElementById('uploadBtn');
    const form = document.getElementById('bulkUploadForm');

    // Check if all required elements exist
    if (!fileInput || !fileList || !uploadBtn || !form) {
        console.error('Required elements not found:', {
            fileInput: !!fileInput,
            fileList: !!fileList,
            uploadBtn: !!uploadBtn,
            form: !!form
        });
        return;
    }

    // Store files globally for reordering
    let selectedFiles = [];
    let currentSession = null;
    let uploadInProgress = false;

    // Handle file selection
    fileInput.addEventListener('change', function() {
        selectedFiles = Array.from(this.files);
        renderFileList();
    });

    // Start session button
    const startSessionBtn = document.getElementById('startSessionBtn');
    startSessionBtn.addEventListener('click', function() {
        startUploadSession();
    });

    // Complete button
    const completeBtn = document.getElementById('completeBtn');
    completeBtn.addEventListener('click', function() {
        if (currentSession) {
            window.location.href = "{{ route('accountability.index') }}?session_completed=" + currentSession.session_id;
        }
    });

    // Render the file list with drag and drop functionality
    function renderFileList() {
        fileList.innerHTML = '';

        if (selectedFiles.length === 0) {
            fileList.innerHTML = '<small class="text-muted">No files selected</small>';
            return;
        }

        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'd-flex justify-content-between align-items-center mb-1 p-2 border rounded file-item';
            fileItem.draggable = true;
            fileItem.dataset.index = index;
            fileItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-grip-vertical text-muted me-2 drag-handle" style="cursor: grab;"></i>
                    <i class="fas fa-file-pdf text-danger me-2"></i>
                    <div>
                        <span class="fw-bold">${file.name}</span>
                        <small class="text-muted d-block">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    </div>
                </div>
                <span class="badge bg-primary">${index + 1}</span>
            `;

            // Add drag event listeners
            fileItem.addEventListener('dragstart', handleDragStart);
            fileItem.addEventListener('dragover', handleDragOver);
            fileItem.addEventListener('drop', handleDrop);
            fileItem.addEventListener('dragend', handleDragEnd);

            fileList.appendChild(fileItem);
        });

        // Show warning if file count doesn't match asset count
        const assetCount = {{ $assets->count() }};
        if (selectedFiles.length !== assetCount) {
            const warning = document.createElement('div');
            warning.className = 'alert alert-warning mt-2 mb-0';
            warning.innerHTML = `
                <i class="fas fa-exclamation-triangle me-1"></i>
                <strong>Warning:</strong> You selected ${selectedFiles.length} file(s) but there are ${assetCount} assets. 
                Make sure the files are in the correct order.
            `;
            fileList.appendChild(warning);
        }
    }

    // Drag and drop handlers
    let draggedElement = null;

    function handleDragStart(e) {
        draggedElement = this;
        this.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        this.classList.add('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        if (draggedElement !== this) {
            const draggedIndex = parseInt(draggedElement.dataset.index);
            const targetIndex = parseInt(this.dataset.index);
            
            // Swap files in the array
            [selectedFiles[draggedIndex], selectedFiles[targetIndex]] = [selectedFiles[targetIndex], selectedFiles[draggedIndex]];
            
            // Re-render the list
            renderFileList();
        }
    }

    function handleDragEnd(e) {
        this.style.opacity = '';
        document.querySelectorAll('.file-item').forEach(item => {
            item.classList.remove('drag-over');
        });
    }

    // Add CSS for drag and drop
    const style = document.createElement('style');
    style.textContent = `
        .file-item {
            transition: all 0.2s ease;
            cursor: move;
        }
        .file-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .file-item.drag-over {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .drag-handle:hover {
            color: #007bff !important;
        }
    `;
    document.head.appendChild(style);

    // Start upload session
    async function startUploadSession() {
        if (selectedFiles.length === 0) {
            alert('Please select at least one file to upload.');
            return;
        }

        try {
            const assetIds = Array.from(document.querySelectorAll('input[name="asset_ids[]"]')).map(input => input.value);
            
            const response = await fetch("{{ route('accountability.start-session') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ asset_ids: assetIds })
            });

            const data = await response.json();
            
            if (data.success) {
                currentSession = data;
                showProgress();
                showUploadButton();
                updateProgress(0, 'Session started. Ready to upload files.');
            } else {
                alert('Failed to start upload session: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error starting session:', error);
            alert('Error starting upload session. Please try again.');
        }
    }

    // Upload files to session
    async function uploadFilesToSession() {
        if (!currentSession || uploadInProgress) return;

        uploadInProgress = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
        uploadBtn.disabled = true;

        try {
            const formData = new FormData();
            
            // Add files
            selectedFiles.forEach((file, index) => {
                formData.append('files[]', file);
                formData.append('asset_indices[]', index);
            });

            // Debug: Log what we're sending
            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            const uploadUrl = `/accountability/upload-to-session/${currentSession.session_id}`;
            console.log('Upload URL:', uploadUrl);
            console.log('Session ID:', currentSession.session_id);
            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                currentSession = data;
                updateProgress(data.progress_percentage, `Uploaded ${data.total_uploaded} of ${data.total_files} files`);
                
                if (data.is_completed) {
                    showCompleteButton();
                    updateProgress(100, 'All files uploaded successfully!');
                } else {
                    // Reset for next batch
                    selectedFiles = [];
                    fileInput.value = '';
                    renderFileList();
                }
            } else {
                console.error('Upload failed:', data);
                alert('Upload failed: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error uploading files:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            alert('Error uploading files: ' + error.message);
        } finally {
            uploadInProgress = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i>Upload Files';
            uploadBtn.disabled = false;
        }
    }

    // Show progress bar
    function showProgress() {
        document.getElementById('progressContainer').style.display = 'block';
    }

    // Update progress
    function updateProgress(percentage, details) {
        document.getElementById('progressBar').style.width = percentage + '%';
        document.getElementById('progressText').textContent = percentage + '%';
        document.getElementById('progressDetails').textContent = details;
    }

    // Show upload button
    function showUploadButton() {
        document.getElementById('startSessionBtn').style.display = 'none';
        document.getElementById('uploadBtn').style.display = 'inline-block';
    }

    // Show complete button
    function showCompleteButton() {
        document.getElementById('uploadBtn').style.display = 'none';
        document.getElementById('completeBtn').style.display = 'inline-block';
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (selectedFiles.length === 0) {
            alert('Please select at least one file to upload.');
            return;
        }

        if (!currentSession) {
            alert('Please start an upload session first.');
            return;
        }

        uploadFilesToSession();
    });
    @endif
});
</script>
@endpush
