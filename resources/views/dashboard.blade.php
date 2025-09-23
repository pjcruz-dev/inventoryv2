@extends('layouts.app')

@section('title', 'Dashboard')
{{-- Page title removed - using breadcrumbs instead --}}

@section('page-actions')
    <div class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-column flex-md-row gap-2 align-items-stretch align-items-md-center w-100">
            <div class="row g-2 w-100">
                <div class="col-6 col-md-3">
                    <select name="month" class="form-select form-select-sm dashboard-filter-select">
                <option value="">All Months</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </option>
                @endfor
            </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="year" class="form-select form-select-sm dashboard-filter-select">
                <option value="">All Years</option>
                @for($year = date('Y'); $year >= 2020; $year--)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="entity" class="form-select form-select-sm dashboard-filter-select">
                <option value="">All Entities</option>
                @foreach($entities as $entity)
                    <option value="{{ $entity }}" {{ request('entity') == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                @endforeach
            </select>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-dashboard-filter btn-sm flex-fill d-flex align-items-center justify-content-center">
                            <i class="fas fa-filter me-1"></i>
                            <span class="d-none d-sm-inline">Filter</span>
            </button>
            @if(request()->hasAny(['month', 'year', 'entity']))
                            <a href="{{ route('dashboard') }}" class="btn btn-dashboard-clear btn-sm d-flex align-items-center justify-content-center">
                                <i class="fas fa-times"></i>
                                <span class="d-none d-sm-inline ms-1">Clear</span>
                </a>
            @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('content')
<div class="container-fluid dashboard-container">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h2 class="welcome-title">Welcome back, {{ auth()->user()->first_name }}! 👋</h2>
                        <p class="welcome-subtitle">Here's what's happening with your inventory today.</p>
                            </div>
                    <div class="welcome-time">
                        <div class="time-display">{{ now()->format('l, F j, Y') }}</div>
                        <div class="time-ago">Last updated {{ now()->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- Key Metrics Row -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card primary">
                <div class="metric-icon">
                    <i class="fas fa-boxes"></i>
                            </div>
                <div class="metric-content">
                    <div class="metric-number">{{ number_format($totalAssets) }}</div>
                    <div class="metric-label">Total Assets</div>
                    @if($totalAssets > 0)
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Active in system</span>
                        </div>
                    @else
                        <div class="metric-change neutral">
                            <i class="fas fa-info-circle"></i>
                            <span>No assets yet</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card success">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                            </div>
                <div class="metric-content">
                    <div class="metric-number">{{ number_format($totalUsers) }}</div>
                    <div class="metric-label">Active Users</div>
                    @if($totalUsers > 0)
                        <div class="metric-change positive">
                            <i class="fas fa-users"></i>
                            <span>Registered users</span>
                        </div>
                    @else
                        <div class="metric-change neutral">
                            <i class="fas fa-user-plus"></i>
                            <span>Add users</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card info">
                <div class="metric-icon">
                    <i class="fas fa-building"></i>
                            </div>
                <div class="metric-content">
                    <div class="metric-number">{{ number_format($totalDepartments) }}</div>
                    <div class="metric-label">Departments</div>
                    @if($totalDepartments > 0)
                        <div class="metric-change positive">
                            <i class="fas fa-building"></i>
                            <span>Organized structure</span>
                        </div>
                    @else
                        <div class="metric-change neutral">
                            <i class="fas fa-building"></i>
                            <span>Setup departments</span>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card warning">
                <div class="metric-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-number">{{ number_format($totalVendors) }}</div>
                    <div class="metric-label">Vendors</div>
                    @if($totalVendors > 0)
                        <div class="metric-change positive">
                            <i class="fas fa-truck"></i>
                            <span>Active suppliers</span>
                        </div>
                    @else
                        <div class="metric-change neutral">
                            <i class="fas fa-truck"></i>
                            <span>Add vendors</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-5">
        <!-- Asset Status Overview -->
        <div class="col-xl-4 col-lg-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Asset Status Overview
                    </h5>
                    <div class="card-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshAssetStatus()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="status-overview">
                        <div class="status-main">
                            <div class="status-percentage">{{ $activeAssetsPercentage }}%</div>
                            <div class="status-label">Assets Active</div>
                    </div>
                        <div class="status-progress">
                            <div class="progress-ring">
                                <svg class="progress-ring-svg" width="120" height="120">
                                    <circle class="progress-ring-circle-bg" cx="60" cy="60" r="50"/>
                                    <circle class="progress-ring-circle" cx="60" cy="60" r="50" 
                                            style="stroke-dasharray: {{ 2 * pi() * 50 }}; stroke-dashoffset: {{ 2 * pi() * 50 * (1 - $activeAssetsPercentage / 100) }};"/>
                                </svg>
                                <div class="progress-text">{{ $activeAssetsPercentage }}%</div>
                    </div>
                    </div>
                </div>
                    <div class="status-breakdown">
                        @foreach($statusBreakdown as $status => $count)
                        <div class="status-item">
                            <div class="status-dot status-{{ strtolower(str_replace(' ', '-', $status)) }}"></div>
                            <span class="status-name">{{ $status }}</span>
                            <span class="status-count">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Enhanced Quick Actions Panel -->
                    <div class="quick-actions-panel">
                        <div class="quick-actions-header">
                            <h6 class="quick-actions-title">
                                <i class="fas fa-rocket me-2"></i>Quick Actions
                            </h6>
                            <div class="quick-actions-subtitle">Most used features</div>
                        </div>
                        
                        <div class="quick-actions-grid-enhanced">
                            @can('create_assets')
                            <a href="{{ route('assets.create') }}" class="quick-action-card primary-card" data-tooltip="Create a new asset">
                                <div class="action-icon-wrapper">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Add Asset</div>
                                    <div class="action-subtitle">Create new asset</div>
                                    <div class="action-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                            @endcan
                            
                            @can('view_assets')
                            <a href="{{ route('assets.index') }}" class="quick-action-card success-card" data-tooltip="Browse all assets">
                                <div class="action-icon-wrapper">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View All</div>
                                    <div class="action-subtitle">Browse assets</div>
                                    <div class="action-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                            @endcan
                            
                            @can('import_export_access')
                            <a href="{{ route('import-export.interface') }}" class="quick-action-card info-card" data-tooltip="Bulk import assets">
                                <div class="action-icon-wrapper">
                                    <i class="fas fa-file-import"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Import</div>
                                    <div class="action-subtitle">Bulk import</div>
                                    <div class="action-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                            @endcan
                            
                            @can('view_accountability_forms')
                            <a href="{{ route('accountability.index') }}" class="quick-action-card warning-card" data-tooltip="Generate accountability forms">
                                <div class="action-icon-wrapper">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Reports</div>
                                    <div class="action-subtitle">Generate forms</div>
                                    <div class="action-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Weekly Breakdown -->
        <div class="col-xl-8 col-lg-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-week me-2"></i>
                        Weekly Breakdown
                    </h5>
                    <div class="card-actions">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="filterWeeklyBreakdown('current')">
                                <i class="fas fa-calendar-day me-1"></i>Current Month
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterWeeklyBreakdown('previous')">
                                <i class="fas fa-chevron-left me-1"></i>Previous
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($weeklyBreakdown))
                        @foreach($weeklyBreakdown as $month => $monthData)
                        <div class="weekly-breakdown-container">
                            <h6 class="breakdown-month-title">{{ $month }}</h6>
                            <div class="table-responsive">
                                <table class="table table-hover weekly-breakdown-table">
                                    <thead>
                                        <tr>
                                            <th class="week-column">WEEK</th>
                                            <th class="deployed-column">DEPLOYED</th>
                                            <th class="disposed-column">DISPOSED</th>
                                            <th class="new-arrival-column">NEW ARRIVAL</th>
                                            <th class="returned-column">RETURNED</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthData as $week => $weekData)
                                            @if(!str_contains($week, 'Week 0'))
                                            <tr>
                                                <td class="week-cell">
                                                    <strong>{{ $week }}</strong>
                                                </td>
                                                <td class="deployed-cell">
                                                    @php
                                                        $deployedCount = $weekData['Deployed'] ?? 0;
                                                        $monthNumber = date('n', strtotime($month));
                                                        $year = date('Y', strtotime($month));
                                                        $weekNumber = (int)str_replace('Week ', '', $week);
                                                    @endphp
                                                    @if($deployedCount > 0)
                                                        <a href="{{ route('dashboard.asset-movements', [
                                                            'week' => 'Week ' . $weekNumber,
                                                            'status' => 'Deployed',
                                                            'month' => $monthNumber,
                                                            'year' => $year
                                                        ]) }}" 
                                                           class="text-decoration-none fw-bold text-success clickable-number" 
                                                           title="Click to view {{ $deployedCount }} deployed assets">
                                                            {{ $deployedCount }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">{{ $deployedCount }}</span>
                                                    @endif
                                                </td>
                                                <td class="disposed-cell">
                                                    @php
                                                        $disposedCount = $weekData['Disposed'] ?? 0;
                                                    @endphp
                                                    @if($disposedCount > 0)
                                                        <a href="{{ route('dashboard.asset-movements', [
                                                            'week' => 'Week ' . $weekNumber,
                                                            'status' => 'Disposed',
                                                            'month' => $monthNumber,
                                                            'year' => $year
                                                        ]) }}" 
                                                           class="text-decoration-none fw-bold text-danger clickable-number" 
                                                           title="Click to view {{ $disposedCount }} disposed assets">
                                                            {{ $disposedCount }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">{{ $disposedCount }}</span>
                                                    @endif
                                                </td>
                                                <td class="new-arrival-cell">
                                                    @php
                                                        $newArrivalCount = $weekData['New Arrival'] ?? 0;
                                                    @endphp
                                                    @if($newArrivalCount > 0)
                                                        <a href="{{ route('dashboard.asset-movements', [
                                                            'week' => 'Week ' . $weekNumber,
                                                            'status' => 'New Arrival',
                                                            'month' => $monthNumber,
                                                            'year' => $year
                                                        ]) }}" 
                                                           class="text-decoration-none fw-bold text-primary clickable-number" 
                                                           title="Click to view {{ $newArrivalCount }} new arrival assets">
                                                            {{ $newArrivalCount }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">{{ $newArrivalCount }}</span>
                                                    @endif
                                                </td>
                                                <td class="returned-cell">
                                                    @php
                                                        $returnedCount = $weekData['Returned'] ?? 0;
                                                    @endphp
                                                    @if($returnedCount > 0)
                                                        <a href="{{ route('dashboard.asset-movements', [
                                                            'week' => 'Week ' . $weekNumber,
                                                            'status' => 'Returned',
                                                            'month' => $monthNumber,
                                                            'year' => $year
                                                        ]) }}" 
                                                           class="text-decoration-none fw-bold text-warning clickable-number" 
                                                           title="Click to view {{ $returnedCount }} returned assets">
                                                            {{ $returnedCount }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">{{ $returnedCount }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <div class="empty-text">No Weekly Data Available</div>
                            <div class="empty-subtext">Weekly breakdown data will appear here once assets are created</div>
                            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-plus me-2"></i>Add Your First Asset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Row -->
    <div class="row g-4 mb-5">
        <!-- Asset Distribution Chart -->
        <div class="col-xl-6 col-lg-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Asset Movement Distribution
                    </h5>
                    <div class="card-actions">
                        <div class="chart-controls">
                            <button class="btn btn-sm btn-outline-primary active" data-chart="doughnut">Doughnut</button>
                            <button class="btn btn-sm btn-outline-primary" data-chart="bar">Bar</button>
                </div>
                </div>
                </div>
                <div class="card-body">
                    @if($totalAssets > 0)
                        <div class="chart-container">
                            <canvas id="distributionChart" width="400" height="200"></canvas>
                </div>
                    @else
                        <div class="empty-chart-state">
                            <div class="empty-chart-icon">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="empty-chart-text">No Movement Data Available</div>
                            <div class="empty-chart-subtext">Create your first asset to see movement distribution</div>
                            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-plus me-2"></i>Add First Asset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Asset Trends -->
        <div class="col-xl-6 col-lg-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Asset Trends
                    </h5>
                    <div class="card-actions">
                        <select class="form-select form-select-sm" id="trendFilter">
                            <option value="problematic">Problematic Assets</option>
                            <option value="deployed">Deployed Assets</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($totalAssets > 0)
                        <div class="chart-container">
                            <canvas id="trendChart" width="400" height="200"></canvas>
                                </div>
                    @else
                        <div class="empty-chart-state">
                            <div class="empty-chart-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="empty-chart-text">No Trend Data</div>
                            <div class="empty-chart-subtext">Assets will appear here as they're created</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
/* Dashboard Styles - Moved to ensure proper loading */
/* Dashboard Container */
.dashboard-container {
    padding: 0 1rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 2rem;
}

.welcome-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(45deg, #fff, #f0f8ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-subtitle {
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.welcome-time {
    text-align: right;
}

.time-display {
    font-size: 1.2rem;
    font-weight: 600;
}

.time-ago {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Metric Cards */
.metric-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
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
    background: linear-gradient(90deg, var(--metric-color), var(--metric-color-light));
}

.metric-card.primary {
    --metric-color: #667eea;
    --metric-color-light: #764ba2;
}

.metric-card.success {
    --metric-color: #10b981;
    --metric-color-light: #34d399;
}

.metric-card.info {
    --metric-color: #3b82f6;
    --metric-color-light: #60a5fa;
}

.metric-card.warning {
    --metric-color: #f59e0b;
    --metric-color-light: #fbbf24;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--metric-color), var(--metric-color-light));
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: white;
    font-size: 1.5rem;
}

.metric-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 1rem;
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.metric-change {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.metric-change.positive {
    color: #10b981;
}

.metric-change.neutral {
    color: #6b7280;
}

.metric-change.negative {
    color: #ef4444;
}

/* Dashboard Cards */
.dashboard-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.card-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafafa;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
}

.card-title i {
    color: #667eea;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.card-body {
    padding: 2rem;
}

/* Status Overview */
.status-overview {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.status-main {
    text-align: center;
}

.status-percentage {
    font-size: 3rem;
    font-weight: 800;
    color: #10b981;
    line-height: 1;
}

.status-label {
    font-size: 1rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.progress-ring {
    position: relative;
    display: inline-block;
}

.progress-ring-svg {
    transform: rotate(-90deg);
}

.progress-ring-circle-bg {
    fill: none;
    stroke: #e5e7eb;
    stroke-width: 8;
}

.progress-ring-circle {
    fill: none;
    stroke: #10b981;
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease-in-out;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.status-breakdown {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-dot.available {
    background: #3b82f6;
}

.status-dot.active {
    background: #10b981;
}

.status-dot.inactive {
    background: #6b7280;
}

.status-dot.under-maintenance {
    background: #f59e0b;
}

.status-dot.issue-reported {
    background: #ef4444;
}

.status-dot.pending-confirmation {
    background: #8b5cf6;
}

.status-dot.disposed {
    background: #dc2626;
}

.status-name {
    flex: 1;
    color: #6b7280;
    font-weight: 500;
}

.status-count {
    font-weight: 600;
    color: #1f2937;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    background: #f9fafb;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: #f3f4f6;
    transform: translateX(5px);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.activity-meta {
    font-size: 0.875rem;
    color: #6b7280;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-deployed {
    background: #dcfce7;
    color: #166534;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-problematic {
    background: #fee2e2;
    color: #991b1b;
}

.activity-time {
    text-align: right;
}

.time-text {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-bottom: 0.5rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-text {
    font-size: 1.125rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.empty-subtext {
    color: #9ca3af;
}

/* Weekly Breakdown Styling */
.weekly-breakdown-container {
    margin-bottom: 2rem;
}

.breakdown-month-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.weekly-breakdown-table {
    margin-bottom: 0;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.weekly-breakdown-table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem 0.75rem;
    text-align: center;
}

.weekly-breakdown-table tbody td {
    padding: 1rem 0.75rem;
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.weekly-breakdown-table tbody tr:hover {
    background-color: #f8f9fa;
}

.week-column {
    width: 20%;
    text-align: left !important;
}

.week-cell {
    text-align: left !important;
    color: #495057;
    font-weight: 500;
}

.deployed-column,
.disposed-column,
.new-arrival-column,
.returned-column,
.maintenance-column {
    width: 16%;
}

.deployed-cell {
    color: #28a745;
    font-weight: 600;
}

.disposed-cell {
    color: #dc3545;
    font-weight: 600;
}

.new-arrival-cell {
    color: #007bff;
    font-weight: 600;
}

.returned-cell {
    color: #ffc107;
    font-weight: 600;
}

.maintenance-cell {
    color: #6f42c1;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .weekly-breakdown-table {
        font-size: 0.875rem;
    }
    
    .weekly-breakdown-table thead th,
    .weekly-breakdown-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .breakdown-month-title {
        font-size: 1rem;
    }
}

/* Enhanced Quick Actions Panel Styling */
.quick-actions-panel {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.quick-actions-header {
    margin-bottom: 1.25rem;
}

.quick-actions-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
}

.quick-actions-subtitle {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

.quick-actions-grid-enhanced {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.875rem;
}

.quick-action-card {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    text-decoration: none;
    color: inherit;
}

.quick-action-card:hover::before {
    transform: scaleX(1);
}

.action-icon-wrapper {
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.action-icon-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--card-color), var(--card-color-light));
    opacity: 0.1;
}

.action-icon-wrapper i {
    font-size: 1.25rem;
    color: var(--card-color);
    position: relative;
    z-index: 1;
}

.action-content {
    flex: 1;
    min-width: 0;
}

.action-title {
    display: block;
    font-size: 0.875rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
    line-height: 1.2;
}

.action-subtitle {
    display: block;
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.3;
    margin-bottom: 0.5rem;
}

.action-arrow {
    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.3s ease;
}

.quick-action-card:hover .action-arrow {
    opacity: 1;
    transform: translateX(0);
}

.action-arrow i {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Card Color Variants */
.primary-card {
    --card-color: #007bff;
    --card-color-light: #66b3ff;
}

.success-card {
    --card-color: #28a745;
    --card-color-light: #5cb85c;
}

.info-card {
    --card-color: #17a2b8;
    --card-color-light: #5bc0de;
}

.warning-card {
    --card-color: #ffc107;
    --card-color-light: #f0ad4e;
}

/* Tooltip Styling */
.quick-action-card[data-tooltip] {
    position: relative;
}

.quick-action-card[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #2c3e50;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 0.5rem;
}

.quick-action-card[data-tooltip]:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #2c3e50;
    z-index: 1000;
}

/* Responsive Design */
@media (max-width: 768px) {
    .quick-actions-grid-enhanced {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .quick-action-card {
        padding: 0.875rem;
    }
    
    .action-icon-wrapper {
        width: 2.5rem;
        height: 2.5rem;
        margin-right: 0.875rem;
    }
    
    .action-icon-wrapper i {
        font-size: 1.125rem;
    }
}

@media (max-width: 576px) {
    .quick-action-card {
        padding: 0.75rem;
    }
    
    .action-icon-wrapper {
        width: 2.25rem;
        height: 2.25rem;
        margin-right: 0.75rem;
    }
    
    .action-icon-wrapper i {
        font-size: 1rem;
    }
    
    .action-title {
        font-size: 0.8125rem;
    }
    
    .action-subtitle {
        font-size: 0.6875rem;
    }
}

/* Chart Container */
.chart-container {
    position: relative;
    height: 300px;
}

/* Empty Chart State */
.empty-chart-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    text-align: center;
    padding: 2rem;
}

.empty-chart-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-chart-text {
    font-size: 1.25rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.empty-chart-subtext {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.chart-controls {
    display: flex;
    gap: 0.5rem;
}

.chart-controls .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.chart-controls .btn.active {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

/* Quick Actions */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.quick-action-item {
    transition: all 0.3s ease;
}

.quick-action-item:hover {
    transform: translateY(-5px);
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    background: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.quick-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.quick-action-btn.primary {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.quick-action-btn.success {
    border-color: #10b981;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
}

.quick-action-btn.info {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    color: white;
}

.quick-action-btn.warning {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: white;
}

.quick-action-btn.secondary {
    border-color: #6b7280;
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    color: white;
}

.quick-action-btn.dark {
    border-color: #1f2937;
    background: linear-gradient(135deg, #1f2937, #374151);
    color: white;
}

.quick-action-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.quick-action-content {
    flex: 1;
}

.quick-action-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.quick-action-subtitle {
    font-size: 0.875rem;
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-time {
        text-align: center;
    }
    
    .metric-card {
        padding: 1.5rem;
    }
    
    .metric-number {
        font-size: 2rem;
    }
    
    .status-overview {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-action-btn {
        padding: 1rem;
    }
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

.metric-card,
.dashboard-card {
    animation: fadeInUp 0.6s ease-out;
}

.metric-card:nth-child(2) { animation-delay: 0.1s; }
.metric-card:nth-child(3) { animation-delay: 0.2s; }
.metric-card:nth-child(4) { animation-delay: 0.3s; }

/* Dashboard Filter Buttons - Soft UI Design */
.btn-dashboard-filter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 0.75rem;
    color: white;
    font-weight: 500;
    padding: 0.5rem 1rem;
    box-shadow: 
        0 4px 7px -1px rgba(102, 126, 234, 0.11),
        0 2px 4px -1px rgba(102, 126, 234, 0.07);
    transition: all 0.15s ease-in;
    position: relative;
    overflow: hidden;
}

.btn-dashboard-filter::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-dashboard-filter:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 
        0 8px 15px -3px rgba(102, 126, 234, 0.3),
        0 4px 6px -2px rgba(102, 126, 234, 0.2);
}

.btn-dashboard-filter:hover::before {
    left: 100%;
}

.btn-dashboard-filter:focus {
    box-shadow: 
        0 0 0 0.2rem rgba(102, 126, 234, 0.25),
        0 4px 7px -1px rgba(102, 126, 234, 0.11);
    border: none;
}

.btn-dashboard-filter:active {
    transform: translateY(0);
    box-shadow: 
        0 2px 4px -1px rgba(102, 126, 234, 0.3),
        inset 0 1px 2px rgba(0,0,0,0.1);
}

.btn-dashboard-clear {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    color: #6c757d;
    font-weight: 500;
    padding: 0.5rem 1rem;
    box-shadow: 
        0 4px 7px -1px rgba(0, 0, 0, 0.08),
        0 2px 4px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.15s ease-in;
    position: relative;
    overflow: hidden;
}

.btn-dashboard-clear::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(108, 117, 125, 0.1), transparent);
    transition: left 0.5s;
}

.btn-dashboard-clear:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #495057;
    transform: translateY(-1px);
    box-shadow: 
        0 8px 15px -3px rgba(0, 0, 0, 0.15),
        0 4px 6px -2px rgba(0, 0, 0, 0.1);
    border-color: #dee2e6;
}

.btn-dashboard-clear:hover::before {
    left: 100%;
}

.btn-dashboard-clear:focus {
    box-shadow: 
        0 0 0 0.2rem rgba(108, 117, 125, 0.25),
        0 4px 7px -1px rgba(0, 0, 0, 0.08);
    border-color: #adb5bd;
}

.btn-dashboard-clear:active {
    transform: translateY(0);
    box-shadow: 
        0 2px 4px -1px rgba(0, 0, 0, 0.2),
        inset 0 1px 2px rgba(0,0,0,0.1);
}

/* Dashboard Filter Selects - Soft UI Design */
.dashboard-filter-select {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    color: #495057;
    font-weight: 500;
    box-shadow: 
        0 4px 7px -1px rgba(0, 0, 0, 0.08),
        0 2px 4px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.15s ease-in;
}

.dashboard-filter-select:focus {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-color: #667eea;
    box-shadow: 
        0 0 0 0.2rem rgba(102, 126, 234, 0.15),
        0 4px 7px -1px rgba(0, 0, 0, 0.08);
    color: #495057;
}

.dashboard-filter-select:hover {
    border-color: #adb5bd;
    box-shadow: 
        0 6px 12px -2px rgba(0, 0, 0, 0.12),
        0 3px 6px -2px rgba(0, 0, 0, 0.08);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-dashboard-filter,
    .btn-dashboard-clear {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }
    
    .dashboard-filter-select {
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .btn-dashboard-filter span,
    .btn-dashboard-clear span {
        display: none !important;
    }
}

/* Weekly Breakdown Table Improvements */
.week-cell {
    min-width: 120px;
    padding: 0.75rem 1rem !important;
    vertical-align: middle;
}

/* Clickable Numbers Styling */
.clickable-number {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    cursor: pointer;
    font-weight: 600;
}

.clickable-number:hover {
    background-color: rgba(0, 0, 0, 0.1);
    transform: scale(1.1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.clickable-number:active {
    transform: scale(0.95);
}

.week-cell strong {
    display: block;
    font-size: 0.875rem;
    color: #495057;
    margin-bottom: 0.25rem;
}

.week-cell small {
    display: block;
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 400;
    line-height: 1.2;
}

/* Table cell improvements */
.deployed-cell,
.disposed-cell,
.new-arrival-cell,
.returned-cell,
.maintenance-cell {
    text-align: center;
    font-weight: 600;
    padding: 0.75rem 0.5rem !important;
    vertical-align: middle;
}

.deployed-cell {
    color: #28a745;
}

.disposed-cell {
    color: #dc3545;
}

.new-arrival-cell {
    color: #17a2b8;
}

.returned-cell {
    color: #6f42c1;
}

.maintenance-cell {
    color: #fd7e14;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .week-cell {
        min-width: 100px;
        padding: 0.5rem 0.75rem !important;
    }
    
    .week-cell strong {
        font-size: 0.8rem;
    }
    
    .week-cell small {
        font-size: 0.7rem;
    }
    
    .deployed-cell,
    .disposed-cell,
    .new-arrival-cell,
    .returned-cell,
    .maintenance-cell {
        padding: 0.5rem 0.25rem !important;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Asset Distribution Chart
const distributionCanvas = document.getElementById('distributionChart');
if (distributionCanvas) {
    const distributionCtx = distributionCanvas.getContext('2d');
    const distributionData = @json($chartData['currentDistribution'] ?? []);

    const distributionLabels = [];
    const distributionValues = [];
    const distributionColors = [];

    // Process distribution data
    if (distributionData && typeof distributionData === 'object') {
        for (const [movement, data] of Object.entries(distributionData)) {
            if (data && data.count > 0) {
                distributionLabels.push(movement.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
                distributionValues.push(data.count);
                
                switch(movement) {
                    case 'deployed': distributionColors.push('#10b981'); break;
                    case 'disposed': distributionColors.push('#ef4444'); break;
                    case 'new_arrival': distributionColors.push('#3b82f6'); break;
                    case 'returned': distributionColors.push('#f59e0b'); break;
                    default: distributionColors.push('#d1d5db'); break;
                }
            }
        }
    }

    // Create chart even if no data (show empty state)
    if (distributionValues.length === 0) {
        distributionLabels.push('No Data');
        distributionValues.push(1);
        distributionColors.push('#e5e7eb');
    }

    const distributionChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: distributionLabels,
            datasets: [{
                data: distributionValues,
                backgroundColor: distributionColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Chart Controls
    document.querySelectorAll('[data-chart="doughnut"]').forEach(btn => {
        btn.addEventListener('click', function() {
            distributionChart.config.type = 'doughnut';
            distributionChart.update();
            document.querySelectorAll('.chart-controls .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    document.querySelectorAll('[data-chart="bar"]').forEach(btn => {
        btn.addEventListener('click', function() {
            distributionChart.config.type = 'bar';
            distributionChart.update();
            document.querySelectorAll('.chart-controls .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// Asset Trends Chart
const trendCanvas = document.getElementById('trendChart');
if (trendCanvas) {
    const trendCtx = trendCanvas.getContext('2d');
    const trendData = @json($chartData['problematicTrend'] ?? []);

    const trendLabels = [];
    const trendValues = [];

    // Process trend data safely
    if (trendData && Array.isArray(trendData)) {
        trendLabels.push(...trendData.map(item => item.month));
        trendValues.push(...trendData.map(item => item.count));
    }

    // Create chart even if no data (show empty state)
    if (trendValues.length === 0) {
        trendLabels.push('No Data');
        trendValues.push(0);
    }

    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Problematic Assets',
                data: trendValues,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ef4444',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

// Chart Controls (moved inside chart creation)

// Refresh Asset Status
function refreshAssetStatus() {
    // Add loading state
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    // Simulate refresh (replace with actual AJAX call)
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        // Reload page or update data
        window.location.reload();
    }, 1000);
}

// Auto-refresh every 5 minutes
setInterval(() => {
    // You can implement auto-refresh here
}, 300000);

// Add smooth scrolling for better UX
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
        });
    });
    
    // Weekly Breakdown Filter Functions
    function filterWeeklyBreakdown(period) {
        // Get current URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        
        if (period === 'current') {
            // Show current month
            const now = new Date();
            urlParams.set('month', now.getMonth() + 1);
            urlParams.set('year', now.getFullYear());
        } else if (period === 'previous') {
            // Show previous month
            const now = new Date();
            const prevMonth = new Date(now.getFullYear(), now.getMonth() - 1);
            urlParams.set('month', prevMonth.getMonth() + 1);
            urlParams.set('year', prevMonth.getFullYear());
        }
        
        // Reload page with new parameters
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    }
</script>
@endpush