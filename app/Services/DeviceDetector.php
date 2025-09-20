<?php

namespace App\Services;

use Illuminate\Http\Request;

class DeviceDetector
{
    protected $request;
    protected $userAgent;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?: request();
        $this->userAgent = $this->request->userAgent();
    }

    /**
     * Check if the device is mobile
     */
    public function isMobile(): bool
    {
        return $this->isMobileDevice() || $this->isTablet();
    }

    /**
     * Check if the device is a mobile phone
     */
    public function isMobileDevice(): bool
    {
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry',
            'Windows Phone', 'Opera Mini', 'IEMobile', 'Mobile Safari'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($this->userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the device is a tablet
     */
    public function isTablet(): bool
    {
        $tabletKeywords = ['iPad', 'Android', 'Tablet', 'Kindle', 'Silk'];

        foreach ($tabletKeywords as $keyword) {
            if (stripos($this->userAgent, $keyword) !== false) {
                // Additional check to exclude mobile phones
                if ($keyword === 'Android' && stripos($this->userAgent, 'Mobile') === false) {
                    return true;
                } elseif ($keyword !== 'Android') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the device is desktop
     */
    public function isDesktop(): bool
    {
        return !$this->isMobile();
    }

    /**
     * Get the device type
     */
    public function deviceType(): string
    {
        if ($this->isTablet()) {
            return 'tablet';
        } elseif ($this->isMobileDevice()) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Get the browser name
     */
    public function browser(): string
    {
        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'Opera' => 'Opera',
            'Internet Explorer' => 'MSIE'
        ];

        foreach ($browsers as $name => $pattern) {
            if (stripos($this->userAgent, $pattern) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Get the operating system
     */
    public function platform(): string
    {
        $platforms = [
            'Windows' => 'Windows',
            'Mac OS X' => 'Mac OS X',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iOS' => 'iPhone|iPad|iPod',
            'Windows Phone' => 'Windows Phone'
        ];

        foreach ($platforms as $name => $pattern) {
            if (preg_match('/' . $pattern . '/i', $this->userAgent)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Get the device information as an array
     */
    public function device(): array
    {
        return [
            'type' => $this->deviceType(),
            'browser' => $this->browser(),
            'platform' => $this->platform(),
            'is_mobile' => $this->isMobile(),
            'is_tablet' => $this->isTablet(),
            'is_desktop' => $this->isDesktop(),
            'user_agent' => $this->userAgent,
            'ip_address' => $this->request->ip(),
        ];
    }

    /**
     * Get a formatted device description
     */
    public function deviceDescription(): string
    {
        $device = $this->device();
        return sprintf(
            '%s on %s (%s)',
            $device['browser'],
            $device['platform'],
            ucfirst($device['type'])
        );
    }
}
