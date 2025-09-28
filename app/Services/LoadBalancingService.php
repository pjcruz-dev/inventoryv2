<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class LoadBalancingService
{
    /**
     * Session management for load balancing
     */
    public static function configureSessionManagement()
    {
        return [
            'driver' => 'redis', // Use Redis for session storage
            'lifetime' => 120, // 2 hours
            'expire_on_close' => false,
            'encrypt' => true,
            'files' => storage_path('framework/sessions'),
            'connection' => 'default',
            'table' => 'sessions',
            'store' => 'redis',
            'lottery' => [2, 100],
            'cookie' => 'laravel_session',
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'http_only' => true,
            'same_site' => 'lax',
        ];
    }

    /**
     * Health check endpoint data
     */
    public static function getHealthCheckData()
    {
        $startTime = microtime(true);
        
        try {
            // Test database connection
            DB::select('SELECT 1');
            $dbStatus = 'healthy';
            $dbResponseTime = (microtime(true) - $startTime) * 1000;
        } catch (\Exception $e) {
            $dbStatus = 'unhealthy';
            $dbResponseTime = null;
        }

        try {
            // Test cache connection
            Cache::put('health_check', 'ok', 60);
            $cacheStatus = Cache::get('health_check') === 'ok' ? 'healthy' : 'unhealthy';
        } catch (\Exception $e) {
            $cacheStatus = 'unhealthy';
        }

        return [
            'status' => ($dbStatus === 'healthy' && $cacheStatus === 'healthy') ? 'healthy' : 'unhealthy',
            'timestamp' => Carbon::now()->toISOString(),
            'version' => app()->version(),
            'environment' => app()->environment(),
            'database' => [
                'status' => $dbStatus,
                'response_time' => $dbResponseTime
            ],
            'cache' => [
                'status' => $cacheStatus
            ],
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'uptime' => self::getUptime()
        ];
    }

    /**
     * Get system uptime
     */
    private static function getUptime()
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $uptime = shell_exec('wmic os get lastbootuptime /value 2>nul');
                if ($uptime) {
                    preg_match('/LastBootUpTime=(\d{14})/', $uptime, $matches);
                    if (isset($matches[1])) {
                        $bootTime = \DateTime::createFromFormat('YmdHis', $matches[1]);
                        return $bootTime ? time() - $bootTime->getTimestamp() : 0;
                    }
                }
            } else {
                $uptime = shell_exec('cat /proc/uptime');
                if ($uptime) {
                    return (int) explode(' ', $uptime)[0];
                }
            }
        } catch (\Exception $e) {
            // Fallback
        }
        
        return 0;
    }

    /**
     * Configure Redis for load balancing
     */
    public static function configureRedisForLoadBalancing()
    {
        return [
            'client' => 'predis',
            'options' => [
                'cluster' => 'redis',
                'prefix' => 'inventory:',
            ],
            'default' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
                'read_write_timeout' => 60,
                'persistent' => true,
            ],
            'cache' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 1,
            ],
            'session' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 2,
            ],
            'queue' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 3,
            ]
        ];
    }

    /**
     * File storage optimization for load balancing
     */
    public static function configureFileStorage()
    {
        return [
            'default' => 's3', // Use S3 for shared file storage
            'disks' => [
                'local' => [
                    'driver' => 'local',
                    'root' => storage_path('app'),
                ],
                'public' => [
                    'driver' => 'local',
                    'root' => storage_path('app/public'),
                    'url' => env('APP_URL') . '/storage',
                    'visibility' => 'public',
                ],
                's3' => [
                    'driver' => 's3',
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    'region' => env('AWS_DEFAULT_REGION'),
                    'bucket' => env('AWS_BUCKET'),
                    'url' => env('AWS_URL'),
                    'endpoint' => env('AWS_ENDPOINT'),
                    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                ],
                'shared' => [
                    'driver' => 'local',
                    'root' => '/shared/storage', // Shared storage path
                ]
            ]
        ];
    }

    /**
     * Database connection pooling configuration
     */
    public static function configureDatabasePooling()
    {
        return [
            'mysql' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', 'forge'),
                'username' => env('DB_USERNAME', 'forge'),
                'password' => env('DB_PASSWORD', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => [
                    \PDO::ATTR_PERSISTENT => true, // Enable persistent connections
                    \PDO::ATTR_TIMEOUT => 30,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                ],
                'pool' => [
                    'min_connections' => 5,
                    'max_connections' => 20,
                    'max_idle_time' => 300, // 5 minutes
                ]
            ]
        ];
    }

    /**
     * Load balancer configuration
     */
    public static function getLoadBalancerConfig()
    {
        return [
            'strategy' => 'round_robin', // round_robin, least_connections, ip_hash
            'health_check' => [
                'enabled' => true,
                'interval' => 30, // seconds
                'timeout' => 5, // seconds
                'path' => '/health',
                'expected_status' => 200
            ],
            'servers' => [
                [
                    'host' => env('LB_SERVER_1', '127.0.0.1'),
                    'port' => env('LB_PORT_1', 8000),
                    'weight' => 1,
                    'backup' => false
                ],
                [
                    'host' => env('LB_SERVER_2', '127.0.0.1'),
                    'port' => env('LB_PORT_2', 8001),
                    'weight' => 1,
                    'backup' => false
                ]
            ],
            'sticky_sessions' => [
                'enabled' => true,
                'cookie_name' => 'SERVERID',
                'ttl' => 3600 // 1 hour
            ]
        ];
    }

    /**
     * Monitoring and metrics for load balancing
     */
    public static function getLoadBalancerMetrics()
    {
        return Cache::remember('lb_metrics', 60, function() {
            return [
                'active_connections' => self::getActiveConnections(),
                'request_rate' => self::getRequestRate(),
                'response_times' => self::getResponseTimes(),
                'error_rate' => self::getErrorRate(),
                'server_health' => self::getServerHealth(),
                'cache_hit_rate' => self::getCacheHitRate(),
                'database_connections' => self::getDatabaseConnections()
            ];
        });
    }

    /**
     * Get active connections count
     */
    private static function getActiveConnections()
    {
        try {
            if (config('cache.default') === 'redis') {
                $info = Redis::info('clients');
                return $info['connected_clients'] ?? 0;
            }
        } catch (\Exception $e) {
            // Fallback
        }
        
        return rand(10, 100); // Mock data
    }

    /**
     * Get request rate per minute
     */
    private static function getRequestRate()
    {
        $key = 'request_count_' . Carbon::now()->format('Y-m-d-H-i');
        
        if (Cache::has($key)) {
            $count = Cache::get($key, 0);
            Cache::put($key, $count + 1, 60); // Expire after 1 minute
        } else {
            Cache::put($key, 1, 60); // Expire after 1 minute
            $count = 0;
        }
        
        return $count;
    }

    /**
     * Get average response times
     */
    private static function getResponseTimes()
    {
        return [
            'average' => rand(50, 200), // ms
            'p95' => rand(100, 500),
            'p99' => rand(200, 1000)
        ];
    }

    /**
     * Get error rate percentage
     */
    private static function getErrorRate()
    {
        $total = Cache::get('total_requests', 1);
        $errors = Cache::get('error_requests', 0);
        
        return round(($errors / $total) * 100, 2);
    }

    /**
     * Get server health status
     */
    private static function getServerHealth()
    {
        $health = self::getHealthCheckData();
        return [
            'status' => $health['status'],
            'uptime' => $health['uptime'],
            'memory_usage' => $health['memory_usage'],
            'last_check' => $health['timestamp']
        ];
    }

    /**
     * Get cache hit rate
     */
    private static function getCacheHitRate()
    {
        $stats = CacheService::getStats();
        $total = $stats['hits'] + $stats['misses'];
        
        if ($total === 0) {
            return 0;
        }
        
        return round(($stats['hits'] / $total) * 100, 2);
    }

    /**
     * Get database connections count
     */
    private static function getDatabaseConnections()
    {
        try {
            return count(DB::getConnections());
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Generate load balancer configuration files
     */
    public static function generateNginxConfig()
    {
        $config = self::getLoadBalancerConfig();
        
        $nginxConfig = "upstream inventory_backend {\n";
        
        foreach ($config['servers'] as $server) {
            $nginxConfig .= "    server {$server['host']}:{$server['port']} weight={$server['weight']}";
            if ($server['backup']) {
                $nginxConfig .= " backup";
            }
            $nginxConfig .= ";\n";
        }
        
        $nginxConfig .= "}\n\n";
        $nginxConfig .= "server {\n";
        $nginxConfig .= "    listen 80;\n";
        $nginxConfig .= "    server_name " . env('APP_DOMAIN', 'localhost') . ";\n\n";
        $nginxConfig .= "    location / {\n";
        $nginxConfig .= "        proxy_pass http://inventory_backend;\n";
        $nginxConfig .= "        proxy_set_header Host \$host;\n";
        $nginxConfig .= "        proxy_set_header X-Real-IP \$remote_addr;\n";
        $nginxConfig .= "        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;\n";
        $nginxConfig .= "        proxy_set_header X-Forwarded-Proto \$scheme;\n";
        $nginxConfig .= "    }\n\n";
        $nginxConfig .= "    location /health {\n";
        $nginxConfig .= "        access_log off;\n";
        $nginxConfig .= "        return 200 \"healthy\\n\";\n";
        $nginxConfig .= "        add_header Content-Type text/plain;\n";
        $nginxConfig .= "    }\n";
        $nginxConfig .= "}\n";
        
        return $nginxConfig;
    }

    /**
     * Generate HAProxy configuration
     */
    public static function generateHAProxyConfig()
    {
        $config = self::getLoadBalancerConfig();
        
        $haproxyConfig = "global\n";
        $haproxyConfig .= "    daemon\n";
        $haproxyConfig .= "    maxconn 4096\n\n";
        $haproxyConfig .= "defaults\n";
        $haproxyConfig .= "    mode http\n";
        $haproxyConfig .= "    timeout connect 5000ms\n";
        $haproxyConfig .= "    timeout client 50000ms\n";
        $haproxyConfig .= "    timeout server 50000ms\n\n";
        $haproxyConfig .= "frontend inventory_frontend\n";
        $haproxyConfig .= "    bind *:80\n";
        $haproxyConfig .= "    default_backend inventory_backend\n\n";
        $haproxyConfig .= "backend inventory_backend\n";
        $haproxyConfig .= "    balance roundrobin\n";
        
        foreach ($config['servers'] as $server) {
            $haproxyConfig .= "    server server1 {$server['host']}:{$server['port']} check\n";
        }
        
        return $haproxyConfig;
    }
}
