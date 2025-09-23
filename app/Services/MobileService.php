<?php

namespace App\Services;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class MobileService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Check if the request is from a mobile device
     */
    public function isMobile()
    {
        return $this->agent->isMobile();
    }

    /**
     * Check if the request is from a tablet
     */
    public function isTablet()
    {
        return $this->agent->isTablet();
    }

    /**
     * Check if the request is from a mobile or tablet
     */
    public function isMobileOrTablet()
    {
        return $this->isMobile() || $this->isTablet();
    }

    /**
     * Get device type
     */
    public function getDeviceType()
    {
        if ($this->isMobile()) {
            return 'mobile';
        } elseif ($this->isTablet()) {
            return 'tablet';
        }
        return 'desktop';
    }

    /**
     * Get mobile-optimized pagination limit
     */
    public function getPaginationLimit()
    {
        if ($this->isMobile()) {
            return 10;
        } elseif ($this->isTablet()) {
            return 15;
        }
        return 20;
    }

    /**
     * Get mobile-optimized table columns
     */
    public function getTableColumns($entity)
    {
        $columns = [
            'assets' => [
                'mobile' => ['asset_tag', 'name', 'status', 'actions'],
                'tablet' => ['asset_tag', 'name', 'category', 'status', 'assigned_to', 'actions'],
                'desktop' => ['asset_tag', 'name', 'category', 'status', 'assigned_to', 'location', 'actions']
            ],
            'users' => [
                'mobile' => ['name', 'department', 'status', 'actions'],
                'tablet' => ['name', 'email', 'department', 'status', 'actions'],
                'desktop' => ['name', 'email', 'department', 'position', 'status', 'actions']
            ]
        ];

        $deviceType = $this->getDeviceType();
        return $columns[$entity][$deviceType] ?? $columns[$entity]['desktop'];
    }

    /**
     * Get mobile-optimized card layout
     */
    public function getCardLayout($entity)
    {
        $layouts = [
            'assets' => [
                'mobile' => 'grid',
                'tablet' => 'grid',
                'desktop' => 'table'
            ],
            'users' => [
                'mobile' => 'list',
                'tablet' => 'grid',
                'desktop' => 'table'
            ]
        ];

        $deviceType = $this->getDeviceType();
        return $layouts[$entity][$deviceType] ?? $layouts[$entity]['desktop'];
    }

    /**
     * Get touch-friendly button sizes
     */
    public function getButtonSizes()
    {
        if ($this->isMobile()) {
            return [
                'small' => 'btn-sm',
                'medium' => 'btn',
                'large' => 'btn-lg'
            ];
        } elseif ($this->isTablet()) {
            return [
                'small' => 'btn-sm',
                'medium' => 'btn',
                'large' => 'btn-lg'
            ];
        }
        return [
            'small' => 'btn-sm',
            'medium' => 'btn',
            'large' => 'btn-lg'
        ];
    }

    /**
     * Get mobile-optimized modal sizes
     */
    public function getModalSizes()
    {
        if ($this->isMobile()) {
            return [
                'small' => 'modal-sm',
                'medium' => '',
                'large' => 'modal-lg',
                'fullscreen' => 'modal-fullscreen'
            ];
        }
        return [
            'small' => 'modal-sm',
            'medium' => '',
            'large' => 'modal-lg',
            'fullscreen' => 'modal-fullscreen'
        ];
    }

    /**
     * Check if touch gestures should be enabled
     */
    public function shouldEnableTouchGestures()
    {
        return $this->isMobileOrTablet();
    }

    /**
     * Get mobile-optimized form layout
     */
    public function getFormLayout()
    {
        if ($this->isMobile()) {
            return 'vertical';
        } elseif ($this->isTablet()) {
            return 'mixed';
        }
        return 'horizontal';
    }

    /**
     * Get mobile-optimized navigation style
     */
    public function getNavigationStyle()
    {
        if ($this->isMobile()) {
            return 'bottom-tabs';
        } elseif ($this->isTablet()) {
            return 'sidebar';
        }
        return 'sidebar';
    }

    /**
     * Get mobile-optimized search behavior
     */
    public function getSearchBehavior()
    {
        if ($this->isMobile()) {
            return [
                'autocomplete' => true,
                'suggestions' => 5,
                'debounce' => 500,
                'minLength' => 2
            ];
        }
        return [
            'autocomplete' => true,
            'suggestions' => 10,
            'debounce' => 300,
            'minLength' => 2
        ];
    }

    /**
     * Get mobile-optimized image sizes
     */
    public function getImageSizes()
    {
        if ($this->isMobile()) {
            return [
                'thumbnail' => '50x50',
                'small' => '100x100',
                'medium' => '200x200',
                'large' => '400x400'
            ];
        } elseif ($this->isTablet()) {
            return [
                'thumbnail' => '60x60',
                'small' => '120x120',
                'medium' => '250x250',
                'large' => '500x500'
            ];
        }
        return [
            'thumbnail' => '80x80',
            'small' => '150x150',
            'medium' => '300x300',
            'large' => '600x600'
        ];
    }

    /**
     * Get mobile-optimized breakpoints
     */
    public function getBreakpoints()
    {
        return [
            'xs' => '0px',
            'sm' => '576px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1200px',
            'xxl' => '1400px'
        ];
    }

    /**
     * Get device-specific CSS classes
     */
    public function getDeviceClasses()
    {
        $deviceType = $this->getDeviceType();
        return [
            'device' => "device-{$deviceType}",
            'touch' => $this->shouldEnableTouchGestures() ? 'touch-enabled' : 'no-touch',
            'mobile' => $this->isMobile() ? 'mobile-device' : '',
            'tablet' => $this->isTablet() ? 'tablet-device' : '',
            'desktop' => (!$this->isMobileOrTablet()) ? 'desktop-device' : ''
        ];
    }

    /**
     * Get mobile-optimized performance settings
     */
    public function getPerformanceSettings()
    {
        if ($this->isMobile()) {
            return [
                'lazyLoad' => true,
                'imageOptimization' => true,
                'minimalAnimations' => true,
                'reducedMotion' => true,
                'batchRequests' => true
            ];
        }
        return [
            'lazyLoad' => false,
            'imageOptimization' => true,
            'minimalAnimations' => false,
            'reducedMotion' => false,
            'batchRequests' => false
        ];
    }
}

