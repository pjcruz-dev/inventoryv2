<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MobileService;

class MobileDetection
{
    protected $mobileService;

    public function __construct(MobileService $mobileService)
    {
        $this->mobileService = $mobileService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Add mobile detection data to the request
        $request->merge([
            'is_mobile' => $this->mobileService->isMobile(),
            'is_tablet' => $this->mobileService->isTablet(),
            'is_mobile_or_tablet' => $this->mobileService->isMobileOrTablet(),
            'device_type' => $this->mobileService->getDeviceType(),
            'mobile_config' => [
                'pagination_limit' => $this->mobileService->getPaginationLimit(),
                'button_sizes' => $this->mobileService->getButtonSizes(),
                'modal_sizes' => $this->mobileService->getModalSizes(),
                'touch_gestures' => $this->mobileService->shouldEnableTouchGestures(),
                'form_layout' => $this->mobileService->getFormLayout(),
                'navigation_style' => $this->mobileService->getNavigationStyle(),
                'search_behavior' => $this->mobileService->getSearchBehavior(),
                'image_sizes' => $this->mobileService->getImageSizes(),
                'performance_settings' => $this->mobileService->getPerformanceSettings(),
                'device_classes' => $this->mobileService->getDeviceClasses()
            ]
        ]);

        return $next($request);
    }
}

