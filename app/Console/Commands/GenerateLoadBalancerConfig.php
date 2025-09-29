<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LoadBalancingService;

class GenerateLoadBalancerConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lb:generate-config {type : The type of configuration (nginx, haproxy, apache)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate load balancer configuration files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        switch (strtolower($type)) {
            case 'nginx':
                $this->generateNginxConfig();
                break;
            case 'haproxy':
                $this->generateHAProxyConfig();
                break;
            case 'apache':
                $this->generateApacheConfig();
                break;
            default:
                $this->error('Invalid configuration type. Use: nginx, haproxy, or apache');
                return 1;
        }
        
        return 0;
    }

    /**
     * Generate Nginx configuration
     */
    private function generateNginxConfig()
    {
        $config = LoadBalancingService::generateNginxConfig();
        $filename = 'nginx-load-balancer.conf';
        
        file_put_contents(storage_path("app/{$filename}"), $config);
        
        $this->info("Nginx configuration generated: storage/app/{$filename}");
        $this->line("Copy this file to your Nginx sites-available directory and enable it.");
    }

    /**
     * Generate HAProxy configuration
     */
    private function generateHAProxyConfig()
    {
        $config = LoadBalancingService::generateHAProxyConfig();
        $filename = 'haproxy-load-balancer.cfg';
        
        file_put_contents(storage_path("app/{$filename}"), $config);
        
        $this->info("HAProxy configuration generated: storage/app/{$filename}");
        $this->line("Copy this file to your HAProxy configuration directory.");
    }

    /**
     * Generate Apache configuration
     */
    private function generateApacheConfig()
    {
        $config = $this->generateApacheLoadBalancerConfig();
        $filename = 'apache-load-balancer.conf';
        
        file_put_contents(storage_path("app/{$filename}"), $config);
        
        $this->info("Apache configuration generated: storage/app/{$filename}");
        $this->line("Copy this file to your Apache sites-available directory and enable it.");
    }

    /**
     * Generate Apache load balancer configuration
     */
    private function generateApacheLoadBalancerConfig()
    {
        $lbConfig = LoadBalancingService::getLoadBalancerConfig();
        
        $config = "<VirtualHost *:80>\n";
        $config .= "    ServerName " . env('APP_DOMAIN', 'localhost') . "\n";
        $config .= "    DocumentRoot /var/www/html\n\n";
        
        // Load balancer configuration
        $config .= "    <Proxy balancer://inventory_cluster>\n";
        foreach ($lbConfig['servers'] as $index => $server) {
            $config .= "        BalancerMember http://{$server['host']}:{$server['port']}";
            if ($server['backup']) {
                $config .= " status=+H";
            }
            $config .= "\n";
        }
        $config .= "    </Proxy>\n\n";
        
        // Proxy configuration
        $config .= "    ProxyPreserveHost On\n";
        $config .= "    ProxyPass / balancer://inventory_cluster/\n";
        $config .= "    ProxyPassReverse / balancer://inventory_cluster/\n\n";
        
        // Health check
        $config .= "    <Location /health>\n";
        $config .= "        ProxyPass balancer://inventory_cluster/health\n";
        $config .= "    </Location>\n\n";
        
        $config .= "    ErrorLog \${APACHE_LOG_DIR}/inventory_error.log\n";
        $config .= "    CustomLog \${APACHE_LOG_DIR}/inventory_access.log combined\n";
        $config .= "</VirtualHost>\n";
        
        return $config;
    }
}
