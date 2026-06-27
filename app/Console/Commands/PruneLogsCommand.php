<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\DeviceLog;
use Illuminate\Console\Command;

class PruneLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-logs {--days=90 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old activity logs and device logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $activityLogsDeleted = ActivityLog::where('created_at', '<', $cutoff)->delete();
        $deviceLogsDeleted = DeviceLog::where('last_login_at', '<', $cutoff)->delete();

        $this->info("Pruned {$activityLogsDeleted} activity logs older than {$days} days.");
        $this->info("Pruned {$deviceLogsDeleted} device logs older than {$days} days.");

        return Command::SUCCESS;
    }
}
