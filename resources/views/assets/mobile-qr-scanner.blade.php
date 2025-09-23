@extends('layouts.mobile')

@section('title', 'QR Scanner - Mobile')
@section('page-title', 'QR Scanner')
@section('page-subtitle', 'Scan asset QR codes quickly')

@section('page-actions')
    <a href="{{ route('assets.mobile') }}" class="btn btn-outline-light btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Scanner Container -->
    <div class="mobile-card mx-3 mt-3">
        <div class="mobile-card-body text-center">
            <div id="scanner-container" class="position-relative">
                <!-- Scanner Placeholder -->
                <div id="scanner-placeholder" class="scanner-placeholder">
                    <div class="scanner-icon mb-3">
                        <i class="fas fa-qrcode fa-4x text-primary"></i>
                    </div>
                    <h5 class="mb-2">QR Code Scanner</h5>
                    <p class="text-muted mb-4">Position the QR code within the frame to scan</p>
                    
                    <!-- Scanner Features -->
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="text-center">
                                <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Mobile-friendly</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <i class="fas fa-bolt fa-2x text-warning mb-2"></i>
                                <small class="d-block text-muted">Instant results</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <small class="d-block text-muted">Secure scanning</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Scanner Video -->
                <div id="scanner-video" class="scanner-video d-none">
                    <video id="video" width="100%" height="300" autoplay muted playsinline></video>
                    <div class="scanner-overlay">
                        <div class="scanner-frame"></div>
                        <div class="scanner-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner Controls -->
    <div class="mobile-card mx-3 mt-3" id="scanner-controls" style="display: none;">
        <div class="mobile-card-body">
            <div class="row g-2">
                <div class="col-6">
                    <button id="start-btn" class="btn btn-success btn-mobile w-100" onclick="startScanner()">
                        <i class="fas fa-play me-2"></i>Start Scanner
                    </button>
                </div>
                <div class="col-6">
                    <button id="stop-btn" class="btn btn-danger btn-mobile w-100" onclick="stopScanner()">
                        <i class="fas fa-stop me-2"></i>Stop Scanner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Results -->
    <div class="mobile-card mx-3 mt-3" id="scan-results" style="display: none;">
        <div class="mobile-card-body">
            <div class="mobile-loading">
                <div class="mobile-spinner"></div>
                <span>Processing QR Code...</span>
            </div>
        </div>
    </div>

    <!-- Asset Information -->
    <div class="mobile-card mx-3 mt-3" id="asset-info" style="display: none;">
        <div class="mobile-card-header">
            <h6 class="mb-0 text-success">
                <i class="fas fa-check-circle me-2"></i>Asset Found
            </h6>
        </div>
        <div class="mobile-card-body">
            <div id="asset-details">
                <!-- Asset details will be populated here -->
            </div>
        </div>
        <div class="mobile-card-footer">
            <div class="row g-2">
                <div class="col-6">
                    <a id="view-asset-btn" href="#" class="btn btn-primary btn-mobile w-100">
                        <i class="fas fa-eye me-1"></i>View Asset
                    </a>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-secondary btn-mobile w-100" onclick="resetScanner()">
                        <i class="fas fa-redo me-1"></i>Scan Another
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div class="mobile-card mx-3 mt-3" id="error-message" style="display: none;">
        <div class="mobile-card-header">
            <h6 class="mb-0 text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Scan Error
            </h6>
        </div>
        <div class="mobile-card-body">
            <p id="error-text" class="mb-3"></p>
            <button class="btn btn-outline-danger btn-mobile w-100" onclick="resetScanner()">
                <i class="fas fa-redo me-1"></i>Try Again
            </button>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mobile-card mx-3 mt-3">
        <div class="mobile-card-header">
            <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>How to Use
            </h6>
        </div>
        <div class="mobile-card-body">
            <div class="mobile-list">
                <div class="mobile-list-item">
                    <i class="fas fa-mobile-alt text-primary"></i>
                    <div>
                        <div class="fw-medium">Mobile Device</div>
                        <small class="text-muted">Use your phone's camera to scan QR codes</small>
                    </div>
                </div>
                <div class="mobile-list-item">
                    <i class="fas fa-qrcode text-warning"></i>
                    <div>
                        <div class="fw-medium">Position QR Code</div>
                        <small class="text-muted">Hold the QR code steady within the scanning frame</small>
                    </div>
                </div>
                <div class="mobile-list-item">
                    <i class="fas fa-lightbulb text-success"></i>
                    <div>
                        <div class="fw-medium">Good Lighting</div>
                        <small class="text-muted">Ensure good lighting for better scanning results</small>
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
    padding: 2rem 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--mobile-border-radius);
    border: 2px dashed #dee2e6;
}

.scanner-video {
    position: relative;
    border-radius: var(--mobile-border-radius);
    overflow: hidden;
    box-shadow: var(--mobile-shadow);
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
    width: 200px;
    height: 200px;
    border: 3px solid var(--mobile-success);
    border-radius: var(--mobile-border-radius);
    position: relative;
    animation: pulse 2s infinite;
}

.scanner-line {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--mobile-success), transparent);
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
    0% { transform: translateY(-100px); }
    100% { transform: translateY(100px); }
}

/* Dark mode mobile scanner */
[data-theme="dark"] .scanner-placeholder {
    background: linear-gradient(135deg, #343a40 0%, #2d2d2d 100%);
    border-color: #404040;
}

[data-theme="dark"] .scanner-frame {
    border-color: var(--mobile-success);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
class MobileQRScanner {
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
            
            // Request camera access
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });
            
            this.video.srcObject = this.stream;
            this.video.play();
            
            // Show scanner interface
            this.placeholder.style.display = 'none';
            this.videoContainer.classList.remove('d-none');
            this.controls.style.display = 'block';
            this.startBtn.style.display = 'none';
            this.stopBtn.style.display = 'block';
            
            this.scanning = true;
            this.startScanning();
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            this.showError('Unable to access camera. Please check permissions and try again.');
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
            <div class="row g-3">
                <div class="col-12">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-desktop text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">${asset.name}</h6>
                            <code class="text-primary">${asset.asset_tag}</code>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-medium text-muted">Status</div>
                        <span class="badge bg-${this.getStatusColor(asset.status)}">${asset.status}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-medium text-muted">Asset ID</div>
                        <div class="fw-bold">#${asset.id}</div>
                    </div>
                </div>
            </div>
        `;
        
        viewBtn.href = asset.url;
        this.assetInfo.style.display = 'block';
    }
    
    showError(message) {
        this.hideAllMessages();
        document.getElementById('error-text').textContent = message;
        this.errorMessage.style.display = 'block';
    }
    
    showLoading(message) {
        this.hideAllMessages();
        this.results.style.display = 'block';
    }
    
    hideAllMessages() {
        this.results.style.display = 'none';
        this.assetInfo.style.display = 'none';
        this.errorMessage.style.display = 'none';
    }
    
    resetInterface() {
        this.placeholder.style.display = 'block';
        this.videoContainer.classList.add('d-none');
        this.controls.style.display = 'none';
        this.startBtn.style.display = 'block';
        this.stopBtn.style.display = 'none';
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
let mobileScanner;

function startScanner() {
    if (!mobileScanner) {
        mobileScanner = new MobileQRScanner();
    }
    mobileScanner.startScanner();
}

function stopScanner() {
    if (mobileScanner) {
        mobileScanner.stopScanner();
    }
}

function resetScanner() {
    if (mobileScanner) {
        mobileScanner.stopScanner();
        mobileScanner = new MobileQRScanner();
    }
}

// Initialize scanner when page loads
document.addEventListener('DOMContentLoaded', function() {
    mobileScanner = new MobileQRScanner();
});
</script>
@endpush

