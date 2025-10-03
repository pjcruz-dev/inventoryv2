@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<!-- Dashboard styles are now loaded in the main layout with high priority -->

/* Enhanced Dashboard Cards */
.dashboard-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

/* Metric Cards */
.metric-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.metric-label {
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-change {
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.metric-change.positive { color: var(--success-color); }
.metric-change.negative { color: var(--danger-color); }

/* Hero Section */
.dashboard-hero {
    background: var(--primary-gradient);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.dashboard-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 100%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.dashboard-hero-content {
    position: relative;
    z-index: 2;
}

/* Progress Ring */
.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dasharray 0.35s;
    transform-origin: 50% 50%;
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.status-badge.deployed { background: #d1fae5; color: #065f46; }
.status-badge.problematic { background: #fee2e2; color: #991b1b; }
.status-badge.pending { background: #fef3c7; color: #92400e; }
.status-badge.returned { background: #dbeafe; color: #1e40af; }
.status-badge.disposed { background: #f3f4f6; color: #374151; }

/* Quick Action Buttons */
.quick-action-btn {
    background: var(--bg-white);
    border: 2px solid var(--border-light);
    border-radius: 12px;
    padding: 1.5rem;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
    height: 120px;
}

.quick-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s;
}

.quick-action-btn:hover::before {
    left: 100%;
}

.quick-action-btn:hover {
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
    text-decoration: none;
    color: var(--primary-color);
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    background: var(--primary-gradient);
    color: white;
    transition: all 0.3s ease;
}

.quick-action-btn:hover .quick-action-icon {
    transform: scale(1.1);
}

.quick-action-text {
    font-weight: 600;
    font-size: 0.9rem;
}

/* Clickable Numbers */
.clickable-number {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.clickable-number:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.clickable-number:active {
    transform: scale(0.95);
}

/* Chart Container */
.chart-container {
    position: relative;
    height: 300px;
    margin-top: 1rem;
}

/* Recent Assets */
.recent-asset-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--bg-light);
    color: var(--text-secondary);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.metric-card {
    animation: fadeInUp 0.6s ease-out;
}

.metric-card:nth-child(1) { animation-delay: 0.1s; }
.metric-card:nth-child(2) { animation-delay: 0.2s; }
.metric-card:nth-child(3) { animation-delay: 0.3s; }
.metric-card:nth-child(4) { animation-delay: 0.4s; }

.dashboard-card {
    animation: fadeInUp 0.8s ease-out;
}

/* Filter Card */
.dashboard-filter-card {
    background: var(--bg-white);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--border-light);
}

.dashboard-filter-select {
    min-width: 140px;
    border-radius: 8px;
    border: 2px solid var(--border-light);
    transition: all 0.3s ease;
}

.dashboard-filter-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.dashboard-filter-btn {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-hero {
        padding: 1.5rem;
        text-align: center;
    }
    
    .metric-value {
        font-size: 2rem;
    }
    
    .quick-action-btn {
        height: 100px;
        padding: 1rem;
    }
    
    .dashboard-filter-card {
        padding: 0.75rem;
    }
    
    .dashboard-filter-select {
        min-width: 120px;
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection
@section('page-title', 'Dashboard')

@section('page-actions')
    <div class="dashboard-filter-card">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h6 class="mb-0 text-muted d-flex align-items-center">
                <i class="fas fa-filter me-2"></i>Filter Dashboard Data
            </h6>
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="month" class="form-select form-select-sm dashboard-filter-select">
                <option value="">All Months</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </option>
                @endfor
            </select>
                <select name="year" class="form-select form-select-sm dashboard-filter-select">
                <option value="">All Years</option>
                @for($year = date('Y'); $year >= 2020; $year--)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
                <select name="entity" class="form-select form-select-sm dashboard-filter-select">
                <option value="">All Entities</option>
                @foreach($entities as $entity)
                    <option value="{{ $entity }}" {{ request('entity') == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                @endforeach
            </select>
                <button type="submit" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center dashboard-filter-btn">
                    <i class="fas fa-filter me-2"></i>Apply
            </button>
            @if(request()->hasAny(['month', 'year', 'entity']))
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center dashboard-filter-btn">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            @endif
        </form>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Enhanced Dashboard Hero Section -->
    <div class="enhanced-dashboard-hero">
        <div class="hero-background-pattern"></div>
        <div class="hero-content-wrapper">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-text-section">
                        <div class="hero-icon-wrapper">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="hero-text-content">
                            <h1 class="hero-title">
                                <span class="title-main">Asset Management</span>
                                <span class="title-accent">Dashboard</span>
                            </h1>
                            <p class="hero-description">
                                Comprehensive overview of your asset inventory, deployment status, and key performance metrics
                            </p>
                            <div class="hero-stats">
                                <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-label">Last updated</span>
                                        <span class="stat-value" id="lastUpdated1">{{ now()->format('M d, Y \a\t g:i A') }}</span>
                                    </div>
                                </div>
                                <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                                    <div class="stat-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-label">Assets Deployed</span>
                                        <span class="stat-value">{{ $deployedAssetsPercentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-deployment-section" data-aos="fade-left" data-aos-delay="300">
                        <div class="deployment-card">
                            <div class="deployment-header">
                                <div class="deployment-icon">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="deployment-label">Deployment Rate</div>
                            </div>
                            <div class="deployment-value">{{ $deployedAssetsPercentage }}%</div>
                            <div class="deployment-progress">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $deployedAssetsPercentage }}%">
                                        <div class="progress-glow"></div>
                                    </div>
                                </div>
                                <div class="progress-label">
                                    <span class="current">{{ $deployedAssetsPercentage }}%</span>
                                    <span class="target">Target: {{ $deploymentTarget }}%</span>
                                </div>
                            </div>
                            <div class="deployment-status">
                                @if($deployedAssetsPercentage >= $deploymentTarget)
                                    <span class="status-badge success">
                                        <i class="fas fa-check-circle"></i> Excellent
                                    </span>
                                @elseif($deployedAssetsPercentage >= ($deploymentTarget * 0.75))
                                    <span class="status-badge warning">
                                        <i class="fas fa-exclamation-triangle"></i> Good
                                    </span>
                                @else
                                    <span class="status-badge danger">
                                        <i class="fas fa-times-circle"></i> Needs Improvement
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-floating-elements">
            <div class="floating-icon icon-1">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="floating-icon icon-2">
                <i class="fas fa-cog"></i>
            </div>
            <div class="floating-icon icon-3">
                <i class="fas fa-database"></i>
            </div>
        </div>
    </div>
        
    <!-- Enhanced Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="enhanced-metric-card" data-aos="fade-up" data-aos-delay="100">
                <div class="metric-card-header">
                    <div class="metric-icon-wrapper primary">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="metric-trend-indicator {{ $assetsGrowth['trend'] === 'positive' ? 'positive' : ($assetsGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        @if($assetsGrowth['trend'] === 'positive')
                            <i class="fas fa-arrow-up"></i>
                        @elseif($assetsGrowth['trend'] === 'negative')
                            <i class="fas fa-arrow-down"></i>
                        @else
                            <i class="fas fa-minus"></i>
                        @endif
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value" data-stat="total-assets">{{ number_format($totalAssets) }}</div>
                    <div class="metric-label">Total Assets</div>
                    <div class="metric-description">Inventory items tracked</div>
                </div>
                <div class="metric-card-footer">
                    <div class="metric-change {{ $assetsGrowth['trend'] === 'positive' ? 'positive' : ($assetsGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        {{ $assetsGrowth['text'] }}
                    </div>
                    <div class="metric-action">
                        <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="metric-card-bg-pattern"></div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="enhanced-metric-card" data-aos="fade-up" data-aos-delay="200">
                <div class="metric-card-header">
                    <div class="metric-icon-wrapper success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="metric-trend-indicator {{ $usersGrowth['trend'] === 'positive' ? 'positive' : ($usersGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        @if($usersGrowth['trend'] === 'positive')
                            <i class="fas fa-arrow-up"></i>
                        @elseif($usersGrowth['trend'] === 'negative')
                            <i class="fas fa-arrow-down"></i>
                        @else
                            <i class="fas fa-minus"></i>
                        @endif
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value" data-stat="total-users">{{ number_format($totalUsers) }}</div>
                    <div class="metric-label">Active Users</div>
                    <div class="metric-description">System users</div>
                </div>
                <div class="metric-card-footer">
                    <div class="metric-change {{ $usersGrowth['trend'] === 'positive' ? 'positive' : ($usersGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        {{ $usersGrowth['text'] }}
                    </div>
                    <div class="metric-action">
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="metric-card-bg-pattern"></div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="enhanced-metric-card" data-aos="fade-up" data-aos-delay="300">
                <div class="metric-card-header">
                    <div class="metric-icon-wrapper warning">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="metric-trend-indicator {{ $departmentsGrowth['trend'] === 'positive' ? 'positive' : ($departmentsGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        @if($departmentsGrowth['trend'] === 'positive')
                            <i class="fas fa-arrow-up"></i>
                        @elseif($departmentsGrowth['trend'] === 'negative')
                            <i class="fas fa-arrow-down"></i>
                        @else
                            <i class="fas fa-minus"></i>
                        @endif
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value" data-stat="total-departments">{{ number_format($totalDepartments) }}</div>
                    <div class="metric-label">Departments</div>
                    <div class="metric-description">Organizational units</div>
                </div>
                <div class="metric-card-footer">
                    <div class="metric-change {{ $departmentsGrowth['trend'] === 'positive' ? 'positive' : ($departmentsGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        {{ $departmentsGrowth['text'] }}
                    </div>
                    <div class="metric-action">
                        <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="metric-card-bg-pattern"></div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="enhanced-metric-card" data-aos="fade-up" data-aos-delay="400">
                <div class="metric-card-header">
                    <div class="metric-icon-wrapper info">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="metric-trend-indicator {{ $vendorsGrowth['trend'] === 'positive' ? 'positive' : ($vendorsGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        @if($vendorsGrowth['trend'] === 'positive')
                            <i class="fas fa-arrow-up"></i>
                        @elseif($vendorsGrowth['trend'] === 'negative')
                            <i class="fas fa-arrow-down"></i>
                        @else
                            <i class="fas fa-minus"></i>
                        @endif
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value" data-stat="total-vendors">{{ number_format($totalVendors) }}</div>
                    <div class="metric-label">Vendors</div>
                    <div class="metric-description">Supplier partners</div>
                </div>
                <div class="metric-card-footer">
                    <div class="metric-change {{ $vendorsGrowth['trend'] === 'positive' ? 'positive' : ($vendorsGrowth['trend'] === 'negative' ? 'negative' : 'neutral') }}">
                        {{ $vendorsGrowth['text'] }}
                    </div>
                    <div class="metric-action">
                        <a href="{{ route('vendors.index') }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="metric-card-bg-pattern"></div>
            </div>
        </div>
    </div>
        


    <!-- Monthly Analysis Row -->
    <div class="row mb-4">
        <!-- Asset Performance Dashboard -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="dashboard-card h-100 asset-performance-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="asset-performance-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Asset Performance Dashboard</h6>
                            <small class="text-muted">Real-time asset insights & alerts</small>
                        </div>
                    </div>
                    <div class="performance-controls d-flex gap-2 align-items-center">
                        <div class="refresh-status" id="refreshStatus">
                            <i class="fas fa-circle text-success"></i>
                            <span>Live</span>
                        </div>
                    </div>
                </div>
        
                <!-- Performance Metrics Grid -->
                <div class="performance-metrics-grid">
                    <!-- Critical Alerts -->
                    <div class="performance-metric-card critical" data-aos="fade-up" data-aos-delay="100">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="metric-badge urgent">{{ \App\Models\Asset::where('status', 'problematic')->count() }}</div>
                        </div>
                        <div class="metric-content">
                            <h6 class="metric-title">Critical Issues</h6>
                            <p class="metric-description">Assets requiring immediate attention</p>
                            <a href="{{ route('assets.index', ['status' => 'problematic']) }}" class="metric-action">
                                <i class="fas fa-arrow-right"></i>View Issues
                            </a>
                        </div>
                    </div>

                    <!-- Maintenance Due -->
                    <div class="performance-metric-card warning" data-aos="fade-up" data-aos-delay="200">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="metric-badge warning">{{ \App\Models\Maintenance::where('status', 'Scheduled')->count() }}</div>
                        </div>
                        <div class="metric-content">
                            <h6 class="metric-title">Maintenance Due</h6>
                            <p class="metric-description">Scheduled maintenance tasks</p>
                            <a href="{{ route('maintenance.index') }}" class="metric-action">
                                <i class="fas fa-arrow-right"></i>View Schedule
                            </a>
                        </div>
                    </div>

                    <!-- Pending Assignments -->
                    <div class="performance-metric-card info" data-aos="fade-up" data-aos-delay="300">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <div class="metric-badge info">{{ \App\Models\AssetAssignment::where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="metric-content">
                            <h6 class="metric-title">Pending Assignments</h6>
                            <p class="metric-description">Assets awaiting assignment</p>
                            <a href="{{ route('asset-assignments.index') }}" class="metric-action">
                                <i class="fas fa-arrow-right"></i>View Assignments
                            </a>
                        </div>
                    </div>

                    <!-- Warranty Expiring -->
                    <div class="performance-metric-card danger" data-aos="fade-up" data-aos-delay="400">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="metric-badge danger">
                                @php
                                    $expiringWarranties = \App\Models\Asset::where('warranty_end', '<=', now()->addDays(30))
                                        ->where('warranty_end', '>=', now())
                                        ->count();
                                @endphp
                                {{ $expiringWarranties }}
                            </div>
                        </div>
                        <div class="metric-content">
                            <h6 class="metric-title">Warranty Expiring</h6>
                            <p class="metric-description">Assets with expiring warranty (30 days)</p>
                            <a href="{{ route('assets.index', ['warranty_expiring' => 'true']) }}" class="metric-action">
                                <i class="fas fa-arrow-right"></i>View Assets
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Performance Insights -->
                <div class="performance-insights">
                    <h6 class="insights-title">
                        <i class="fas fa-lightbulb me-2"></i>Performance Insights
                    </h6>
                    <div class="insights-list">
                        <div class="insight-item positive">
                            <div class="insight-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h6 class="insight-title">Asset Utilization</h6>
                                <p class="insight-description" id="assetUtilizationText">{{ $deployedAssetsPercentage }}% of assets are currently deployed and active</p>
                            </div>
                        </div>
                        <div class="insight-item neutral">
                            <div class="insight-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div>
                                <h6 class="insight-title">Asset Health</h6>
                                <p class="insight-description" id="assetHealthText">{{ \App\Models\Asset::where('status', 'problematic')->count() }}% of assets are currently problematic</p>
                            </div>
                        </div>
                        <div class="insight-item positive">
                            <div class="insight-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div>
                                <h6 class="insight-title">System Performance</h6>
                                <p class="insight-description">All systems operating within normal parameters</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Weekly Movement Analysis -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="dashboard-card h-100 weekly-analysis-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="weekly-analysis-icon">
                            <i class="fas fa-calendar-week"></i>
                </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Weekly Movement Analysis</h6>
                            <small class="text-muted">Asset movement patterns</small>
                        </div>
                    </div>
                    <div class="weekly-controls d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm weekly-chart-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#weeklyChart" aria-expanded="false">
                            <i class="fas fa-chart-bar me-2"></i>Chart View
                        </button>
                        <button class="btn btn-outline-success btn-sm" id="exportWeeklyData" type="button">
                            <i class="fas fa-download me-2"></i>Export Data
                        </button>
                    </div>
                </div>
                
                <!-- Enhanced Chart View -->
                <div class="collapse mb-4" id="weeklyChart">
                    <div class="weekly-chart-container">
                        <div class="chart-header">
                            <h6 class="chart-title">Weekly Movement Trends</h6>
                            <div class="chart-legend">
                                <span class="legend-item">
                                    <i class="fas fa-circle text-primary"></i> Deployed
                                </span>
                                <span class="legend-item">
                                    <i class="fas fa-circle text-danger"></i> Problematic
                                </span>
                                <span class="legend-item">
                                                <i class="fas fa-circle text-warning"></i> Under Maintenance
                                </span>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="weeklyMovementChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="weekly-breakdown">
                    @if(!empty($weeklyBreakdown['months']))
                        @foreach($weeklyBreakdown['months'] as $month => $weeks)
                            @php
                                $totalForMonth = 0;
                                foreach($weeks as $weekData) {
                                    $totalForMonth += array_sum($weekData);
                                }
                            @endphp
                            <div class="weekly-period-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}" data-month="{{ $month }}">
                                <div class="weekly-period-header">
                                    <div class="weekly-period-title">
                                        <h6 class="week-month-name">{{ $month }}</h6>
                                        <div class="weekly-summary">
                                            <span class="total-movements">{{ $totalForMonth }} movements</span>
                                            @if($totalForMonth > 0)
                                                <span class="movement-intensity">
                                                    @php
                                                        $avgPerWeek = $totalForMonth / count($weeks);
                                                        $intensity = $avgPerWeek >= 10 ? 'high' : ($avgPerWeek >= 5 ? 'medium' : 'low');
                                                    @endphp
                                                    <i class="fas fa-tachometer-alt me-1"></i>
                                                    <span class="intensity-level {{ $intensity }}">{{ ucfirst($intensity) }} activity</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($totalForMonth > 0)
                                        <div class="weekly-visual-indicator">
                                            <div class="movement-sparkline">
                                                @foreach($weeks as $weekData)
                                                    @php
                                                        $weekTotal = array_sum($weekData);
                                                        $height = $weekTotal > 0 ? min(100, ($weekTotal / max(array_map('array_sum', $weeks))) * 100) : 0;
                                                    @endphp
                                                    <div class="spark-bar" style="height: {{ $height }}%"></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($totalForMonth > 0)
                                    <div class="weekly-table-container">
                                        <div class="weekly-table-wrapper">
                                            <table class="weekly-table">
                                        <thead>
                                            <tr>
                                                        <th class="week-header">Week</th>
                                                @foreach($weeklyBreakdown['statuses'] as $status)
                                                            @php
                                                                $statusConfig = match($status) {
                                                                    'Deployed' => ['color' => 'success', 'icon' => 'fas fa-check-circle'],
                                                                    'Problematic' => ['color' => 'danger', 'icon' => 'fas fa-exclamation-triangle'],
                                                                    'Pending' => ['color' => 'warning', 'icon' => 'fas fa-clock'],
                                                                    'Returned' => ['color' => 'info', 'icon' => 'fas fa-undo'],
                                                                    'Disposed' => ['color' => 'secondary', 'icon' => 'fas fa-trash'],
                                                                    'New Arrival' => ['color' => 'primary', 'icon' => 'fas fa-plus-circle'],
                                                                    'Transferred' => ['color' => 'warning', 'icon' => 'fas fa-exchange-alt'],
                                                                    default => ['color' => 'light', 'icon' => 'fas fa-question']
                                                                };
                                                            @endphp
                                                            <th class="status-header {{ $statusConfig['color'] }}" data-header-status="{{ $status }}">
                                                                <div class="status-header-content">
                                                                    <i class="{{ $statusConfig['icon'] }}"></i>
                                                                    <span>{{ $status }}</span>
                                                                    <small class="header-total">{{ array_sum(array_column($weeks, $status)) }}</small>
                                                                </div>
                                                            </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($weeks as $week => $data)
                                                            @php
                                                            $weekTotal = array_sum($data);
                                                                $monthNumber = date('n', strtotime($month));
                                                                $year = date('Y', strtotime($month));
                                                            @endphp
                                                        <tr class="weekly-row {{ $weekTotal > 0 ? 'has-activity' : 'no-activity' }}" data-week="{{ $week }}">
                                                            <td class="week-cell">
                                                                <div class="week-info">
                                                                    <span class="week-name">{{ $week }}</span>
                                                                    @if($weekTotal > 0)
                                                                        <span class="week-badge">{{ $weekTotal }}</span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            @foreach($weeklyBreakdown['statuses'] as $status)
                                                                @php
                                                                    $count = $data[$status] ?? 0;
                                                                    $statusConfig = match($status) {
                                                                        'Deployed' => 'success',
                                                                        'Problematic' => 'danger',
                                                                        'Pending' => 'warning',
                                                                        'Returned' => 'info',
                                                                        'Disposed' => 'secondary',
                                                                        'New Arrival' => 'primary',
                                                                        'Transferred' => 'warning',
                                                                        default => 'light'
                                                                    };
                                                                @endphp
                                                                <td class="status-cell {{ $statusConfig }}" data-status="{{ $status }}">
                                                            @if($count > 0)
                                                                <a href="{{ route('dashboard.asset-movements', [
                                                                    'week' => $week,
                                                                    'status' => $status,
                                                                    'month' => $monthNumber,
                                                                    'year' => $year
                                                                ]) }}" 
                                                                           class="movement-link" 
                                                                   title="Click to view {{ $count }} {{ strtolower($status) }} assets">
                                                                            <span class="movement-count week-count">{{ $count }}</span>
                                                                </a>
                                                            @else
                                                                        <span class="no-movement week-count">—</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                    </div>
                                @else
                                    <div class="weekly-empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-calendar-week"></i>
                                        </div>
                                        <h6 class="empty-title">No Movements</h6>
                                        <p class="empty-description">No asset movements recorded for this month</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="weekly-empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <h6 class="empty-title">No Weekly Data</h6>
                            <p class="empty-description">No weekly movement data available</p>
                            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Add First Asset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & System Status Row -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-xl-8 col-lg-12 mb-3">
            <div class="dashboard-card h-100 quick-actions-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="quick-actions-icon">
                            <i class="fas fa-bolt"></i>
                </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Quick Actions</h6>
                            <small class="text-muted">Common tasks and shortcuts</small>
                        </div>
                    </div>
                    <div class="quick-actions-controls">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle quick-actions-customize-btn" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-2"></i>Customize
                            </button>
                            <ul class="dropdown-menu quick-actions-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-plus me-2"></i>Add New Action</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit Actions</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sort me-2"></i>Reorder Actions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="quick-actions-grid">
                        @can('create_assets')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="100">
                            <a href="{{ route('assets.create') }}" class="enhanced-quick-action-btn" title="Add a new asset to inventory">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon primary">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">Add Asset</h6>
                                    <p class="action-description">Create new asset</p>
                                </div>
                                <div class="action-badge new">New</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_assets')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="200">
                            <a href="{{ route('assets.index') }}" class="enhanced-quick-action-btn" title="View all assets in inventory">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon success">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">View Assets</h6>
                                    <p class="action-description">{{ $totalAssets }} total assets</p>
                                </div>
                                <div class="action-badge count">{{ $totalAssets }}</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                        @can('create_users')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="300">
                            <a href="{{ route('users.create') }}" class="enhanced-quick-action-btn" title="Add a new user to the system">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon info">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">Add User</h6>
                                    <p class="action-description">Create new user</p>
                                </div>
                                <div class="action-badge new">New</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_reports')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="400">
                            <a href="{{ route('assets.print-employee-assets') }}" class="enhanced-quick-action-btn" target="_blank" title="Generate asset reports">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon warning">
                                        <i class="fas fa-print"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">Reports</h6>
                                    <p class="action-description">Generate PDF reports</p>
                                </div>
                                <div class="action-badge pdf">PDF</div>
                                <div class="action-arrow">
                                    <i class="fas fa-external-link-alt"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_maintenance')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="500">
                            <a href="{{ route('maintenance.index') }}" class="enhanced-quick-action-btn" title="Manage asset maintenance">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon danger">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">Maintenance</h6>
                                    <p class="action-description">{{ \App\Models\Maintenance::where('status', 'pending')->count() }} pending</p>
                                </div>
                                <div class="action-badge urgent">{{ \App\Models\Maintenance::where('status', 'pending')->count() }}</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                    @can('view_asset_assignments')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="600">
                            <a href="{{ route('asset-assignments.index') }}" class="enhanced-quick-action-btn" title="View asset assignment timeline">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon secondary">
                                        <i class="fas fa-hand-holding"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">Assignments</h6>
                                    <p class="action-description">{{ \App\Models\AssetAssignment::where('status', 'pending')->count() }} pending</p>
                                </div>
                                <div class="action-badge pending">{{ \App\Models\AssetAssignment::where('status', 'pending')->count() }}</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                    </div>
                
                <div class="quick-actions-footer">
                    <div class="footer-content">
                        <div class="footer-stats">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-handshake"></i>
                </div>
                                <div class="stat-info">
                                    <span class="stat-label">Asset Assignments</span>
                                    <span class="stat-value">{{ \App\Models\AssetAssignment::count() }} total</span>
                                </div>
                            </div>
                        </div>
                        <div class="footer-actions">
                            <a href="{{ route('asset-assignments.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-2"></i>View All
                            </a>
                            @can('create_asset_assignments')
                                <a href="{{ route('asset-assignments.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>New Assignment
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="col-xl-4 col-lg-12 mb-3">
            <div class="dashboard-card h-100 system-status-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="system-status-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">System Status</h6>
                            <small class="text-muted">System health monitoring</small>
                        </div>
                    </div>
                    <div class="system-refresh">
                        <button class="btn btn-outline-secondary btn-sm system-refresh-btn" title="Refresh status">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="system-status-overview">
                    <div class="status-summary">
                        <div class="overall-status healthy">
                            <div class="status-indicator-large">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="status-info">
                                <h6 class="status-title">System Healthy</h6>
                                <p class="status-description">All systems operational</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="system-components">
                        <div class="component-item" data-aos="fade-up" data-aos-delay="100">
                            <div class="component-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="component-info">
                                <h6 class="component-name">Database</h6>
                                <p class="component-description">MySQL connection</p>
                            </div>
                            <div class="component-status">
                                <div class="status-indicator healthy"></div>
                                <span class="status-text">Online</span>
                            </div>
                        </div>
                        
                        <div class="component-item" data-aos="fade-up" data-aos-delay="200">
                            <div class="component-icon">
                                <i class="fas fa-hdd"></i>
                            </div>
                            <div class="component-info">
                                <h6 class="component-name">File Storage</h6>
                                <p class="component-description">Local storage</p>
                            </div>
                            <div class="component-status">
                                <div class="status-indicator healthy"></div>
                                <span class="status-text">Healthy</span>
                            </div>
                        </div>
                        
                        <div class="component-item" data-aos="fade-up" data-aos-delay="300">
                            <div class="component-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="component-info">
                                <h6 class="component-name">Email Service</h6>
                                <p class="component-description">SMTP server</p>
                            </div>
                            <div class="component-status">
                                <div class="status-indicator warning"></div>
                                <span class="status-text">Limited</span>
                            </div>
                        </div>
                        
                        <div class="component-item" data-aos="fade-up" data-aos-delay="400">
                            <div class="component-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="component-info">
                                <h6 class="component-name">Backup</h6>
                                <p class="component-description">Daily backups</p>
                            </div>
                            <div class="component-status">
                                <div class="status-indicator healthy"></div>
                                <span class="status-text">Up to Date</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="system-status-footer">
                    <div class="performance-metrics">
                        <div class="metric-item">
                            <div class="metric-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="metric-info">
                                <span class="metric-label">Response Time</span>
                                <span class="metric-value">45ms</span>
                            </div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-icon">
                                <i class="fas fa-memory"></i>
                            </div>
                            <div class="metric-info">
                                <span class="metric-label">Memory Usage</span>
                                <span class="metric-value">68%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="last-updated">
                        <i class="fas fa-clock me-2"></i>
                        <small class="text-muted">Last updated: <span id="lastUpdated2">{{ now()->format('M d, Y \a\t g:i A') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
/* Enhanced chart styling */
.chart-container canvas {
    border-radius: 8px;
}

/* Smooth animations for progress rings */
.progress-ring-circle {
    transition: stroke-dashoffset 0.5s ease-in-out;
}

/* Enhanced table styling */
.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateX(2px);
    transition: all 0.2s ease;
}

/* Loading animation for clickable numbers */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.clickable-number.loading {
    animation: pulse 1s infinite;
}

/* Enhanced Quick Action Badges */
.quick-action-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* System Status Indicators */
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s infinite;
}

/* Enhanced Monthly Status Cards */
.monthly-analysis .badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

/* Weekly Movement Chart Toggle */
.collapse {
    transition: all 0.3s ease;
}

/* Enhanced Quick Actions with Badges */
.quick-action-btn {
    position: relative;
    overflow: visible;
}

.quick-action-btn:hover .quick-action-badge {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

/* System Status Cards */
.system-status .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Enhanced Empty States */
.text-center.py-4 i {
    opacity: 0.3;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .quick-action-badge {
        width: 18px;
        height: 18px;
        font-size: 9px;
    }
    
    .status-indicator {
        width: 10px;
        height: 10px;
    }
}

/* Asset Performance Dashboard Styles */
.asset-performance-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--border-light);
}

.asset-performance-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    margin-right: 1rem;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.performance-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.performance-metric-card {
    background: var(--bg-light);
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.performance-metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.performance-metric-card.critical::before { background: #ef4444; }
.performance-metric-card.warning::before { background: #f59e0b; }
.performance-metric-card.info::before { background: #3b82f6; }
.performance-metric-card.danger::before { background: #dc2626; }

.performance-metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.metric-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.metric-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
}

.performance-metric-card.critical .metric-icon { background: #ef4444; }
.performance-metric-card.warning .metric-icon { background: #f59e0b; }
.performance-metric-card.info .metric-icon { background: #3b82f6; }
.performance-metric-card.danger .metric-icon { background: #dc2626; }

.metric-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    color: white;
}

.metric-badge.urgent { background: #ef4444; }
.metric-badge.warning { background: #f59e0b; }
.metric-badge.info { background: #3b82f6; }
.metric-badge.danger { background: #dc2626; }

.metric-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.metric-description {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.metric-action {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.metric-action:hover {
    color: var(--primary-color);
    text-decoration: none;
    transform: translateX(3px);
}

/* Performance Insights */
.performance-insights {
    background: var(--bg-light);
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid var(--border-light);
}

.insights-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.insights-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.insight-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.insight-item.positive {
    background: rgba(16, 185, 129, 0.05);
    border-left: 3px solid #10b981;
}

.insight-item.neutral {
    background: rgba(245, 158, 11, 0.05);
    border-left: 3px solid #f59e0b;
}

.insight-item.negative {
    background: rgba(239, 68, 68, 0.05);
    border-left: 3px solid #ef4444;
}

.insight-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.insight-item.positive .insight-icon {
    background: #10b981;
    color: white;
}

.insight-item.neutral .insight-icon {
    background: #f59e0b;
    color: white;
}

.insight-item.negative .insight-icon {
    background: #ef4444;
    color: white;
}

.insight-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.insight-description {
    color: var(--text-secondary);
    font-size: 0.8rem;
    line-height: 1.4;
    margin: 0;
}

/* Refresh Status Indicator */
.refresh-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 12px;
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    transition: all 0.3s ease;
}

.refresh-status.updating {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.refresh-status.error {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.refresh-status i {
    font-size: 8px;
    animation: pulse 2s infinite;
}

.refresh-status.updating i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Weekly Movement Chart (Bar Chart)
const weeklyChartCtx = document.getElementById('weeklyMovementChart');
if (weeklyChartCtx) {
    const weeklyData = @json($weeklyBreakdown ?? []);
    
    if (weeklyData && weeklyData.months) {
        const months = Object.keys(weeklyData.months);
        const statuses = weeklyData.statuses || ['Deployed', 'Disposed', 'New Arrival', 'Returned', 'Transferred'];
        
        // Prepare data for the chart
        const chartData = {
            labels: months,
            datasets: statuses.map((status, index) => {
                const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6'];
                return {
                    label: status,
                    data: months.map(month => {
                        const monthData = weeklyData.months[month];
                        return Object.values(monthData).reduce((sum, week) => sum + (week[status] || 0), 0);
                    }),
                    backgroundColor: colors[index % colors.length] + '20',
                    borderColor: colors[index % colors.length],
                borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                };
            })
        };
        
        try {
            new Chart(weeklyChartCtx, {
                type: 'bar',
                data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                            position: 'top',
                labels: {
                                usePointStyle: true,
                    padding: 20,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: false,
                            grid: {
                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            stacked: false,
                beginAtZero: true,
                grid: {
                                color: 'rgba(0,0,0,0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
        }
    }
});
} catch (error) {
            console.error('Error creating weekly movement chart:', error);
            weeklyChartCtx.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-exclamation-triangle fa-lg mb-2"></i><br>Chart failed to load</div>';
        }
    }
}

// Add loading state to clickable numbers
const clickableNumbers = document.querySelectorAll('.clickable-number');

clickableNumbers.forEach(function(link) {
    link.addEventListener('click', function(e) {
        // Add loading state
        const originalText = this.textContent;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
        this.style.pointerEvents = 'none';
        
        // If the page doesn't load within 3 seconds, restore the original text
        setTimeout(() => {
            if (this.innerHTML.includes('fa-spinner')) {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }
        }, 3000);
    });
});



// Weekly Movement Analysis Export Function
function exportWeeklyData() {
    const weeklyData = @json($weeklyBreakdown ?? []);
    const monthlyRollup = @json($monthlyRollup ?? []);
    
    if (!weeklyData || !weeklyData.months) {
        alert('No data available to export');
        return;
    }
    
    // Prepare CSV data
    let csvContent = 'Weekly Movement Analysis Export\n\n';
    
    // Add export metadata
    csvContent += 'Export Date,' + new Date().toLocaleDateString() + '\n';
    csvContent += 'Generated By,' + '{{ auth()->user()->first_name ?? "System" }} {{ auth()->user()->last_name ?? "" }}' + '\n\n';
    
    // Add monthly summary
    csvContent += 'Monthly Summary\n';
    csvContent += 'Month,Total Movements\n';
    Object.keys(weeklyData.months).forEach(month => {
        const monthData = weeklyData.months[month];
        const totalMovements = Object.values(monthData).reduce((sum, week) => 
            sum + Object.values(week).reduce((weekSum, count) => weekSum + count, 0), 0
        );
        csvContent += `"${month}",${totalMovements}\n`;
    });
    
    csvContent += '\n';
    
    // Add detailed weekly breakdown
    csvContent += 'Detailed Weekly Breakdown\n';
    const statuses = weeklyData.statuses || ['Deployed', 'Disposed', 'New Arrival', 'Returned', 'Transferred'];
    
    // Create header row
    csvContent += 'Month,Week,' + statuses.join(',') + ',Total\n';
    
    // Add data rows
    Object.keys(weeklyData.months).forEach(month => {
        const monthData = weeklyData.months[month];
        Object.keys(monthData).forEach(week => {
            const weekData = monthData[week];
            const weekTotal = Object.values(weekData).reduce((sum, count) => sum + count, 0);
            
            let row = `"${month}","${week}"`;
            statuses.forEach(status => {
                row += `,${weekData[status] || 0}`;
            });
            row += `,${weekTotal}\n`;
            csvContent += row;
        });
    });
    
    // Add monthly rollup data if available
    if (monthlyRollup && monthlyRollup.months) {
        csvContent += '\nMonthly Rollup Summary\n';
        csvContent += 'Month,' + statuses.join(',') + ',Total\n';
        
        Object.keys(monthlyRollup.months).forEach(month => {
            const monthData = monthlyRollup.months[month];
            const monthTotal = Object.values(monthData).reduce((sum, count) => sum + count, 0);
            
            let row = `"${month}"`;
            statuses.forEach(status => {
                row += `,${monthData[status] || 0}`;
            });
            row += `,${monthTotal}\n`;
            csvContent += row;
        });
    }
    
    // Create and download the file
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `weekly_movement_analysis_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Add event listener for export button
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportWeeklyData');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportWeeklyData);
    }
    
    // Initialize live timestamp updates
    initializeLiveTimestamps();
});

// Live Data Update Function
function initializeLiveTimestamps() {
    // Function to update timestamps
    function updateTimestamps() {
        const now = new Date();
        const formattedTime = now.toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        // Update both timestamp elements
        const lastUpdated1 = document.getElementById('lastUpdated1');
        const lastUpdated2 = document.getElementById('lastUpdated2');
        
        if (lastUpdated1) {
            lastUpdated1.textContent = formattedTime;
        }
        if (lastUpdated2) {
            lastUpdated2.textContent = formattedTime;
        }
    }
    
    // Function to fetch live data - COMPREHENSIVE DATA UPDATE
    async function fetchLiveData() {
        console.log('Fetching live data...');
        updateRefreshStatus('updating', 'Updating...');
        
        try {
            const response = await fetch('{{ route("dashboard") }}?live=1', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                console.log('Live data received:', data);
                
                // Update all dashboard data comprehensively
                updateDashboardData(data);
                updateTimestamps();
                
                // Show success status
                updateRefreshStatus('success', 'Live');
                showLiveUpdateIndicator();
                
                // Show success notification
                showRefreshNotification('Dashboard updated successfully', 'success');
            } else {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        } catch (error) {
            console.error('Live data update failed:', error);
            updateRefreshStatus('error', 'Failed');
            showRefreshNotification('Failed to refresh data: ' + error.message, 'error');
            
            // Retry after 10 seconds
            setTimeout(() => {
                console.log('Retrying data fetch...');
                fetchLiveData();
            }, 10000);
        }
    }
    
    // Function to update refresh status indicator
    function updateRefreshStatus(status, text) {
        const refreshStatus = document.getElementById('refreshStatus');
        if (refreshStatus) {
            refreshStatus.className = `refresh-status ${status}`;
            
            let icon = 'fas fa-circle';
            if (status === 'updating') {
                icon = 'fas fa-sync-alt';
            } else if (status === 'success') {
                icon = 'fas fa-check-circle';
            } else if (status === 'error') {
                icon = 'fas fa-exclamation-circle';
            }
            
            refreshStatus.innerHTML = `<i class="${icon}"></i><span>${text}</span>`;
            
            // Reset to live status after 3 seconds if successful
            if (status === 'success') {
                setTimeout(() => {
                    updateRefreshStatus('live', 'Live');
                }, 3000);
            }
        }
    }
    
    // Function to update dashboard data - COMPREHENSIVE UPDATE
    function updateDashboardData(data) {
        console.log('Updating dashboard with fresh data:', data);
        
        // Update asset counts
        if (data.totalAssets !== undefined) {
            const totalAssetsEl = document.querySelector('[data-stat="total-assets"]');
            if (totalAssetsEl) {
                totalAssetsEl.textContent = data.totalAssets.toLocaleString();
            }
            
            // Update any other references to total assets
            const assetRefs = document.querySelectorAll('[data-ref="total-assets"]');
            assetRefs.forEach(el => {
                el.textContent = data.totalAssets.toLocaleString();
            });
        }
        
        if (data.totalUsers !== undefined) {
            const totalUsersEl = document.querySelector('[data-stat="total-users"]');
            if (totalUsersEl) {
                totalUsersEl.textContent = data.totalUsers.toLocaleString();
            }
        }
        
        if (data.totalDepartments !== undefined) {
            const totalDeptsEl = document.querySelector('[data-stat="total-departments"]');
            if (totalDeptsEl) {
                totalDeptsEl.textContent = data.totalDepartments.toLocaleString();
            }
        }
        
        if (data.totalVendors !== undefined) {
            const totalVendorsEl = document.querySelector('[data-stat="total-vendors"]');
            if (totalVendorsEl) {
                totalVendorsEl.textContent = data.totalVendors.toLocaleString();
            }
        }
        
        // Update deployment percentage and related elements
        if (data.deployedAssetsPercentage !== undefined) {
            const deploymentEl = document.querySelector('.deployment-value');
            if (deploymentEl) {
                deploymentEl.textContent = data.deployedAssetsPercentage + '%';
            }
            
            // Update progress bar
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                progressFill.style.width = data.deployedAssetsPercentage + '%';
            }
            
            // Update deployment status badge
            const statusBadge = document.querySelector('.deployment-status .status-badge');
            if (statusBadge) {
                if (data.deployedAssetsPercentage >= 80) {
                    statusBadge.className = 'status-badge success';
                    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Excellent';
                } else if (data.deployedAssetsPercentage >= 60) {
                    statusBadge.className = 'status-badge warning';
                    statusBadge.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Good';
                } else {
                    statusBadge.className = 'status-badge danger';
                    statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Needs Attention';
                }
            }
        }
        
        // Update weekly movement data if available
        if (data.weeklyBreakdown) {
            updateWeeklyMovementData(data.weeklyBreakdown);
        }
        
        // Update recent assets count if available
        if (data.recentAssets) {
            updateRecentAssetsCount(data.recentAssets);
        }
        
        // Update performance insights
        updatePerformanceInsights(data);
        
        // Update any metric cards that might have hardcoded values
        updateMetricCards(data);
        
        // Show live update indicator
        showLiveUpdateIndicator();
    }
    
    // Function to update weekly movement data
    function updateWeeklyMovementData(weeklyData) {
        if (!weeklyData.months) return;
        
        // Update each month's data
        Object.keys(weeklyData.months).forEach(month => {
            const monthData = weeklyData.months[month];
            
            // Find the month container
            const monthContainer = document.querySelector(`[data-month="${month}"]`);
            if (!monthContainer) return;
            
            // Update total movements for the month
            const totalMovements = Object.values(monthData).reduce((sum, week) => 
                sum + Object.values(week).reduce((weekSum, count) => weekSum + count, 0), 0
            );
            
            const totalMovementsEl = monthContainer.querySelector('.total-movements');
            if (totalMovementsEl) {
                totalMovementsEl.textContent = `${totalMovements} movements`;
            }
            
            // Update each week's data
            Object.keys(monthData).forEach(week => {
                const weekData = monthData[week];
                const weekRow = monthContainer.querySelector(`[data-week="${week}"]`);
                if (!weekRow) return;
                
                // Update each status count in the week
                Object.keys(weekData).forEach(status => {
                    const count = weekData[status];
                    const statusCell = weekRow.querySelector(`[data-status="${status}"]`);
                    if (statusCell) {
                        const countEl = statusCell.querySelector('.week-count');
                        if (countEl) {
                            if (count > 0) {
                                countEl.textContent = count;
                                countEl.className = 'movement-count week-count';
                            } else {
                                countEl.textContent = '—';
                                countEl.className = 'no-movement week-count';
                            }
                        }
                        
                        // Update week total
                        const weekTotal = Object.values(weekData).reduce((sum, c) => sum + c, 0);
                        const weekBadge = weekRow.querySelector('.week-badge');
                        if (weekBadge) {
                            weekBadge.textContent = weekTotal;
                        }
                    }
                });
            });
            
            // Update header totals
            const statuses = weeklyData.statuses || ['Deployed', 'Disposed', 'New Arrival', 'Returned', 'Transferred'];
            statuses.forEach(status => {
                const totalForStatus = Object.values(monthData).reduce((sum, week) => 
                    sum + (week[status] || 0), 0
                );
                
                const headerCell = monthContainer.querySelector(`[data-header-status="${status}"]`);
                if (headerCell) {
                    const totalEl = headerCell.querySelector('.header-total');
                    if (totalEl) {
                        totalEl.textContent = totalForStatus;
                    }
                }
            });
        });
        
        // Trigger visual update
        const weeklyTable = document.querySelector('.weekly-table-container');
        if (weeklyTable) {
            weeklyTable.style.opacity = '0.7';
            setTimeout(() => {
                weeklyTable.style.opacity = '1';
            }, 500);
        }
    }
    
    // Function to update recent assets count
    function updateRecentAssetsCount(recentAssets) {
        const recentAssetsCount = document.querySelector('.recent-assets-count .badge');
        if (recentAssetsCount && recentAssets.length !== undefined) {
            recentAssetsCount.textContent = recentAssets.length;
        }
    }
    
    // Function to update performance insights
    function updatePerformanceInsights(data) {
        // Update Asset Utilization
        if (data.deployedAssetsPercentage !== undefined) {
            const assetUtilizationText = document.getElementById('assetUtilizationText');
            if (assetUtilizationText) {
                assetUtilizationText.textContent = `${data.deployedAssetsPercentage}% of assets are currently deployed and active`;
            }
        }
        
        // Update Asset Health (problematic assets)
        if (data.totalAssets !== undefined) {
            // Calculate problematic percentage
            const problematicCount = data.problematicAssets || 0;
            const problematicPercentage = data.totalAssets > 0 ? 
                Math.round((problematicCount / data.totalAssets) * 100) : 0;
            
            const assetHealthText = document.getElementById('assetHealthText');
            if (assetHealthText) {
                assetHealthText.textContent = `${problematicPercentage}% of assets are currently problematic`;
            }
        }
    }
    
    // Function to update metric cards with fresh data
    function updateMetricCards(data) {
        // Update any metric cards that might have hardcoded values
        const metricCards = document.querySelectorAll('.metric-card');
        metricCards.forEach(card => {
            const valueEl = card.querySelector('.metric-value');
            const labelEl = card.querySelector('.metric-label');
            
            if (valueEl && labelEl) {
                const label = labelEl.textContent.toLowerCase();
                
                // Update based on label content
                if (label.includes('total assets') && data.totalAssets !== undefined) {
                    valueEl.textContent = data.totalAssets.toLocaleString();
                } else if (label.includes('users') && data.totalUsers !== undefined) {
                    valueEl.textContent = data.totalUsers.toLocaleString();
                } else if (label.includes('departments') && data.totalDepartments !== undefined) {
                    valueEl.textContent = data.totalDepartments.toLocaleString();
                } else if (label.includes('vendors') && data.totalVendors !== undefined) {
                    valueEl.textContent = data.totalVendors.toLocaleString();
                } else if (label.includes('deployed') && data.deployedAssetsPercentage !== undefined) {
                    valueEl.textContent = data.deployedAssetsPercentage + '%';
                }
            }
        });
        
        // Update any other dynamic content
        updateDynamicContent(data);
    }
    
    // Function to update any other dynamic content
    function updateDynamicContent(data) {
        // Update any other elements that might contain dynamic data
        const dynamicElements = document.querySelectorAll('[data-dynamic]');
        dynamicElements.forEach(el => {
            const key = el.getAttribute('data-dynamic');
            if (data[key] !== undefined) {
                el.textContent = data[key];
            }
        });
    }
    
    // Function to show live update indicator
    function showLiveUpdateIndicator() {
        const indicator = document.getElementById('liveIndicator');
        if (indicator) {
            indicator.style.opacity = '1';
            indicator.style.transform = 'scale(1)';
            indicator.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Data Refreshed';
            
            // Add success checkmark after update
            setTimeout(() => {
                indicator.innerHTML = '<i class="fas fa-check-circle text-success"></i> Data Updated';
                indicator.style.background = 'rgba(16, 185, 129, 0.1)';
                indicator.style.color = '#059669';
            }, 1000);
            
            setTimeout(() => {
                indicator.style.opacity = '0.7';
                indicator.style.transform = 'scale(0.95)';
                indicator.innerHTML = '<i class="fas fa-circle text-success"></i> Live';
                indicator.style.background = 'rgba(255, 255, 255, 0.95)';
                indicator.style.color = '#059669';
            }, 4000);
        }
        
        // Show notification
        showRefreshNotification();
    }
    
    // Function to show refresh notification
    function showRefreshNotification() {
        // Create notification element
        const notification = document.createElement('div');
        notification.id = 'refreshNotification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-check-circle text-success"></i>
                <span>Dashboard data refreshed successfully</span>
                <small>${new Date().toLocaleTimeString()}</small>
            </div>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            font-size: 14px;
            font-weight: 500;
            z-index: 1001;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            min-width: 250px;
        `;
        
        // Add styles for notification content
        const style = document.createElement('style');
        style.textContent = `
            .notification-content {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .notification-content small {
                display: block;
                font-size: 11px;
                opacity: 0.8;
                margin-top: 2px;
            }
        `;
        document.head.appendChild(style);
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 4 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }
    
    // Update timestamps immediately
    updateTimestamps();
    
    // Update timestamps every minute
    setInterval(updateTimestamps, 60000);
    
    // Soft refresh every 30 seconds - updates all data without page reload
    setInterval(fetchLiveData, 30000);
    
    // Also fetch data immediately on page load
    setTimeout(fetchLiveData, 2000);
    
    // Add visual indicator for live updates
    addLiveUpdateIndicator();
}

// Add visual indicator for live updates
function addLiveUpdateIndicator() {
    // Create live indicator element
    const liveIndicator = document.createElement('div');
    liveIndicator.id = 'liveIndicator';
    liveIndicator.innerHTML = '<i class="fas fa-circle text-success"></i> Live';
    liveIndicator.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 8px 12px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        font-size: 12px;
        font-weight: 500;
        color: #059669;
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    `;
    
    // Add pulsing animation to the dot
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        #liveIndicator i {
            animation: pulse 2s infinite;
        }
    `;
    document.head.appendChild(style);
    
    // Add to page
    document.body.appendChild(liveIndicator);
    
    // Hide indicator after 5 seconds
    setTimeout(() => {
        liveIndicator.style.opacity = '0.7';
        liveIndicator.style.transform = 'scale(0.95)';
    }, 5000);
}
</script>
@endpush