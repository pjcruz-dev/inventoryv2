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
                                        <span class="stat-value">{{ now()->format('M d, Y \a\t g:i A') }}</span>
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
                                    <span class="target">Target: 80%</span>
                                </div>
                            </div>
                            <div class="deployment-status">
                                @if($deployedAssetsPercentage >= 80)
                                    <span class="status-badge success">
                                        <i class="fas fa-check-circle"></i> Excellent
                                    </span>
                                @elseif($deployedAssetsPercentage >= 60)
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
                    <div class="metric-value">{{ number_format($totalAssets) }}</div>
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
                    <div class="metric-value">{{ number_format($totalUsers) }}</div>
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
                    <div class="metric-value">{{ number_format($totalDepartments) }}</div>
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
                    <div class="metric-value">{{ number_format($totalVendors) }}</div>
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
        
    <!-- Deployment Status & Recent Assets Row -->
    <div class="row mb-4">
        <!-- Deployment Status -->
        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="dashboard-card h-100 deployment-status-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="deployment-status-icon">
                            <i class="fas fa-rocket"></i>
                            </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Deployment Status</h6>
                            <small class="text-muted">Asset deployment overview</small>
                        </div>
                        </div>
                    <div class="deployment-status-badge {{ $deployedAssetsPercentage >= 80 ? 'high' : ($deployedAssetsPercentage >= 50 ? 'medium' : 'low') }}">
                        {{ $deployedAssetsPercentage >= 80 ? 'Excellent' : ($deployedAssetsPercentage >= 50 ? 'Good' : 'Needs Attention') }}
                    </div>
                </div>
                
                <!-- Enhanced Progress Ring -->
                <div class="deployment-progress-container">
                    <div class="progress-ring-wrapper">
                        <svg class="progress-ring" width="140" height="140">
                            <circle class="progress-ring-bg" stroke="#f1f5f9" stroke-width="12" fill="transparent" r="58" cx="70" cy="70"/>
                            <circle class="progress-ring-fill" 
                                    stroke="{{ $deployedAssetsPercentage >= 80 ? '#10b981' : ($deployedAssetsPercentage >= 50 ? '#f59e0b' : '#ef4444') }}" 
                                    stroke-width="12" fill="transparent" r="58" cx="70" cy="70" 
                                    stroke-dasharray="{{ (2 * 3.14159 * 58) }}" 
                                    stroke-dashoffset="{{ (2 * 3.14159 * 58) - (($deployedAssetsPercentage / 100) * (2 * 3.14159 * 58)) }}"
                                    stroke-linecap="round"
                                    style="transition: stroke-dashoffset 1s ease-in-out;"/>
                        </svg>
                        <div class="progress-ring-content">
                            <div class="deployment-percentage">{{ $deployedAssetsPercentage }}%</div>
                            <div class="deployment-label">Deployed</div>
                            <div class="deployment-trend">
                                @if($deployedAssetsPercentage >= 80)
                                    <i class="fas fa-arrow-up text-success"></i>
                                    <span class="text-success">Optimal</span>
                                @elseif($deployedAssetsPercentage >= 50)
                                    <i class="fas fa-minus text-warning"></i>
                                    <span class="text-warning">Moderate</span>
                                @else
                                    <i class="fas fa-arrow-down text-danger"></i>
                                    <span class="text-danger">Low</span>
                                @endif
                </div>
            </div>
        </div>
    </div>

                <!-- Enhanced Stats Grid -->
                <div class="deployment-stats-grid">
                    <div class="stat-item deployed">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ \App\Models\Asset::whereIn('status', ['deployed', 'active', 'assigned', 'in_use'])->count() }}</div>
                            <div class="stat-label">Deployed Assets</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-item new-arrival">
                        <div class="stat-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ \App\Models\Asset::where('movement', 'New Arrival')->count() }}</div>
                            <div class="stat-label">New Arrivals</div>
                            <div class="stat-change info">
                                <i class="fas fa-info-circle"></i>
                                <span>Available</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-item pending">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ \App\Models\Asset::where('status', 'Pending Confirmation')->count() }}</div>
                            <div class="stat-label">Pending Confirmation</div>
                            <div class="stat-change warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Awaiting</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="deployment-action">
                    <a href="{{ route('assets.index', ['status' => 'pending']) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-tasks me-2"></i>Manage Deployment
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Assets -->
        <div class="col-xl-8 col-lg-6 mb-3">
            <div class="dashboard-card h-100 recent-assets-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <div class="recent-assets-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Recent Assets</h6>
                            <small class="text-muted">Latest asset additions</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="recent-assets-count">
                            <span class="badge bg-primary">{{ $recentAssets->count() }}</span>
                            <small class="text-muted ms-1">items</small>
                        </div>
                        <a href="{{ route('assets.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View All
                        </a>
                    </div>
                </div>
                
                <div class="recent-assets-list">
                    @if($recentAssets->count() > 0)
                        @foreach($recentAssets as $index => $asset)
                            <div class="recent-asset-item" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                <div class="asset-main-info">
                                    <div class="asset-icon-wrapper">
                                        <div class="asset-type-icon {{ strtolower($asset->category->name ?? 'default') }}">
                                            @php
                                                $iconMap = [
                                                    'computer' => 'fas fa-desktop',
                                                    'monitor' => 'fas fa-tv',
                                                    'printer' => 'fas fa-print',
                                                    'phone' => 'fas fa-phone',
                                                    'tablet' => 'fas fa-tablet-alt',
                                                    'default' => 'fas fa-box'
                                                ];
                                                $categoryName = strtolower($asset->category->name ?? 'default');
                                                $icon = $iconMap[$categoryName] ?? $iconMap['default'];
                                            @endphp
                                            <i class="{{ $icon }}"></i>
                                                </div>
                                        <div class="asset-priority-indicator {{ $asset->status === 'problematic' ? 'high' : ($asset->status === 'deployed' ? 'low' : 'medium') }}"></div>
                                            </div>
                                    
                                    <div class="asset-details">
                                        <div class="asset-header">
                                            <h6 class="asset-name">{{ $asset->name }}</h6>
                                            <div class="asset-meta">
                                                <span class="asset-category">{{ $asset->category->name ?? 'Uncategorized' }}</span>
                                                <span class="asset-divider">•</span>
                                                <span class="asset-time">{{ $asset->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="asset-status-row">
                                            <div class="asset-status-badge {{ $asset->status === 'deployed' ? 'deployed' : ($asset->status === 'problematic' ? 'problematic' : 'pending') }}">
                                                <i class="fas fa-circle"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                                </div>
                                            
                                            @if($asset->assignedUser)
                                                <div class="asset-assignment">
                                                    <i class="fas fa-user"></i>
                                                    <span>{{ $asset->assignedUser->first_name ?? 'Unknown User' }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="asset-actions">
                                    <div class="asset-actions-group">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @if($asset->status !== 'deployed')
                                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-secondary btn-sm" title="Edit Asset">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="asset-quick-info">
                                        <small class="text-muted">
                                            ID: {{ $asset->id }}
                                        </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        
                        <!-- View More Button -->
                        @if($recentAssets->count() >= 5)
                            <div class="recent-assets-footer">
                                <a href="{{ route('assets.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-arrow-right me-2"></i>View All Assets
                                </a>
                        </div>
                        @endif
                    @else
                        <div class="recent-assets-empty">
                            <div class="empty-state-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h6 class="empty-state-title">No Recent Assets</h6>
                            <p class="empty-state-description">No assets have been added recently.</p>
                            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Add First Asset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Monthly Analysis Row -->
    <div class="row mb-4">
        <!-- Monthly Status Overview -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="dashboard-card h-100 monthly-overview-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="monthly-overview-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Monthly Status Overview</h6>
                            <small class="text-muted">Asset activity breakdown</small>
                        </div>
                    </div>
                    <div class="monthly-filter-group">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle monthly-filter-btn" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar-alt me-2"></i>Filter Month
                            </button>
                            <ul class="dropdown-menu monthly-filter-menu">
                                @for($i = 0; $i < 6; $i++)
                                    @php
                                        $date = now()->subMonths($i);
                                        $monthName = $date->format('F Y');
                                        $isCurrent = $i === 0;
                                    @endphp
                                    <li>
                                        <a class="dropdown-item {{ $isCurrent ? 'active' : '' }}" href="{{ route('dashboard', ['month' => $date->month, 'year' => $date->year]) }}">
                                            <i class="fas fa-circle me-2 {{ $isCurrent ? 'text-primary' : 'text-muted' }}"></i>
                                            {{ $monthName }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>
        
                <div class="monthly-analysis">
                    @if(!empty($monthlyRollup['months']))
                        @foreach($monthlyRollup['months'] as $monthName => $data)
                            @php
                                $totalForMonth = array_sum(array_column($data, 'count'));
                                $deployedCount = $data['Deployed']['count'] ?? 0;
                                $disposedCount = $data['Disposed']['count'] ?? 0;
                                $newArrivalCount = $data['New Arrival']['count'] ?? 0;
                                $returnedCount = $data['Returned']['count'] ?? 0;
                                $transferredCount = $data['Transferred']['count'] ?? 0;
                                
                                // Calculate health score based on positive vs negative activities
                                $positiveActivities = $deployedCount + $newArrivalCount;
                                $negativeActivities = $disposedCount;
                                $neutralActivities = $returnedCount + $transferredCount;
                                $healthScore = $totalForMonth > 0 ? round((($positiveActivities - $negativeActivities) / $totalForMonth) * 100) : 0;
                            @endphp
                            
                            <div class="monthly-period-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <!-- Enhanced Header -->
                                <div class="monthly-period-header">
                                    <div class="monthly-period-title">
                                        <div class="month-header">
                                            <h6 class="month-name">{{ $monthName }}</h6>
                                            <div class="month-badge">
                                                <span class="total-activities">{{ $totalForMonth }}</span>
                                                <small>activities</small>
                                            </div>
                                        </div>
                                        <div class="monthly-summary">
                                            @if($totalForMonth > 0)
                                                <div class="health-indicator">
                                                    <div class="health-icon {{ $healthScore >= 70 ? 'excellent' : ($healthScore >= 40 ? 'good' : ($healthScore >= 0 ? 'moderate' : 'poor')) }}">
                                                        <i class="fas fa-heartbeat"></i>
                                                    </div>
                                                    <div class="health-info">
                                                        <span class="health-score">{{ $healthScore }}%</span>
                                                        <small class="health-label">Health Score</small>
                                                    </div>
                                                </div>
                                                <div class="activity-bar">
                                                    <div class="activity-fill" style="width: {{ min(100, ($totalForMonth / 100) * 100) }}%"></div>
                                                    <div class="activity-pulse"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($totalForMonth > 0)
                                    <!-- Monthly Chart Section -->
                                    <div class="monthly-chart-section">
                                        <div class="chart-container">
                                            <canvas id="monthlyChart_{{ $loop->index }}" class="monthly-chart-canvas"></canvas>
                                        </div>
                                        <div class="chart-legend">
                                            @php
                                                $statusConfigs = [
                                                    'Deployed' => ['color' => '#10b981', 'icon' => 'fas fa-check-circle'],
                                                    'New Arrival' => ['color' => '#3b82f6', 'icon' => 'fas fa-plus-circle'],
                                                    'Returned' => ['color' => '#06b6d4', 'icon' => 'fas fa-undo'],
                                                    'Transferred' => ['color' => '#f59e0b', 'icon' => 'fas fa-exchange-alt'],
                                                    'Disposed' => ['color' => '#ef4444', 'icon' => 'fas fa-trash']
                                                ];
                                            @endphp
                                            @foreach($statusConfigs as $status => $config)
                                                @if(($data[$status]['count'] ?? 0) > 0)
                                                    <div class="legend-item">
                                                        <div class="legend-color" style="background-color: {{ $config['color'] }}"></div>
                                                        <i class="{{ $config['icon'] }}"></i>
                                                        <span>{{ $status }}</span>
                                                        <span class="legend-count">{{ $data[$status]['count'] ?? 0 }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Enhanced Status Grid -->
                                    <div class="monthly-status-grid">
                                        @php
                                            $statusConfigs = [
                                                'Deployed' => ['color' => 'success', 'icon' => 'fas fa-check-circle', 'label' => 'Deployed', 'count' => $deployedCount],
                                                'New Arrival' => ['color' => 'primary', 'icon' => 'fas fa-plus-circle', 'label' => 'New Arrival', 'count' => $newArrivalCount],
                                                'Returned' => ['color' => 'info', 'icon' => 'fas fa-undo', 'label' => 'Returned', 'count' => $returnedCount],
                                                'Transferred' => ['color' => 'warning', 'icon' => 'fas fa-exchange-alt', 'label' => 'Transferred', 'count' => $transferredCount],
                                                'Disposed' => ['color' => 'danger', 'icon' => 'fas fa-trash', 'label' => 'Disposed', 'count' => $disposedCount]
                                            ];
                                        @endphp
                                        
                                        @foreach($statusConfigs as $status => $config)
                                            @if($config['count'] > 0)
                                                @php
                                                    $statusData = $data[$status] ?? ['count' => 0, 'percentage' => 0];
                                                @endphp
                                                <div class="status-item {{ $config['color'] }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                                    <div class="status-icon-wrapper">
                                                        <div class="status-icon">
                                                            <i class="{{ $config['icon'] }}"></i>
                                                        </div>
                                                        <div class="status-glow"></div>
                                                    </div>
                                                    <div class="status-content">
                                                        <div class="status-count">{{ $statusData['count'] }}</div>
                                                        <div class="status-label">{{ $config['label'] }}</div>
                                                        <div class="status-percentage">{{ $statusData['percentage'] }}%</div>
                                                    </div>
                                                    <div class="status-progress">
                                                        <div class="progress-track">
                                                            <div class="progress-fill" style="width: {{ $statusData['percentage'] }}%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="status-trend">
                                                        @if($status === 'Deployed' || $status === 'New Arrival')
                                                            <i class="fas fa-arrow-up text-success"></i>
                                                        @elseif($status === 'Disposed')
                                                            <i class="fas fa-arrow-down text-danger"></i>
                                                        @else
                                                            <i class="fas fa-minus text-warning"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Enhanced Empty State -->
                                    <div class="monthly-empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-chart-pie"></i>
                                            <div class="empty-pulse"></div>
                                        </div>
                                        <h6 class="empty-title">No Activity</h6>
                                        <p class="empty-description">No asset activities recorded for this month</p>
                                        <div class="empty-actions">
                                            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus me-2"></i>Add Asset
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <!-- Global Empty State -->
                        <div class="monthly-empty-state global">
                            <div class="empty-icon">
                                <i class="fas fa-chart-pie"></i>
                                <div class="empty-pulse"></div>
                            </div>
                            <h6 class="empty-title">No Monthly Data</h6>
                            <p class="empty-description">No monthly activity data available</p>
                            <div class="empty-actions">
                                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add First Asset
                                </a>
                                <a href="{{ route('assets.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>View Assets
                                </a>
                            </div>
                        </div>
                    @endif
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
                    <div class="weekly-controls">
                        <button class="btn btn-outline-primary btn-sm weekly-chart-toggle" type="button" id="weeklyChartToggleBtn">
                            <i class="fas fa-chart-bar me-2"></i>
                            <span class="chart-toggle-text">Chart View</span>
                        </button>
                    </div>
                </div>
                
                <!-- Enhanced Chart View -->
                <div class="collapse mb-4" id="weeklyChart" style="display: none;">
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
                            <div class="weekly-period-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
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
                                                            <th class="status-header {{ $statusConfig['color'] }}">
                                                                <div class="status-header-content">
                                                                    <i class="{{ $statusConfig['icon'] }}"></i>
                                                                    <span>{{ $status }}</span>
                                                                    <small>{{ array_sum(array_column($weeks, $status)) }}</small>
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
                                                        <tr class="weekly-row {{ $weekTotal > 0 ? 'has-activity' : 'no-activity' }}">
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
                                                                <td class="status-cell {{ $statusConfig }}">
                                                            @if($count > 0)
                                                                <a href="{{ route('dashboard.asset-movements', [
                                                                    'week' => $week,
                                                                    'status' => $status,
                                                                    'month' => $monthNumber,
                                                                    'year' => $year
                                                                ]) }}" 
                                                                           class="movement-link" 
                                                                   title="Click to view {{ $count }} {{ strtolower($status) }} assets">
                                                                            <span class="movement-count">{{ $count }}</span>
                                                                </a>
                                                            @else
                                                                        <span class="no-movement">—</span>
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
                        <small class="text-muted">Last updated: {{ now()->format('M d, Y \a\t g:i A') }}</small>
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

/* Weekly Chart Collapsible Styles */
.weekly-chart-container {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.weekly-chart-toggle {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.weekly-chart-toggle:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.weekly-chart-toggle:active {
    transform: translateY(0);
}

.weekly-chart-toggle i {
    transition: all 0.3s ease;
}

.chart-toggle-text {
    transition: all 0.3s ease;
}

.collapse.show .weekly-chart-container {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chart-wrapper {
    position: relative;
    height: 300px;
    margin-top: 1rem;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.chart-title {
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.chart-legend {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.legend-item i {
    font-size: 0.75rem;
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
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// Weekly Movement Chart (Bar Chart) - Collapsible
let weeklyMovementChart = null;
const weeklyChartCtx = document.getElementById('weeklyMovementChart');
const weeklyData = @json($weeklyBreakdown ?? []);

// Function to create the chart
function createWeeklyMovementChart() {
    if (!weeklyChartCtx || weeklyMovementChart) {
        return;
    }
    
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
            weeklyMovementChart = new Chart(weeklyChartCtx, {
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

// Function to destroy the chart
function destroyWeeklyMovementChart() {
    if (weeklyMovementChart) {
        weeklyMovementChart.destroy();
        weeklyMovementChart = null;
    }
}

// Handle chart toggle manually
document.addEventListener('DOMContentLoaded', function() {
    const weeklyChartCollapse = document.getElementById('weeklyChart');
    const weeklyChartToggle = document.getElementById('weeklyChartToggleBtn');
    const chartToggleText = document.querySelector('.chart-toggle-text');
    
    if (weeklyChartCollapse && weeklyChartToggle) {
        let isChartVisible = false;
        
        weeklyChartToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (isChartVisible) {
                // Hide the chart
                weeklyChartCollapse.style.display = 'none';
                weeklyChartCollapse.classList.remove('show');
                destroyWeeklyMovementChart();
                
                // Update button
                if (chartToggleText) {
                    chartToggleText.textContent = 'Chart View';
                }
                weeklyChartToggle.querySelector('i').className = 'fas fa-chart-bar me-2';
                isChartVisible = false;
            } else {
                // Show the chart
                weeklyChartCollapse.style.display = 'block';
                weeklyChartCollapse.classList.add('show');
                
                // Create chart after a short delay
                setTimeout(() => {
                    try {
                        createWeeklyMovementChart();
                    } catch (error) {
                        console.error('Error creating chart:', error);
                        // Show error message
                        const chartContainer = document.querySelector('.weekly-chart-container');
                        if (chartContainer) {
                            chartContainer.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-exclamation-triangle fa-lg mb-2"></i><br>Chart failed to load</div>';
                        }
                    }
                }, 100);
                
                // Update button
                if (chartToggleText) {
                    chartToggleText.textContent = 'Hide Chart';
                }
                weeklyChartToggle.querySelector('i').className = 'fas fa-eye-slash me-2';
                isChartVisible = true;
            }
        });
    }
});

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

// Monthly Status Charts
document.addEventListener('DOMContentLoaded', function() {
    const monthlyCharts = document.querySelectorAll('.monthly-chart-canvas');
    
    monthlyCharts.forEach(function(canvas, index) {
        const ctx = canvas.getContext('2d');
        const monthlyData = @json($monthlyRollup ?? []);
        
        if (monthlyData && monthlyData.months) {
            const months = Object.keys(monthlyData.months);
            const currentMonthData = monthlyData.months[months[index]];
            
            if (currentMonthData) {
                const statusConfigs = {
                    'Deployed': { color: '#10b981', icon: 'fas fa-check-circle' },
                    'New Arrival': { color: '#3b82f6', icon: 'fas fa-plus-circle' },
                    'Returned': { color: '#06b6d4', icon: 'fas fa-undo' },
                    'Transferred': { color: '#f59e0b', icon: 'fas fa-exchange-alt' },
                    'Disposed': { color: '#ef4444', icon: 'fas fa-trash' }
                };
                
                const labels = [];
                const data = [];
                const colors = [];
                const hoverColors = [];
                
                Object.keys(statusConfigs).forEach(status => {
                    const statusData = currentMonthData[status];
                    if (statusData && statusData.count > 0) {
                        labels.push(status);
                        data.push(statusData.count);
                        colors.push(statusConfigs[status].color);
                        hoverColors.push(statusConfigs[status].color + '80');
                    }
                });
                
                if (data.length > 0) {
                    try {
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: data,
                                    backgroundColor: colors,
                                    borderColor: colors,
                                    borderWidth: 2,
                                    hoverBackgroundColor: hoverColors,
                                    hoverBorderWidth: 3,
                                    hoverOffset: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '60%',
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                        borderColor: '#667eea',
                                        borderWidth: 1,
                                        cornerRadius: 8,
                                        displayColors: true,
                                        callbacks: {
                                            title: function(context) {
                                                return context[0].label;
                                            },
                                            label: function(context) {
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                                return `${context.parsed} assets (${percentage}%)`;
                                            }
                                        }
                                    }
                                },
                                animation: {
                                    animateRotate: true,
                                    animateScale: true,
                                    duration: 1500,
                                    easing: 'easeInOutQuart'
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                },
                                elements: {
                                    arc: {
                                        borderWidth: 2
                                    }
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error creating monthly chart:', error);
                        canvas.parentElement.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-exclamation-triangle fa-lg mb-2"></i><br>Chart failed to load</div>';
                    }
                } else {
                    canvas.parentElement.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-chart-pie fa-lg mb-2"></i><br>No data to display</div>';
                }
            }
        }
    });
});
</script>
@endpush