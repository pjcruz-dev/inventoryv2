@extends('layouts.app')

@section('title', 'QR Code Scanner')

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            <span class="d-none d-md-inline">Back to Assets</span>
        </a>
        <button class="btn btn-primary btn-sm" onclick="startScanner()">
            <i class="fas fa-qrcode me-1"></i>
            <span class="d-none d-md-inline">Start Scanner</span>
        </button>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        QR Code Scanner
                    </h5>
                    <p class="text-muted mb-0">Scan QR codes to quickly access asset information</p>
                </div>
                <div class="card-body">
                    <!-- Permissions Policy Warning -->
                    <div id="permissions-warning" class="alert alert-warning d-none" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Camera Access Required
                        </h6>
                        <p class="mb-2">This scanner requires camera access to work properly. If you're having issues:</p>
                        <ul class="mb-2">
                            <li>Make sure you're using HTTPS or localhost</li>
                            <li>Allow camera permissions when prompted</li>
                            <li>Check your browser's site settings if camera access is blocked</li>
                            <li>Try refreshing the page after allowing permissions</li>
                        </ul>
                        <button type="button" class="btn btn-warning btn-sm" onclick="document.getElementById('permissions-warning').classList.add('d-none')">
                            <i class="fas fa-times me-1"></i>Dismiss
                        </button>
                    </div>
                    
                    <!-- Scanner Container -->
                    <div id="scanner-container" class="text-center mb-4">
                        <div id="scanner-placeholder" class="scanner-placeholder">
                            <div class="scanner-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <h5>QR Code Scanner</h5>
                            <p class="text-muted">Click "Start Scanner" to begin scanning QR codes</p>
                            <div class="scanner-features">
                                <div class="feature-item">
                                    <i class="fas fa-mobile-alt text-primary"></i>
                                    <span>Mobile-friendly</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-bolt text-warning"></i>
                                    <span>Instant results</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <span>Secure scanning</span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="scanner-video" class="scanner-video d-none">
                            <video id="video" width="100%" height="300" autoplay muted playsinline></video>
                            <div class="scanner-overlay">
                                <div class="scanner-frame"></div>
                                <div class="scanner-line"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Scanner Controls -->
                    <div id="scanner-controls" class="scanner-controls d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <button id="start-btn" class="btn btn-success btn-lg w-100" onclick="startScanner()">
                                    <i class="fas fa-play me-2"></i>Start Scanner
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button id="stop-btn" class="btn btn-danger btn-lg w-100" onclick="stopScanner()">
                                    <i class="fas fa-stop me-2"></i>Stop Scanner
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Results -->
                    <div id="scan-results" class="scan-results d-none">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Processing QR Code...</h6>
                                    <p class="mb-0">Please wait while we process the scanned data</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Information -->
                    <div id="asset-info" class="asset-info d-none">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Asset Found
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="asset-details">
                                    <!-- Asset details will be populated here -->
                                </div>
                                <div class="mt-3">
                                    <a id="view-asset-btn" href="#" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>View Asset Details
                                    </a>
                                    <button class="btn btn-outline-secondary" onclick="resetScanner()">
                                        <i class="fas fa-redo me-1"></i>Scan Another
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message" class="error-message d-none">
                        <div class="alert alert-danger">
                            <h6 class="mb-1">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Scan Error
                            </h6>
                            <p id="error-text" class="mb-0"></p>
                            <button class="btn btn-outline-danger btn-sm mt-2" onclick="resetScanner()">
                                <i class="fas fa-redo me-1"></i>Try Again
                            </button>
                        </div>
                    </div>

                    <!-- Manual QR Code Input -->
                    <div class="manual-input mt-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-keyboard me-2"></i>
                                    Manual QR Code Input
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">If camera access is not available, you can manually enter the QR code data:</p>
                                <div class="input-group">
                                    <input type="text" 
                                           id="manual-qr-input" 
                                           class="form-control" 
                                           placeholder="Paste QR code data here..."
                                           onkeypress="handleManualInput(event)">
                                    <button class="btn btn-info" onclick="processManualQR()">
                                        <i class="fas fa-search me-1"></i>Process
                                    </button>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    You can copy QR code data from other QR scanner apps or manually type the asset information.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="scanner-instructions mt-4">
                        <h6>How to use the QR Scanner:</h6>
                        <ol class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-mobile-alt text-primary me-2"></i>
                                <strong>Mobile Device:</strong> Use your phone's camera to scan QR codes
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-desktop text-info me-2"></i>
                                <strong>Desktop:</strong> Allow camera access when prompted
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-qrcode text-warning me-2"></i>
                                <strong>Position:</strong> Hold the QR code steady within the scanning frame
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-keyboard text-secondary me-2"></i>
                                <strong>Manual Input:</strong> If camera doesn't work, use the manual input above
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-lightbulb text-success me-2"></i>
                                <strong>Tip:</strong> Ensure good lighting for better scanning results
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.scanner-placeholder {
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 1rem;
    border: 2px dashed #dee2e6;
}

.scanner-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.scanner-features {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 2rem;
}

.feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.feature-item i {
    font-size: 1.5rem;
}

.scanner-video {
    position: relative;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.scanner-frame {
    width: 250px;
    height: 250px;
    border: 3px solid #28a745;
    border-radius: 1rem;
    position: relative;
    animation: pulse 2s infinite;
}

.scanner-line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #28a745, transparent);
    animation: scan 2s linear infinite;
}

@keyframes pulse {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    50% { 
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
}

@keyframes scan {
    0% { transform: translateY(-125px); }
    100% { transform: translateY(125px); }
}

.scanner-controls {
    margin: 2rem 0;
}

.scan-results {
    margin: 2rem 0;
}

.asset-info {
    margin: 2rem 0;
}

.error-message {
    margin: 2rem 0;
}

.scanner-instructions {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    border-left: 4px solid #007bff;
}

.scanner-instructions h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
}

.scanner-instructions li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.scanner-instructions li:last-child {
    border-bottom: none;
}

.manual-input .input-group {
    margin-bottom: 1rem;
}

.manual-input .form-control:focus {
    border-color: #0dcaf0;
    box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .scanner-features {
        flex-direction: column;
        gap: 1rem;
    }
    
    .scanner-frame {
        width: 200px;
        height: 200px;
    }
    
    .scanner-controls .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
// Browser compatibility check
function checkBrowserCompatibility() {
    const issues = [];
    
    // Check for getUserMedia support
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        issues.push('Camera access not supported');
    }
    
    // Check for HTTPS requirement
    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        issues.push('HTTPS required for camera access');
    }
    
    // Check for jsQR library
    if (typeof jsQR === 'undefined') {
        issues.push('QR code library not loaded');
    }
    
    // Check for permissions policy support
    if (navigator.permissions) {
        navigator.permissions.query({ name: 'camera' }).then(permissionStatus => {
            if (permissionStatus.state === 'denied') {
                issues.push('Camera permission denied');
            }
        }).catch(() => {
            // Permissions API not fully supported
        });
    }
    
    return issues;
}

// Show compatibility warnings
document.addEventListener('DOMContentLoaded', function() {
    const issues = checkBrowserCompatibility();
    if (issues.length > 0) {
        console.warn('QR Scanner compatibility issues:', issues);
        
        // Show the permissions warning
        const permissionsWarning = document.getElementById('permissions-warning');
        if (permissionsWarning) {
            permissionsWarning.classList.remove('d-none');
        }
    }
});
</script>
<script>
class QRScanner {
    constructor() {
        this.video = document.getElementById('video');
        this.canvas = document.createElement('canvas');
        this.context = this.canvas.getContext('2d');
        this.stream = null;
        this.scanning = false;
        this.scanInterval = null;
        
        this.initializeElements();
    }
    
    initializeElements() {
        this.placeholder = document.getElementById('scanner-placeholder');
        this.videoContainer = document.getElementById('scanner-video');
        this.controls = document.getElementById('scanner-controls');
        this.results = document.getElementById('scan-results');
        this.assetInfo = document.getElementById('asset-info');
        this.errorMessage = document.getElementById('error-message');
        this.startBtn = document.getElementById('start-btn');
        this.stopBtn = document.getElementById('stop-btn');
    }
    
    async startScanner() {
        try {
            this.showLoading('Starting camera...');
            
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Camera access not supported in this browser. Please use a modern browser or enable HTTPS.');
            }
            
            // Check if we're on HTTPS or localhost
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                throw new Error('Camera access requires HTTPS. Please access this page via HTTPS or localhost.');
            }
            
            // Check permissions policy first
            if (navigator.permissions) {
                try {
                    const permissionStatus = await navigator.permissions.query({ name: 'camera' });
                    if (permissionStatus.state === 'denied') {
                        throw new Error('Camera permission denied. Please allow camera access in your browser settings and refresh the page.');
                    }
                } catch (permError) {
                    console.warn('Could not check camera permissions:', permError);
                }
            }
            
            // Request camera access with multiple fallback strategies
            const constraintOptions = [
                {
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                },
                {
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                },
                {
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                },
                { video: true },
                {
                    video: { 
                        width: { ideal: 320 },
                        height: { ideal: 240 }
                    }
                }
            ];
            
            let success = false;
            let lastError = null;
            
            for (let i = 0; i < constraintOptions.length; i++) {
                try {
                    console.log(`Trying camera constraint ${i + 1}/${constraintOptions.length}:`, constraintOptions[i]);
                    this.stream = await navigator.mediaDevices.getUserMedia(constraintOptions[i]);
                    success = true;
                    console.log('Camera access successful with constraint:', constraintOptions[i]);
                    break;
                } catch (cameraError) {
                    console.warn(`Camera constraint ${i + 1} failed:`, cameraError);
                    lastError = cameraError;
                }
            }
            
            if (!success) {
                throw lastError || new Error('All camera access attempts failed');
            }
            
            this.video.srcObject = this.stream;
            
            // Wait for video to be ready
            this.video.onloadedmetadata = () => {
                this.video.play();
                
                // Show scanner interface
                this.placeholder.classList.add('d-none');
                this.videoContainer.classList.remove('d-none');
                this.controls.classList.remove('d-none');
                this.startBtn.classList.add('d-none');
                this.stopBtn.classList.remove('d-none');
                
                this.scanning = true;
                this.startScanning();
            };
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            let errorMessage = 'Unable to access camera. ';
            let showManualInput = false;
            
            if (error.message.includes('HTTPS')) {
                errorMessage += 'Please access this page via HTTPS or localhost.';
            } else if (error.message.includes('not supported')) {
                errorMessage += 'Please use a modern browser that supports camera access.';
            } else if (error.name === 'NotAllowedError' || error.message.includes('Permission denied')) {
                errorMessage += 'Camera permission denied. Please:';
                errorMessage += '<br>1. Click the camera icon in your browser\'s address bar';
                errorMessage += '<br>2. Select "Allow" for camera access';
                errorMessage += '<br>3. Refresh the page and try again';
                showManualInput = true;
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'No camera found. Please connect a camera and try again.';
                showManualInput = true;
            } else if (error.message.includes('Permissions policy violation')) {
                errorMessage += 'Camera access blocked by browser security policy. Please:';
                errorMessage += '<br>1. Check your browser\'s site settings';
                errorMessage += '<br>2. Allow camera access for this site';
                errorMessage += '<br>3. Try using a different browser or incognito mode';
                showManualInput = true;
            } else {
                errorMessage += 'Please check permissions and try again.';
                showManualInput = true;
            }
            
            this.showError(errorMessage);
            
            // Show manual input option if camera access fails
            if (showManualInput) {
                this.showManualInputOption();
            }
        }
    }
    
    stopScanner() {
        this.scanning = false;
        
        if (this.scanInterval) {
            clearInterval(this.scanInterval);
            this.scanInterval = null;
        }
        
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        this.video.srcObject = null;
        this.resetInterface();
    }
    
    startScanning() {
        this.scanInterval = setInterval(() => {
            if (this.scanning && this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                this.scanFrame();
            }
        }, 100);
    }
    
    scanFrame() {
        this.canvas.width = this.video.videoWidth;
        this.canvas.height = this.video.videoHeight;
        this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        
        const imageData = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            this.processQRCode(code.data);
        }
    }
    
    async processQRCode(qrData) {
        this.scanning = false;
        this.showLoading('Processing QR code...');
        
        try {
            const response = await fetch('{{ route("qr-scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ qr_data: qrData })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAssetInfo(data.asset);
            } else {
                this.showError(data.message || 'Invalid QR code');
            }
            
        } catch (error) {
            console.error('Error processing QR code:', error);
            this.showError('Error processing QR code. Please try again.');
        }
    }
    
    showAssetInfo(asset) {
        this.hideAllMessages();
        
        const assetDetails = document.getElementById('asset-details');
        const viewBtn = document.getElementById('view-asset-btn');
        
        assetDetails.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Asset Tag</h6>
                    <p class="h5 text-primary mb-3">${asset.asset_tag}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Status</h6>
                    <span class="badge bg-${this.getStatusColor(asset.status)} mb-3">${asset.status}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h6 class="text-muted mb-1">Asset Name</h6>
                    <p class="h6 mb-0">${asset.name}</p>
                </div>
            </div>
        `;
        
        viewBtn.href = asset.url;
        this.assetInfo.classList.remove('d-none');
    }
    
    showError(message) {
        this.hideAllMessages();
        document.getElementById('error-text').textContent = message;
        this.errorMessage.classList.remove('d-none');
    }
    
    showLoading(message) {
        this.hideAllMessages();
        this.results.classList.remove('d-none');
    }
    
    hideAllMessages() {
        this.results.classList.add('d-none');
        this.assetInfo.classList.add('d-none');
        this.errorMessage.classList.add('d-none');
    }
    
    showManualInputOption() {
        // Show the manual input section
        const manualInput = document.getElementById('manual-input');
        if (manualInput) {
            manualInput.classList.remove('d-none');
        }
        
        // Add a retry button to the error message
        const errorText = document.getElementById('error-text');
        if (errorText && !errorText.innerHTML.includes('Retry')) {
            errorText.innerHTML += '<br><br><button class="btn btn-primary btn-sm mt-2" onclick="startScanner()">Try Camera Again</button>';
        }
    }
    
    resetInterface() {
        this.placeholder.classList.remove('d-none');
        this.videoContainer.classList.add('d-none');
        this.controls.classList.add('d-none');
        this.startBtn.classList.remove('d-none');
        this.stopBtn.classList.add('d-none');
        this.hideAllMessages();
    }
    
    getStatusColor(status) {
        const colors = {
            'Active': 'success',
            'Available': 'primary',
            'Inactive': 'secondary',
            'Under Maintenance': 'warning',
            'Issue Reported': 'danger',
            'Pending Confirmation': 'info',
            'Disposed': 'dark'
        };
        return colors[status] || 'secondary';
    }
}

// Global functions
let scanner;

function startScanner() {
    if (!scanner) {
        scanner = new QRScanner();
    }
    scanner.startScanner();
}

function stopScanner() {
    if (scanner) {
        scanner.stopScanner();
    }
}

function resetScanner() {
    if (scanner) {
        scanner.stopScanner();
        scanner = new QRScanner();
    }
}

// Manual QR code input functions
function handleManualInput(event) {
    if (event.key === 'Enter') {
        processManualQR();
    }
}

async function processManualQR() {
    const input = document.getElementById('manual-qr-input');
    const qrData = input.value.trim();
    
    if (!qrData) {
        alert('Please enter QR code data');
        return;
    }
    
    if (scanner) {
        await scanner.processQRCode(qrData);
    }
}

// Initialize scanner when page loads
document.addEventListener('DOMContentLoaded', function() {
    scanner = new QRScanner();
});
</script>
@endpush

