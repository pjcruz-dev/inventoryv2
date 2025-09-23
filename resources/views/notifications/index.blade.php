@extends('layouts.app')

@section('title', 'Notifications')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-success btn-sm" onclick="markAllAsRead()" title="Mark all as read">
                <i class="fas fa-check-double me-1"></i>
                <span class="d-none d-lg-inline">Mark All Read</span>
            </button>
            <button class="btn btn-outline-warning btn-sm" onclick="markAllAsUnread()" title="Mark all as unread">
                <i class="fas fa-envelope me-1"></i>
                <span class="d-none d-lg-inline">Mark All Unread</span>
            </button>
        </div>
        <div class="btn-group" role="group">
            <button class="btn btn-outline-danger btn-sm" onclick="deleteRead()" title="Delete read notifications">
                <i class="fas fa-trash me-1"></i>
                <span class="d-none d-lg-inline">Delete Read</span>
            </button>
            <button class="btn btn-outline-secondary btn-sm" onclick="deleteAll()" title="Delete all notifications">
                <i class="fas fa-trash-alt me-1"></i>
                <span class="d-none d-lg-inline">Delete All</span>
            </button>
        </div>
        <button class="btn btn-primary btn-sm" onclick="refreshNotifications()" title="Refresh notifications">
            <i class="fas fa-sync-alt me-1"></i>
            <span class="d-none d-lg-inline">Refresh</span>
        </button>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Statistics Cards with Enhanced Design -->
        <div class="col-12 mb-4">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card stats-card-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-label">Total Notifications</h6>
                                    <h2 class="stats-value mb-0" id="total-notifications">{{ $stats['total'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card stats-card-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-label">Unread</h6>
                                    <h2 class="stats-value mb-0" id="unread-notifications">{{ $stats['unread'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card stats-card-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-label">Urgent</h6>
                                    <h2 class="stats-value mb-0" id="urgent-notifications">{{ $stats['urgent'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card stats-card-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="stats-label">Read</h6>
                                    <h2 class="stats-value mb-0" id="read-notifications">{{ $stats['read'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters and Search -->
        <div class="col-12 mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="filter-label">Filter by:</span>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" checked>
                                    <label class="btn btn-outline-primary btn-sm" for="filter-all">
                                        <i class="fas fa-list me-1"></i>All
                                    </label>

                                    <input type="radio" class="btn-check" name="filter" id="filter-unread" value="unread">
                                    <label class="btn btn-outline-warning btn-sm" for="filter-unread">
                                        <i class="fas fa-envelope me-1"></i>Unread
                                    </label>

                                    <input type="radio" class="btn-check" name="filter" id="filter-urgent" value="urgent">
                                    <label class="btn btn-outline-danger btn-sm" for="filter-urgent">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Urgent
                                    </label>

                                    <input type="radio" class="btn-check" name="filter" id="filter-read" value="read">
                                    <label class="btn btn-outline-success btn-sm" for="filter-read">
                                        <i class="fas fa-check-circle me-1"></i>Read
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="search-notifications" placeholder="Search notifications...">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()" title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Notifications List -->
        <div class="col-12">
            <div class="card notifications-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Notifications
                        <span class="badge bg-primary ms-2" id="notification-count">{{ $notifications->count() }}</span>
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleViewMode()" title="Toggle view mode">
                            <i class="fas fa-th" id="view-mode-icon"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleSelectMode()" title="Select notifications">
                            <i class="fas fa-check-square"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="notifications-container">
                        @if($notifications->count() > 0)
                            <div class="notifications-list" id="notifications-list">
                                @foreach($notifications as $notification)
                                    <div class="notification-card {{ $notification->isUnread() ? 'unread' : '' }} {{ $notification->is_urgent ? 'urgent' : '' }}" 
                                         data-notification-id="{{ $notification->id }}"
                                         data-type="{{ $notification->type }}"
                                         data-urgent="{{ $notification->is_urgent ? 'true' : 'false' }}">
                                        <div class="notification-content">
                                            <div class="notification-header">
                                                <div class="notification-icon-wrapper">
                                                    <div class="notification-icon bg-{{ $notification->color }}">
                                                        <i class="{{ $notification->icon }}"></i>
                                                    </div>
                                                    @if($notification->isUnread())
                                                        <div class="unread-indicator"></div>
                                                    @endif
                                                </div>
                                                <div class="notification-main">
                                                    <div class="notification-title">{{ $notification->title }}</div>
                                                    <div class="notification-message">{{ $notification->message }}</div>
                                                    <div class="notification-meta">
                                                        <span class="notification-time">
                                                            <i class="fas fa-clock me-1"></i>{{ $notification->time_ago }}
                                                        </span>
                                                        @if($notification->is_urgent)
                                                            <span class="badge bg-danger ms-2">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Urgent
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="notification-actions">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @if($notification->isUnread())
                                                                <li>
                                                                    <a class="dropdown-item" href="#" onclick="markAsRead({{ $notification->id }})">
                                                                        <i class="fas fa-check me-2"></i>Mark as Read
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item" href="#" onclick="markAsUnread({{ $notification->id }})">
                                                                        <i class="fas fa-envelope me-2"></i>Mark as Unread
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if($notification->action_url)
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ $notification->action_url }}">
                                                                        <i class="fas fa-external-link-alt me-2"></i>{{ $notification->action_text ?? 'View' }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="deleteNotification({{ $notification->id }})">
                                                                    <i class="fas fa-trash me-2"></i>Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-bell-slash"></i>
                                </div>
                                <h4>No notifications</h4>
                                <p>You're all caught up! No notifications to display.</p>
                                <button class="btn btn-primary" onclick="refreshNotifications()">
                                    <i class="fas fa-sync-alt me-1"></i>Refresh
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Enhanced Statistics Cards */
.stats-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stats-card-primary::before { background: linear-gradient(90deg, #007bff, #0056b3); }
.stats-card-warning::before { background: linear-gradient(90deg, #ffc107, #e0a800); }
.stats-card-danger::before { background: linear-gradient(90deg, #dc3545, #c82333); }
.stats-card-success::before { background: linear-gradient(90deg, #28a745, #1e7e34); }

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.stats-card-primary .stats-icon { background: linear-gradient(135deg, #007bff, #0056b3); }
.stats-card-warning .stats-icon { background: linear-gradient(135deg, #ffc107, #e0a800); }
.stats-card-danger .stats-icon { background: linear-gradient(135deg, #dc3545, #c82333); }
.stats-card-success .stats-icon { background: linear-gradient(135deg, #28a745, #1e7e34); }

.stats-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.stats-value {
    font-size: 2rem;
    font-weight: 700;
    color: #212529;
}

/* Filter Card */
.filter-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
}

.filter-label {
    font-weight: 600;
    color: #495057;
    margin-right: 0.5rem;
}

/* Notifications Card */
.notifications-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.notifications-list {
    max-height: 600px;
    overflow-y: auto;
}

/* Notification Cards */
.notification-card {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
    position: relative;
    background: white;
}

.notification-card:last-child {
    border-bottom: none;
}

.notification-card:hover {
    background: #f8f9fa;
    transform: translateX(4px);
}

.notification-card.unread {
    background: linear-gradient(90deg, rgba(0, 123, 255, 0.05), white);
    border-left: 4px solid #007bff;
}

.notification-card.urgent {
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.05), white);
    border-left: 4px solid #dc3545;
}

.notification-card.urgent.unread {
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.1), white);
}

.notification-content {
    padding: 1.5rem;
}

.notification-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.notification-icon-wrapper {
    position: relative;
    flex-shrink: 0;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.unread-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 12px;
    height: 12px;
    background: #007bff;
    border: 2px solid white;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

.notification-main {
    flex-grow: 1;
    min-width: 0;
}

.notification-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.notification-message {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.notification-time {
    font-size: 0.85rem;
    color: #6c757d;
    display: flex;
    align-items: center;
}

.notification-actions {
    flex-shrink: 0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1.5rem;
}

.empty-state h4 {
    color: #495057;
    margin-bottom: 1rem;
}

.empty-state p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

/* Dark Mode Styles */
[data-theme="dark"] .stats-card {
    background: #2d3748;
    color: #e2e8f0;
}

[data-theme="dark"] .stats-value {
    color: #e2e8f0;
}

[data-theme="dark"] .stats-label {
    color: #a0aec0;
}

[data-theme="dark"] .filter-card {
    background: linear-gradient(135deg, #2d3748, #4a5568);
}

[data-theme="dark"] .filter-label {
    color: #e2e8f0;
}

[data-theme="dark"] .notifications-card {
    background: #2d3748;
    color: #e2e8f0;
}

[data-theme="dark"] .notification-card {
    background: #2d3748;
    border-color: #4a5568;
}

[data-theme="dark"] .notification-card:hover {
    background: #4a5568;
}

[data-theme="dark"] .notification-card.unread {
    background: linear-gradient(90deg, rgba(0, 123, 255, 0.1), #2d3748);
}

[data-theme="dark"] .notification-card.urgent {
    background: linear-gradient(90deg, rgba(220, 53, 69, 0.1), #2d3748);
}

[data-theme="dark"] .notification-title {
    color: #e2e8f0;
}

[data-theme="dark"] .notification-message {
    color: #a0aec0;
}

[data-theme="dark"] .notification-time {
    color: #a0aec0;
}

[data-theme="dark"] .empty-state {
    color: #a0aec0;
}

[data-theme="dark"] .empty-state h4 {
    color: #e2e8f0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .notification-header {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .notification-actions {
        align-self: flex-end;
    }
    
    .notification-content {
        padding: 1rem;
    }
    
    .stats-card .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-icon {
        margin-bottom: 1rem;
    }
}

/* Custom Scrollbar */
.notifications-list::-webkit-scrollbar {
    width: 6px;
}

.notifications-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.notifications-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notifications-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

[data-theme="dark"] .notifications-list::-webkit-scrollbar-track {
    background: #4a5568;
}

[data-theme="dark"] .notifications-list::-webkit-scrollbar-thumb {
    background: #718096;
}

[data-theme="dark"] .notifications-list::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>
@endpush

@push('scripts')
<script>
// Notification management functions
function markAsRead(notificationId) {
    fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
            }
            updateNotificationCounts();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('unread');
                });
                updateNotificationCounts();
                showAlert('All notifications marked as read', 'success');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function deleteNotification(notificationId) {
    if (confirm('Delete this notification?')) {
        fetch(`/api/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove from UI
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.remove();
                }
                updateNotificationCounts();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function deleteRead() {
    if (confirm('Delete all read notifications?')) {
        fetch('/api/notifications/delete-read', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove read notifications from UI
                document.querySelectorAll('.notification-item:not(.unread)').forEach(item => {
                    item.remove();
                });
                updateNotificationCounts();
                showAlert('Read notifications deleted', 'success');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function refreshNotifications() {
    location.reload();
}

function searchNotifications() {
    const searchTerm = document.getElementById('search-notifications').value.toLowerCase();
    const notificationItems = document.querySelectorAll('.notification-item');
    
    notificationItems.forEach(item => {
        const title = item.querySelector('.notification-title').textContent.toLowerCase();
        const message = item.querySelector('.notification-message').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || message.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function updateNotificationCounts() {
    // This would typically fetch updated counts from the server
    // For now, we'll just update the UI based on visible elements
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const totalCount = document.querySelectorAll('.notification-item').length;
    
    document.getElementById('unread-notifications').textContent = unreadCount;
    document.getElementById('total-notifications').textContent = totalCount;
    document.getElementById('read-notifications').textContent = totalCount - unreadCount;
}

function showAlert(message, type = 'info') {
    // Create and show a Bootstrap alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

// Filter functionality
document.querySelectorAll('input[name="filter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const filter = this.value;
        const notificationItems = document.querySelectorAll('.notification-item');
        
        notificationItems.forEach(item => {
            switch(filter) {
                case 'all':
                    item.style.display = 'block';
                    break;
                case 'unread':
                    item.style.display = item.classList.contains('unread') ? 'block' : 'none';
                    break;
                case 'urgent':
                    item.style.display = item.classList.contains('urgent') ? 'block' : 'none';
                    break;
            }
        });
    });
});

// Search functionality
document.getElementById('search-notifications').addEventListener('input', searchNotifications);
</script>
@endpush
