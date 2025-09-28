<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SecurityMonitoringService;

class SecurityMonitoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:monitor {--interval=60 : Monitoring interval in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run continuous security monitoring';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        
        $this->info("Starting security monitoring (interval: {$interval}s)");
        $this->info('Press Ctrl+C to stop');
        
        while (true) {
            try {
                $result = SecurityMonitoringService::monitorSecurityEvents();
                
                $this->line(sprintf(
                    '[%s] Events analyzed: %d, Threats detected: %d',
                    now()->format('Y-m-d H:i:s'),
                    $result['events_analyzed'],
                    $result['threats_detected']
                ));
                
                if ($result['threats_detected'] > 0) {
                    $this->warn("⚠️  {$result['threats_detected']} threats detected!");
                }
                
            } catch (\Exception $e) {
                $this->error("Error during monitoring: " . $e->getMessage());
            }
            
            sleep($interval);
        }
    }
}
