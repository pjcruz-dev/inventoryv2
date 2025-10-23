@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
/* Dashboard Styles - Updated {{ date('Y-m-d H:i:s') }} */

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

/* Declined Assets Widget */
.declined-assets-widget {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e9ecef;
    position: relative;
    overflow: hidden;
}

.declined-assets-widget::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #dc3545 0%, #fd7e14 50%, #ffc107 100%);
}

.widget-icon.declined {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    font-size: 1.75rem;
    margin-right: 1rem;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.25);
    animation: pulse-red 2s ease-in-out infinite;
}

@keyframes pulse-red {
    0%, 100% { box-shadow: 0 6px 20px rgba(220, 53, 69, 0.25); }
    50% { box-shadow: 0 6px 30px rgba(220, 53, 69, 0.4); }
}

.decline-stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 2px solid transparent;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.decline-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: width 0.3s ease;
}

.decline-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.decline-stat-card:hover::before {
    width: 8px;
}

.decline-stat-card.high-severity::before {
    background: linear-gradient(180deg, #dc3545 0%, #c82333 100%);
}

.decline-stat-card.high-severity:hover {
    border-color: rgba(220, 53, 69, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
}

.decline-stat-card.medium-severity::before {
    background: linear-gradient(180deg, #ffc107 0%, #ff9800 100%);
}

.decline-stat-card.medium-severity:hover {
    border-color: rgba(255, 193, 7, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #fffbf0 100%);
}

.decline-stat-card.low-severity::before {
    background: linear-gradient(180deg, #17a2b8 0%, #138496 100%);
}

.decline-stat-card.low-severity:hover {
    border-color: rgba(23, 162, 184, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #f0fbfd 100%);
}

.decline-stat-card.follow-up-required::before {
    background: linear-gradient(180deg, #6f42c1 0%, #5a32a3 100%);
}

.decline-stat-card.follow-up-required:hover {
    border-color: rgba(111, 66, 193, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #f8f5fc 100%);
}

.decline-stat-card .stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    position: relative;
    transition: all 0.3s ease;
}

.decline-stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.decline-stat-card.high-severity .stat-icon {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.05) 100%);
    color: #dc3545;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

.decline-stat-card.medium-severity .stat-icon {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.05) 100%);
    color: #ff9800;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
}

.decline-stat-card.low-severity .stat-icon {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.15) 0%, rgba(23, 162, 184, 0.05) 100%);
    color: #17a2b8;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
}

.decline-stat-card.follow-up-required .stat-icon {
    background: linear-gradient(135deg, rgba(111, 66, 193, 0.15) 0%, rgba(111, 66, 193, 0.05) 100%);
    color: #6f42c1;
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.2);
}

.decline-stat-card .stat-details {
    flex: 1;
}

.decline-stat-card .stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.4rem;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.decline-stat-card .stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.4;
}

.decline-stat-card .stat-alert {
    font-size: 0.7rem;
    color: #dc3545;
    font-weight: 700;
    margin-top: 0.5rem;
    padding: 0.25rem 0.6rem;
    background: rgba(220, 53, 69, 0.1);
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    animation: pulse-alert 2s ease-in-out infinite;
}

@keyframes pulse-alert {
    0%, 100% { background: rgba(220, 53, 69, 0.1); }
    50% { background: rgba(220, 53, 69, 0.2); }
}

.decline-trend-section, .recent-declines-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.decline-trend-section:hover, .recent-declines-section:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-color: #e0e0e0;
}

.decline-trend-section h6, .recent-declines-section h6 {
    font-weight: 700;
    color: #2c3e50;
    font-size: 1rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.decline-trend-section h6::before {
    content: '📈';
    font-size: 1.2rem;
}

.recent-declines-section h6::before {
    content: '📋';
    font-size: 1.2rem;
}

.decline-trend-section {
    height: 370px;
}

.decline-trend-section canvas {
    max-height: 250px !important;
}

.recent-declines-section {
    height: 370px;
}

.recent-declines-list {
    max-height: 260px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.recent-declines-list::-webkit-scrollbar {
    width: 6px;
}

.recent-declines-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.recent-declines-list::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.recent-declines-list::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #5568d3 0%, #63398d 100%);
}

.decline-item {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.decline-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.decline-item:hover {
    transform: translateX(5px);
    background: white;
    border-color: #667eea;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
}

.decline-item:hover::before {
    opacity: 1;
}

.decline-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.decline-asset-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.asset-tag-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.decline-item:hover .asset-tag-badge {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.asset-name {
    font-weight: 700;
    color: #2c3e50;
    font-size: 0.85rem;
    line-height: 1.3;
}

.severity-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 16px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.decline-item:hover .severity-badge {
    transform: scale(1.05);
}

.severity-badge.severity-high {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    animation: pulse-high 2s ease-in-out infinite;
}

.severity-badge.severity-high::before {
    content: '🔴';
    font-size: 0.8rem;
}

@keyframes pulse-high {
    0%, 100% { box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3); }
    50% { box-shadow: 0 4px 12px rgba(220, 53, 69, 0.5); }
}

.severity-badge.severity-medium {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: #2c3e50;
}

.severity-badge.severity-medium::before {
    content: '🟡';
    font-size: 0.8rem;
}

.severity-badge.severity-low {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.severity-badge.severity-low::before {
    content: '🔵';
    font-size: 0.8rem;
}

.decline-item-details {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.decline-detail {
    font-size: 0.75rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-weight: 500;
}

.decline-detail i {
    color: #667eea;
    font-size: 0.85rem;
}

.decline-reason-preview {
    font-size: 0.8rem;
    color: #495057;
    padding: 0.75rem;
    background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
    border-radius: 10px;
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    border: 1px solid #ffe0e0;
    margin-top: 0.75rem;
    line-height: 1.5;
    font-weight: 500;
}

.decline-reason-preview i {
    margin-top: 0.15rem;
    color: #dc3545;
    font-size: 1rem;
    flex-shrink: 0;
}

.category-breakdown {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.category-item {
    display: grid;
    grid-template-columns: 200px 1fr 70px;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: white;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.category-item:hover {
    transform: translateX(3px);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border-color: #e0e0e0;
}

.category-label {
    font-weight: 700;
    color: #2c3e50;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-label::before {
    content: '📊';
    font-size: 1rem;
}

.category-bar-wrapper {
    background: linear-gradient(135deg, #f0f0f0 0%, #e9ecef 100%);
    height: 28px;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    position: relative;
}

.category-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    height: 100%;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.category-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    to { left: 100%; }
}

.category-count {
    font-weight: 800;
    color: #2c3e50;
    text-align: center;
    font-size: 1.1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
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

/* System Status Card */
.system-status-card {
    border-left: 5px solid #007bff;
}

.system-status-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #343a40;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.system-status-body {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 1rem;
}

.status-alert {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    background-color: #f0f9f0;
    border: 1px solid #d1f2d1;
}

.status-alert.operational {
    background-color: #f0f9f0;
    border: 1px solid #d1f2d1;
    color: #2d5a2d;
}

.status-indicator {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #28a745;
    color: white;
    font-size: 1.1rem;
    margin-right: 1.25rem;
    flex-shrink: 0;
}

.status-text h6 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
    color: #2d5a2d;
}

.status-text small {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 400;
}

.system-components {
    margin-bottom: 1.5rem;
}

.component-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.component-item:last-child {
    border-bottom: none;
}

.component-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    margin-right: 1rem;
}

.component-info {
    flex: 1;
}

.component-name {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.2rem;
    color: #495057;
}

.component-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

.component-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-indicator.healthy {
    background-color: #28a745;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-indicator.warning {
    background-color: #ffc107;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-text {
    font-size: 0.85rem;
    font-weight: 500;
    color: #495057;
}

.system-status-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
}

.performance-metrics {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.metric-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.metric-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    margin-right: 0.75rem;
}

.metric-info {
    display: flex;
    flex-direction: column;
}

.metric-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.2rem;
}

.metric-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #495057;
}

.last-updated {
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    color: #6c757d;
}

.system-refresh-btn {
    border-color: #6c757d;
    color: #6c757d;
}

.system-refresh-btn:hover {
    background-color: #6c757d;
    color: white;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.system-metrics {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.metric-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.metric-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.02), rgba(32, 201, 151, 0.02));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.metric-item:hover {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: rgba(40, 167, 69, 0.2);
}

.metric-item:hover::before {
    opacity: 1;
}

.metric-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    position: relative;
    z-index: 2;
}

.metric-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    position: relative;
    z-index: 2;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.metric-status.healthy {
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

.metric-status.warning {
    color: #ffc107;
    background: rgba(255, 193, 7, 0.1);
}

.metric-status.danger {
    color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}

.metric-item:hover .metric-status.healthy {
    background: rgba(40, 167, 69, 0.15);
    transform: scale(1.05);
}

.metric-item:hover .metric-status.warning {
    background: rgba(255, 193, 7, 0.15);
    transform: scale(1.05);
}

.metric-item:hover .metric-status.danger {
    background: rgba(220, 53, 69, 0.15);
    transform: scale(1.05);
}

.monitoring-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Enhanced Refresh Button */
.system-refresh-btn {
    border: 2px solid #6c757d;
    color: #6c757d;
    background: transparent;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.system-refresh-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(108, 117, 125, 0.1), transparent);
    transition: left 0.5s;
}

.system-refresh-btn:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    border-color: #6c757d;
}

.system-refresh-btn:hover::before {
    left: 100%;
}

.system-refresh-btn:active {
    transform: translateY(0);
}

.system-refresh-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.system-refresh-btn:disabled:hover {
    background: transparent;
    color: #6c757d;
    transform: none;
    box-shadow: none;
}

/* Status Text Enhancements */
.status-text h6 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
    position: relative;
    z-index: 2;
}

.status-text small {
    font-size: 0.9rem;
    opacity: 0.8;
    position: relative;
    z-index: 2;
}

/* Loading Animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.system-refresh-btn .fa-spinner {
    animation: spin 1s linear infinite;
}

/* Pulse Animation for Status Indicator */
@keyframes statusPulse {
    0% { 
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
        transform: scale(1);
    }
    50% { 
        box-shadow: 0 0 0 8px rgba(40, 167, 69, 0);
        transform: scale(1.05);
    }
    100% { 
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        transform: scale(1);
    }
}

.status-indicator.pulsing {
    animation: statusPulse 2s infinite;
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
    
    .system-metrics {
        grid-template-columns: 1fr;
    }
    
    .monitoring-toggle {
        flex-direction: column;
        gap: 0.25rem;
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

    <!-- Monthly Analysis Row -->
    <div class="row mb-4">
        <!-- Monthly Status Overview -->
        <div class="col-xl-6 col-lg-12 mb-3">
            <div class="dashboard-card h-100 monthly-overview-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="monthly-overview-icon">
                            <i class="fas fa-chart-bar"></i>
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
                            @endphp
                            <div class="monthly-period-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                <div class="monthly-period-header">
                                    <div class="monthly-period-title">
                                        <h6 class="month-name">{{ $monthName }}</h6>
                                        <div class="monthly-summary">
                                            <span class="total-activities">{{ $totalForMonth }} activities</span>
                                            @if($totalForMonth > 0)
                                                <span class="activity-trend">
                                                    @php
                                                        $deployedCount = $data['deployed']['count'] ?? 0;
                                                        $problematicCount = $data['problematic']['count'] ?? 0;
                                                        $healthScore = $totalForMonth > 0 ? round((($deployedCount - $problematicCount) / $totalForMonth) * 100) : 0;
                                                    @endphp
                                                    <i class="fas fa-heartbeat me-1"></i>
                                                    <span class="health-score {{ $healthScore >= 70 ? 'good' : ($healthScore >= 40 ? 'moderate' : 'poor') }}">
                                                        {{ $healthScore }}% health
                                                    </span>
                                                </span>
                                            @endif
                </div>
                </div>
                                    @if($totalForMonth > 0)
                                        <div class="monthly-visual-indicator">
                                            <div class="activity-bar">
                                                <div class="activity-fill" style="width: {{ min(100, ($totalForMonth / 50) * 100) }}%"></div>
            </div>
        </div>
                                    @endif
    </div>

                                @if($totalForMonth > 0)
                                    <div class="monthly-status-grid">
                                @foreach($monthlyRollup['statuses'] as $status)
                                    @php
                                        $statusData = $data[$status] ?? ['count' => 0, 'percentage' => 0];
                                                $statusConfig = match($status) {
                                                    'Deployed' => ['color' => 'success', 'icon' => 'fas fa-check-circle', 'label' => 'Deployed'],
                                                    'Maintenance' => ['color' => 'warning', 'icon' => 'fas fa-tools', 'label' => 'Maintenance'],
                                                    'Pending Confirmation' => ['color' => 'info', 'icon' => 'fas fa-clock', 'label' => 'Pending'],
                                                    'Active' => ['color' => 'success', 'icon' => 'fas fa-check-circle', 'label' => 'Active'],
                                                    'For Disposal' => ['color' => 'danger', 'icon' => 'fas fa-trash', 'label' => 'For Disposal'],
                                                    'Available' => ['color' => 'primary', 'icon' => 'fas fa-check', 'label' => 'Available'],
                                                    'Return' => ['color' => 'info', 'icon' => 'fas fa-undo', 'label' => 'Return'],
                                                    'New Arrival' => ['color' => 'primary', 'icon' => 'fas fa-plus-circle', 'label' => 'New Arrival'],
                                                    default => ['color' => 'light', 'icon' => 'fas fa-question', 'label' => ucfirst(str_replace('_', ' ', $status))]
                                        };
                                    @endphp
                                            @if($statusData['count'] > 0)
                                                <div class="status-item {{ $statusConfig['color'] }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                                    <div class="status-icon">
                                                        <i class="{{ $statusConfig['icon'] }}"></i>
                                    </div>
                                                    <div class="status-content">
                                                        <div class="status-count">{{ $statusData['count'] }}</div>
                                                        <div class="status-label">{{ $statusConfig['label'] }}</div>
                                                        <div class="status-percentage">{{ $statusData['percentage'] }}%</div>
                                    </div>
                                                    <div class="status-progress">
                                                        <div class="progress-bar" style="width: {{ $statusData['percentage'] }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                @endforeach
                                    </div>
                                @else
                                    <div class="monthly-empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <h6 class="empty-title">No Activity</h6>
                                        <p class="empty-description">No asset activities recorded for this month</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="monthly-empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h6 class="empty-title">No Monthly Data</h6>
                            <p class="empty-description">No monthly activity data available</p>
                            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Add First Asset
                            </a>
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
                        <button class="btn btn-primary btn-sm weekly-chart-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#weeklyChart" aria-expanded="false" aria-controls="weeklyChart">
                            <i class="fas fa-chart-bar me-2"></i>Chart View
                        </button>
                    </div>
                </div>
                
                <!-- Enhanced Chart View -->
                <div class="collapse mb-4" id="weeklyChart">
                    <div class="weekly-chart-container" style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="chart-header" style="margin-bottom: 20px;">
                            <h6 class="chart-title" style="font-weight: 600; color: #333; margin-bottom: 15px;">Weekly Movement Trends</h6>
                            <div class="chart-legend" style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <span class="legend-item" style="display: flex; align-items: center; gap: 5px; font-size: 14px;">
                                    <i class="fas fa-circle" style="color: #28a745;"></i> Active
                                </span>
                                <span class="legend-item" style="display: flex; align-items: center; gap: 5px; font-size: 14px;">
                                    <i class="fas fa-circle" style="color: #dc3545;"></i> Maintenance
                                </span>
                                <span class="legend-item" style="display: flex; align-items: center; gap: 5px; font-size: 14px;">
                                    <i class="fas fa-circle" style="color: #ffc107;"></i> Pending Confirmation
                                </span>
                            </div>
                        </div>
                        <div class="chart-wrapper" style="position: relative; height: 400px; width: 100%;">
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
                                                                    'Return' => ['color' => 'info', 'icon' => 'fas fa-undo'],
                                                                    'New Arrival' => ['color' => 'primary', 'icon' => 'fas fa-plus-circle'],
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
                        <a href="{{ route('assets.index') }}" class="enhanced-quick-action-btn" title="View all assets">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon success">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                    <h6 class="action-title">View Assets</h6>
                                <p class="action-description">Browse inventory</p>
                                </div>
                                <div class="action-badge count">{{ $totalAssets }}</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                    @can('create_maintenance')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="300">
                        <a href="{{ route('maintenance.create') }}" class="enhanced-quick-action-btn" title="Create maintenance record">
                                <div class="action-icon-wrapper">
                                <div class="action-icon warning">
                                    <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                <h6 class="action-title">Maintenance</h6>
                                <p class="action-description">Schedule repair</p>
                                </div>
                            <div class="action-badge maintenance">Repair</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                    @can('create_disposal')
                        <div class="quick-action-item" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('disposal.create') }}" class="enhanced-quick-action-btn" title="Create disposal record">
                                <div class="action-icon-wrapper">
                                    <div class="action-icon danger">
                                    <i class="fas fa-trash"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                <h6 class="action-title">Disposal</h6>
                                <p class="action-description">Dispose asset</p>
                                </div>
                            <div class="action-badge disposal">Dispose</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
                        
                    @can('view_reports')
                    <div class="quick-action-item" data-aos="fade-up" data-aos-delay="500">
                        <a href="{{ route('reports.index') }}" class="enhanced-quick-action-btn" title="View system reports">
                                <div class="action-icon-wrapper">
                                <div class="action-icon info">
                                    <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="action-glow"></div>
                                </div>
                                <div class="action-content">
                                <h6 class="action-title">Reports</h6>
                                <p class="action-description">View analytics</p>
                                </div>
                            <div class="action-badge reports">Analytics</div>
                                <div class="action-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                </a>
                            </div>
                        @endcan
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
                
                <div class="system-status-body">
                    <div class="status-alert operational">
                        <div class="status-indicator">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        <div class="status-text">
                            <h6 class="mb-0 fw-semibold">System Healthy</h6>
                            <small class="text-muted">All systems operational</small>
                        </div>
                    </div>
                    
                    <div class="system-components">
                        <div class="component-item">
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
                        
                        <div class="component-item">
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
                        
                        <div class="component-item">
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
                        
                        <div class="component-item">
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

    <!-- Declined Assets Widget -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card declined-assets-widget">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="widget-icon declined">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Declined Asset Assignments</h5>
                            <small class="text-muted">Monitor and manage declined confirmations</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('asset-assignment-confirmations.index') }}?status=declined" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i> View All
                        </a>
                        <a href="{{ route('asset-assignments.export-declines') }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-export me-1"></i> Export Report
                        </a>
                    </div>
                </div>

                <!-- Statistics Row -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="decline-stat-card high-severity">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value">{{ $declinedAssets['high_severity'] }}</div>
                                <div class="stat-label">High Severity</div>
                                @if($declinedAssets['pending_high_severity']->count() > 0)
                                    <div class="stat-alert">
                                        <i class="fas fa-clock"></i> {{ $declinedAssets['pending_high_severity']->count() }} pending
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="decline-stat-card medium-severity">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value">{{ $declinedAssets['medium_severity'] }}</div>
                                <div class="stat-label">Medium Severity</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="decline-stat-card low-severity">
                            <div class="stat-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value">{{ $declinedAssets['low_severity'] }}</div>
                                <div class="stat-label">Low Severity</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="decline-stat-card follow-up-required">
                            <div class="stat-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value">{{ $declinedAssets['requires_follow_up'] }}</div>
                                <div class="stat-label">Requires Follow-up</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Decline Trend Chart & Recent Declines -->
                <div class="row">
                    <div class="col-lg-7 mb-3">
                        <div class="decline-trend-section">
                            <h6 class="mb-3 fw-semibold">7-Day Decline Trend</h6>
                            <div style="position: relative; height: 250px;">
                                <canvas id="declineTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 mb-3">
                        <div class="recent-declines-section">
                            <h6 class="mb-3 fw-semibold">Recent Declined Assets</h6>
                            <div class="recent-declines-list">
                                @forelse($declinedAssets['recent'] as $decline)
                                    <div class="decline-item">
                                        <div class="decline-item-header">
                                            <div class="decline-asset-info">
                                                <span class="asset-tag-badge">{{ $decline->asset->asset_tag }}</span>
                                                <span class="asset-name">{{ $decline->asset->asset_name }}</span>
                                            </div>
                                            <span class="severity-badge severity-{{ $decline->decline_severity }}">
                                                {{ strtoupper($decline->decline_severity ?? 'N/A') }}
                                            </span>
                                        </div>
                                        <div class="decline-item-details">
                                            <div class="decline-detail">
                                                <i class="fas fa-user"></i>
                                                {{ $decline->user->first_name }} {{ $decline->user->last_name }}
                                            </div>
                                            <div class="decline-detail">
                                                <i class="fas fa-calendar"></i>
                                                {{ $decline->declined_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="decline-reason-preview">
                                            <i class="fas fa-comment-alt"></i>
                                            {{ $decline->getFormattedDeclineReason() }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <p class="mb-0">No recent declined assets</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                @if($declinedAssets['by_category']->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="mb-3 fw-semibold">Decline Reasons Breakdown</h6>
                            <div class="category-breakdown">
                                @foreach($declinedAssets['by_category'] as $category => $count)
                                    <div class="category-item">
                                        <div class="category-label">{{ ucwords(str_replace('_', ' ', $category)) }}</div>
                                        <div class="category-bar-wrapper">
                                            <div class="category-bar" style="width: {{ ($count / $declinedAssets['total']) * 100 }}%"></div>
                                        </div>
                                        <div class="category-count">{{ $count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple System Status Refresh
    const refreshButton = document.querySelector('.system-refresh-btn');
    const lastUpdated = document.querySelector('.last-updated small');
    
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;
            
            // Simulate refresh
            setTimeout(() => {
                lastUpdated.textContent = `Last updated: ${new Date().toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                })} at ${new Date().toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit', 
                    hour12: true 
                })}`;
                
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 1000);
        });
    }

    // Initialize Weekly Movement Chart
    const weeklyChartCanvas = document.getElementById('weeklyMovementChart');
    if (weeklyChartCanvas) {
        // Prepare chart data from PHP
        const chartData = @json($chartData);
        const weeklyBreakdown = @json($weeklyBreakdown);
        
        // Process weekly data for chart
        let labels = [];
        let deployedData = [];
        let maintenanceData = [];
        let pendingData = [];
        
        // Use real data from controller if available
        if (chartData && chartData.weeklyData) {
            labels = chartData.weeklyData.weeks;
            deployedData = chartData.weeklyData.deployed;
            maintenanceData = chartData.weeklyData.maintenance;
            pendingData = chartData.weeklyData.pending;
        } else {
            // Fallback to sample data
            for (let i = 7; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - (i * 7));
                const weekStart = new Date(date);
                weekStart.setDate(date.getDate() - date.getDay());
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                
                labels.push(`${weekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`);
                
                // Generate sample data
                deployedData.push(Math.floor(Math.random() * 10) + 5);
                maintenanceData.push(Math.floor(Math.random() * 5) + 1);
                pendingData.push(Math.floor(Math.random() * 3) + 1);
            }
        }
        
        // Create the chart
        const weeklyMovementChart = new Chart(weeklyChartCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Active',
                        data: deployedData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Maintenance',
                        data: maintenanceData,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pending Confirmation',
                        data: pendingData,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Weekly Asset Movement Trends',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Week'
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Number of Assets'
                        },
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Make chart responsive
        window.addEventListener('resize', function() {
            weeklyMovementChart.resize();
        });
    }

    // Initialize Decline Trend Chart
    const declineTrendCanvas = document.getElementById('declineTrendChart');
    if (declineTrendCanvas) {
        const declineTrendData = @json($declinedAssets['trend']);
        
        const labels = declineTrendData.map(item => item.date);
        const counts = declineTrendData.map(item => item.count);
        
        const declineTrendChart = new Chart(declineTrendCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Declined Assets',
                    data: counts,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#dc3545',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return ' Declined: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Make chart responsive
        window.addEventListener('resize', function() {
            declineTrendChart.resize();
        });
    }
});
</script>
@endpush
