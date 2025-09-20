<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Responsiveness Test - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/sass/app.scss'])
    <style>
        /* Mobile-First Responsive Design for Inventory Management System */
        
        /* Base Mobile Styles */
        @media (max-width: 767.98px) {
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .stat-card {
                background: #fff;
                padding: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                text-align: center;
            }

            .stat-card .stat-number {
                font-size: 1.25rem;
                font-weight: 700;
                color: #667eea;
                margin-bottom: 0.25rem;
            }

            .stat-card .stat-label {
                font-size: 0.75rem;
                color: #6c757d;
                font-weight: 500;
            }

            .test-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin: 1rem 0;
            }
        }

        /* Tablet Styles */
        @media (min-width: 768px) and (max-width: 1023.98px) {
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }

        /* Desktop Styles */
        @media (min-width: 1024px) {
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 2rem;
            }
        }
    </style>
    <style>
        .test-section {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
        }
        .test-title {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            margin: -1rem -1rem 1rem -1rem;
            border-radius: 0.5rem 0.5rem 0 0;
            font-weight: 600;
        }
        .device-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            z-index: 9999;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .test-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="device-info" id="deviceInfo">
        <div>Width: <span id="screenWidth"></span>px</div>
        <div>Height: <span id="screenHeight"></span>px</div>
        <div>Device: <span id="deviceType"></span></div>
        <div>Orientation: <span id="orientation"></span></div>
    </div>

    <div class="container-fluid py-4">
        <h1 class="mb-4">
            <i class="fas fa-mobile-alt me-2"></i>
            Mobile Responsiveness Test
        </h1>

        <!-- Navigation Test -->
        <div class="test-section">
            <div class="test-title">Navigation Test</div>
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">Test Brand</a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Assets</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Users</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                            More
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Reports</a></li>
                                            <li><a class="dropdown-item" href="#">Settings</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Button Test -->
        <div class="test-section">
            <div class="test-title">Button Responsiveness Test</div>
            <div class="test-grid">
                <div class="test-card">
                    <button class="btn btn-primary btn-sm mb-2">Small Button</button>
                    <div class="small text-muted">Small size</div>
                </div>
                <div class="test-card">
                    <button class="btn btn-success mb-2">Normal Button</button>
                    <div class="small text-muted">Normal size</div>
                </div>
                <div class="test-card">
                    <button class="btn btn-info btn-lg mb-2">Large Button</button>
                    <div class="small text-muted">Large size</div>
                </div>
                <div class="test-card">
                    <button class="btn btn-outline-warning btn-block mb-2">Block Button</button>
                    <div class="small text-muted">Full width</div>
                </div>
            </div>
        </div>

        <!-- Form Test -->
        <div class="test-section">
            <div class="test-title">Form Responsiveness Test</div>
            <form>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="testInput1" class="form-label">Input Field 1</label>
                        <input type="text" class="form-control" id="testInput1" placeholder="Enter text">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="testSelect1" class="form-label">Select Field</label>
                        <select class="form-select" id="testSelect1">
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="testTextarea1" class="form-label">Textarea</label>
                        <textarea class="form-control" id="testTextarea1" rows="3" placeholder="Enter description"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="testCheck1">
                            <label class="form-check-label" for="testCheck1">
                                Checkbox option
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Test -->
        <div class="test-section">
            <div class="test-title">Table Responsiveness Test</div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>Laptop Dell XPS</td>
                            <td>Computer</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>John Doe</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>Monitor Samsung 24"</td>
                            <td>Monitor</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>Jane Smith</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Test -->
        <div class="test-section">
            <div class="test-title">Modal Responsiveness Test</div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testModal">
                Open Test Modal
            </button>
            
            <div class="modal fade" id="testModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Test Modal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>This is a test modal to check mobile responsiveness.</p>
                            <form>
                                <div class="mb-3">
                                    <label for="modalInput" class="form-label">Input in Modal</label>
                                    <input type="text" class="form-control" id="modalInput">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Test -->
        <div class="test-section">
            <div class="test-title">Stats Cards Test</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">150</div>
                    <div class="stat-label">Total Assets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">45</div>
                    <div class="stat-label">Assigned</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">105</div>
                    <div class="stat-label">Available</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Maintenance</div>
                </div>
            </div>
        </div>

        <!-- Touch Test -->
        <div class="test-section">
            <div class="test-title">Touch Interaction Test</div>
            <div class="row">
                <div class="col-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Touch Test</h5>
                            <p class="card-text">Tap this card</p>
                            <div id="touchCounter" class="h4 text-primary">0</div>
                            <small class="text-muted">Tap count</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Swipe Test</h5>
                            <p class="card-text">Swipe this card</p>
                            <div id="swipeDirection" class="h4 text-success">-</div>
                            <small class="text-muted">Swipe direction</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Test -->
        <div class="test-section">
            <div class="test-title">Performance Test</div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Load Time</h5>
                            <div id="loadTime" class="h4 text-info">-</div>
                            <small class="text-muted">Page load time</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Memory Usage</h5>
                            <div id="memoryUsage" class="h4 text-warning">-</div>
                            <small class="text-muted">JS heap size</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>FPS</h5>
                            <div id="fpsCounter" class="h4 text-danger">-</div>
                            <small class="text-muted">Frames per second</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        // Update device info
        function updateDeviceInfo() {
            document.getElementById('screenWidth').textContent = window.innerWidth;
            document.getElementById('screenHeight').textContent = window.innerHeight;
            
            let deviceType = 'Desktop';
            if (window.innerWidth < 768) deviceType = 'Mobile';
            else if (window.innerWidth < 1024) deviceType = 'Tablet';
            
            document.getElementById('deviceType').textContent = deviceType;
            document.getElementById('orientation').textContent = 
                window.innerHeight > window.innerWidth ? 'Portrait' : 'Landscape';
        }
        
        // Touch counter
        let touchCount = 0;
        document.querySelector('.card').addEventListener('click', function() {
            touchCount++;
            document.getElementById('touchCounter').textContent = touchCount;
        });
        
        // Swipe detection
        let startX = 0;
        let startY = 0;
        
        document.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchend', function(e) {
            if (!startX || !startY) return;
            
            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            
            const diffX = startX - endX;
            const diffY = startY - endY;
            
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                const direction = diffX > 0 ? 'Left' : 'Right';
                document.getElementById('swipeDirection').textContent = direction;
            } else if (Math.abs(diffY) > 50) {
                const direction = diffY > 0 ? 'Up' : 'Down';
                document.getElementById('swipeDirection').textContent = direction;
            }
            
            startX = 0;
            startY = 0;
        });
        
        // Performance monitoring
        function updatePerformance() {
            // Load time
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            document.getElementById('loadTime').textContent = loadTime + 'ms';
            
            // Memory usage
            if (performance.memory) {
                const memoryMB = (performance.memory.usedJSHeapSize / 1024 / 1024).toFixed(2);
                document.getElementById('memoryUsage').textContent = memoryMB + 'MB';
            }
        }
        
        // FPS counter
        let frameCount = 0;
        let lastTime = performance.now();
        
        function countFPS() {
            frameCount++;
            const currentTime = performance.now();
            
            if (currentTime - lastTime >= 1000) {
                document.getElementById('fpsCounter').textContent = frameCount;
                frameCount = 0;
                lastTime = currentTime;
            }
            
            requestAnimationFrame(countFPS);
        }
        
        // Initialize
        window.addEventListener('load', function() {
            updateDeviceInfo();
            updatePerformance();
            countFPS();
        });
        
        window.addEventListener('resize', updateDeviceInfo);
        window.addEventListener('orientationchange', function() {
            setTimeout(updateDeviceInfo, 100);
        });
        
        // Auto-refresh performance data
        setInterval(updatePerformance, 5000);
    </script>
</body>
</html>
