@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
.clickable-number {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.clickable-number:hover {
    background-color: #e3f2fd;
    transform: scale(1.1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.clickable-number:active {
    transform: scale(0.95);
}
</style>
@endsection
@section('page-title', 'Dashboard')

@section('page-actions')
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2 align-items-center">
            <select name="month" class="form-select form-select-sm" style="width: 140px; height: 38px;">
                <option value="">All Months</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select form-select-sm" style="width: 100px;">
                <option value="">All Years</option>
                @for($year = date('Y'); $year >= 2020; $year--)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
            <select name="entity" class="form-select form-select-sm" style="width: 120px;">
                <option value="">All Entities</option>
                @foreach($entities as $entity)
                    <option value="{{ $entity }}" {{ request('entity') == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center" style="height: 38px; padding: 0 16px;">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
            @if(request()->hasAny(['month', 'year', 'entity']))
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm shadow-sm d-flex align-items-center" style="height: 38px; padding: 0 16px;">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            @endif
        </form>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient metric-icon-container">
                                <i class="fas fa-boxes text-white fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-bold">Total Assets</div>
                            <div class="fs-2 fw-bold text-dark">{{ number_format($totalAssets) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient metric-icon-container">
                                <i class="fas fa-users text-white fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-bold">Total Users</div>
                            <div class="fs-2 fw-bold text-dark">{{ number_format($totalUsers) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient metric-icon-container">
                                <i class="fas fa-building text-white fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-bold">Departments</div>
                            <div class="fs-2 fw-bold text-dark">{{ number_format($totalDepartments) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient metric-icon-container">
                                <i class="fas fa-truck text-white fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small text-uppercase fw-bold">Vendors</div>
                            <div class="fs-2 fw-bold text-dark">{{ number_format($totalVendors) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deployment Status & Recent Assets Row -->
    <div class="row mb-4">
        <!-- Deployment Status -->
        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Asset Deployment Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 fw-bold text-success">{{ $deployedAssetsPercentage }}%</div>
                        <div class="text-muted">Assets Deployed</div>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $deployedAssetsPercentage }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span>{{ $totalAssets - \App\Models\Asset::where('status', 'deployed')->count() }} Pending</span>
                        <span>{{ \App\Models\Asset::where('status', 'deployed')->count() }} Deployed</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Assets -->
        <div class="col-xl-8 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clock me-2 text-primary"></i>Recent Assets
                        </h6>
                        <a href="{{ route('assets.index') }}" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center" style="height: 32px; padding: 0 12px;">
                            <i class="fas fa-eye me-2"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentAssets->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentAssets as $asset)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="recent-asset-icon">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $asset->name }}</div>
                                                <div class="small text-muted">
                                                    {{ $asset->category->name ?? 'No Category' }} • 
                                                    <span class="badge badge-sm bg-{{ $asset->status === 'deployed' ? 'success' : ($asset->status === 'problematic' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted">{{ $asset->created_at->diffForHumans() }}</div>
                                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center justify-content-center" title="View Asset Details" style="width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent assets found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="row mb-4">
        <!-- Current Asset Distribution -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Current Asset Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="distributionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Problematic Assets Trend -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Problematic Assets Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Analysis Row -->
    <div class="row mb-4">
        <!-- Monthly Status Rollup -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Monthly Status Analysis
                    </h6>
                </div>
                <div class="card-body">
                    @if(!empty($monthlyRollup['months']))
                        @foreach($monthlyRollup['months'] as $month => $data)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">{{ $month }}</h6>
                                @foreach($monthlyRollup['statuses'] as $status)
                                    @php
                                        $statusData = $data[$status] ?? ['count' => 0, 'percentage' => 0];
                                        $statusColor = match($status) {
                                            'deployed' => 'success',
                                            'problematic' => 'danger',
                                            'pending_confirm' => 'warning',
                                            'returned' => 'info',
                                            'disposed' => 'secondary',
                                            'new_arrived' => 'primary',
                                            default => 'light'
                                        };
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                        <span class="small fw-semibold">{{ $statusData['count'] }} ({{ $statusData['percentage'] }}%)</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $statusColor }}" style="width: {{ $statusData['percentage'] }}%"></div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No monthly data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Weekly Breakdown -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-calendar-week me-2 text-primary"></i>Weekly Movement Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    @if(!empty($weeklyBreakdown['months']))
                        @foreach($weeklyBreakdown['months'] as $month => $weeks)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">{{ $month }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th class="border-0 small text-muted">Week</th>
                                                @foreach($weeklyBreakdown['statuses'] as $status)
                                                    <th class="border-0 small text-muted text-center">{{ $status }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($weeks as $week => $data)
                                                <tr>
                                                    <td class="small fw-semibold">{{ $week }}</td>
                                                    @foreach($weeklyBreakdown['statuses'] as $status)
                                                        <td class="text-center small">
                                                            @php
                                                                $count = $data[$status] ?? 0;
                                                                $monthNumber = date('n', strtotime($month));
                                                                $year = date('Y', strtotime($month));
                                                            @endphp
                                                            @if($count > 0)
                                                                <a href="{{ route('dashboard.asset-movements', [
                                                                    'week' => $week,
                                                                    'status' => $status,
                                                                    'month' => $monthNumber,
                                                                    'year' => $year
                                                                ]) }}" 
                                                                   class="text-decoration-none fw-bold text-primary clickable-number" 
                                                                   title="Click to view {{ $count }} {{ strtolower($status) }} assets">
                                                                    {{ $count }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">{{ $count }}</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-week fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No weekly data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @can('create_assets')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('assets.create') }}" class="btn btn-primary w-100 d-flex flex-column align-items-center justify-content-center shadow-sm border-0 text-decoration-none quick-action-btn" style="height: 120px; transition: all 0.3s ease; border-radius: 12px;">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <i class="fas fa-plus fa-lg text-white"></i>
                                    </div>
                                    <span class="fw-semibold text-white small">Add Asset</span>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_assets')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('assets.index') }}" class="btn btn-success w-100 d-flex flex-column align-items-center justify-content-center shadow-sm border-0 text-decoration-none quick-action-btn" style="height: 120px; transition: all 0.3s ease; border-radius: 12px;">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <i class="fas fa-list fa-lg text-white"></i>
                                    </div>
                                    <span class="fw-semibold text-white small">View Assets</span>
                                </a>
                            </div>
                        @endcan
                        
                        @can('create_users')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('users.create') }}" class="btn btn-info w-100 d-flex flex-column align-items-center justify-content-center shadow-sm border-0 text-decoration-none quick-action-btn" style="height: 120px; transition: all 0.3s ease; border-radius: 12px;">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <i class="fas fa-user-plus fa-lg text-white"></i>
                                    </div>
                                    <span class="fw-semibold text-white small">Add User</span>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_reports')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('assets.print-employee-assets') }}" class="btn btn-warning w-100 d-flex flex-column align-items-center justify-content-center shadow-sm border-0 text-decoration-none quick-action-btn" style="height: 120px; transition: all 0.3s ease; border-radius: 12px;" target="_blank">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <i class="fas fa-print fa-lg text-white"></i>
                                    </div>
                                    <span class="fw-semibold text-white small">Reports</span>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_maintenance')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary w-100 d-flex flex-column align-items-center justify-content-center shadow-sm border-0 text-decoration-none quick-action-btn" style="height: 120px; transition: all 0.3s ease; border-radius: 12px;">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <i class="fas fa-tools fa-lg text-white"></i>
                                    </div>
                                    <span class="fw-semibold text-white small">Maintenance</span>
                                </a>
                            </div>
                        @endcan
                        
                        @can('view_timeline')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('timeline.index') }}" class="btn btn-dark w-100 d-flex flex-column align-items-center justify-content-center shadow-sm border-0 text-decoration-none quick-action-btn" style="height: 120px; transition: all 0.3s ease; border-radius: 12px;">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                        <i class="fas fa-history fa-lg text-white"></i>
                                    </div>
                                    <span class="fw-semibold text-white small">Timeline</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.quick-action-btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.quick-action-btn:active {
    transform: translateY(-1px) !important;
}

/* Ensure consistent icon container sizing in metric cards */
.metric-icon-container {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
}

/* Improve button alignment in filter section */
.filter-controls .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

/* Recent assets list improvements */
.recent-asset-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f8f9fa;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Asset Distribution Pie Chart
const distributionCtx = document.getElementById('distributionChart').getContext('2d');
const distributionData = @json($chartData['currentDistribution'] ?? []);

const distributionLabels = [];
const distributionValues = [];
const distributionColors = [];

// Process distribution data
if (distributionData && typeof distributionData === 'object') {
    for (const [status, data] of Object.entries(distributionData)) {
        if (data && data.count > 0) {
            distributionLabels.push(status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
            distributionValues.push(data.count);
            
            switch(status) {
                case 'deployed': distributionColors.push('#198754'); break;
                case 'problematic': distributionColors.push('#dc3545'); break;
                case 'pending_confirm': distributionColors.push('#ffc107'); break;
                case 'returned': distributionColors.push('#0dcaf0'); break;
                case 'disposed': distributionColors.push('#6c757d'); break;
                case 'new_arrived': distributionColors.push('#0d6efd'); break;
                default: distributionColors.push('#adb5bd'); break;
            }
        }
    }
}

// If no data, show a message
if (distributionLabels.length === 0) {
    distributionLabels.push('No Data');
    distributionValues.push(1);
    distributionColors.push('#e9ecef');
}

try {
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: distributionLabels,
            datasets: [{
                data: distributionValues,
                backgroundColor: distributionColors,
                borderWidth: 2,
                borderColor: '#fff'
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
                    usePointStyle: true
                }
            }
        }
    }
});
} catch (error) {
    console.error('Error creating distribution chart:', error);
    document.getElementById('distributionChart').innerHTML = '<div class="text-center text-muted">Chart failed to load</div>';
}

// Problematic Assets Trend Line Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendData = @json($chartData['problematicTrend'] ?? []);

const trendLabels = [];
const trendValues = [];

// Process trend data safely
if (trendData && Array.isArray(trendData)) {
    trendLabels.push(...trendData.map(item => item.month));
    trendValues.push(...trendData.map(item => item.count));
}

// If no data, show a message
if (trendLabels.length === 0) {
    trendLabels.push('No Data');
    trendValues.push(0);
}

try {
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Problematic Assets',
                data: trendValues,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#dc3545',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6
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
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
} catch (error) {
    console.error('Error creating trend chart:', error);
    document.getElementById('trendChart').innerHTML = '<div class="text-center text-muted">Chart failed to load</div>';
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
</script>
@endpush