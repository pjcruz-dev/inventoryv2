@if(!empty($breadcrumbs) && count($breadcrumbs) > 0)
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <ol class="breadcrumb">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="breadcrumb-item {{ $breadcrumb['active'] ? 'active' : '' }}" 
                    {{ $breadcrumb['active'] ? 'aria-current="page"' : '' }}>
                    
                    @if($breadcrumb['icon'])
                        <div class="breadcrumb-icon">
                            <i class="{{ $breadcrumb['icon'] }}"></i>
                        </div>
                    @endif
                    
                    @if($breadcrumb['url'] && !$breadcrumb['active'])
                        <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">
                            {{ $breadcrumb['title'] }}
                        </a>
                    @else
                        <span class="breadcrumb-text">
                            {{ $breadcrumb['title'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>

<style>
/* Breadcrumb Container - Integrated with page title */
.breadcrumb-container {
    margin-top: 0.5rem;
    margin-bottom: 0;
}

.breadcrumb-nav {
    background: transparent;
    border-radius: 0;
    padding: 0;
    box-shadow: none;
    border: none;
    position: relative;
    overflow: visible;
}

.breadcrumb-nav::before {
    display: none;
}

.breadcrumb {
    margin-bottom: 0;
    background: transparent;
    padding: 0;
    font-size: 0.875rem;
    font-weight: 500;
    position: relative;
    z-index: 1;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    color: #6c757d;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    margin-right: 0.25rem;
    background: rgba(248, 249, 250, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.breadcrumb-item:hover {
    background: rgba(255, 255, 255, 0.8);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(163, 177, 198, 0.3);
    border-color: rgba(102, 126, 234, 0.3);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "";
    width: 6px;
    height: 6px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    margin: 0 0.75rem;
    position: relative;
    z-index: 1;
}

.breadcrumb-icon {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
}

.breadcrumb-icon i {
    color: white;
    font-size: 0.75rem;
}

.breadcrumb-item.active {
    color: #495057;
    font-weight: 600;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    border-color: rgba(102, 126, 234, 0.4);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
}

.breadcrumb-item.active .breadcrumb-text {
    color: #495057;
}

.breadcrumb-item.active .breadcrumb-icon {
    background: linear-gradient(135deg, #cb0c9f 0%, #ad1457 100%);
    box-shadow: 0 2px 4px rgba(203, 12, 159, 0.3);
}

.breadcrumb-link {
    color: #667eea;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.breadcrumb-link:hover {
    color: #764ba2;
    text-decoration: none;
}

.breadcrumb-text {
    color: inherit;
    font-weight: inherit;
}

/* Responsive Design */
@media (max-width: 768px) {
    .breadcrumb-container {
        margin-top: 0.25rem;
    }
    
    .breadcrumb {
        font-size: 0.8rem;
        flex-wrap: wrap;
    }
    
    .breadcrumb-item {
        margin-bottom: 0.25rem;
        padding: 0.2rem 0.4rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        margin: 0 0.5rem;
    }
    
    .breadcrumb-icon {
        width: 18px;
        height: 18px;
        margin-right: 0.4rem;
    }
    
    .breadcrumb-icon i {
        font-size: 0.7rem;
    }
}

@media (max-width: 576px) {
    .breadcrumb-container {
        margin-top: 0.25rem;
    }
    
    .breadcrumb {
        font-size: 0.75rem;
    }
    
    .breadcrumb-item {
        padding: 0.15rem 0.3rem;
    }
    
    .breadcrumb-icon {
        width: 16px;
        height: 16px;
        margin-right: 0.3rem;
    }
    
    .breadcrumb-icon i {
        font-size: 0.65rem;
    }
}

/* Animation for breadcrumb items */
.breadcrumb-item:not(.active):hover {
    transform: translateY(-1px) scale(1.02);
}

.breadcrumb-item.active:hover {
    transform: none;
}

/* Soft UI separator dots */
.breadcrumb-item + .breadcrumb-item::before {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}
</style>
@endif
