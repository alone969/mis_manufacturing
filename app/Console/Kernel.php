<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Clear expired OTPs every hour
        $schedule->command('app:cleanup-otp')
            ->hourly()
            ->withoutOverlapping();

        // Clear expired sessions daily at 2 AM
        $schedule->command('session:gc')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // Backup database weekly on Sunday at 3 AM
        $schedule->command('backup:clean')
            ->daily()
            ->at('01:00')
            ->withoutOverlapping();

        $schedule->command('backup:run')
            ->weekly()
            ->sundays()
            ->at('03:00')
            ->withoutOverlapping();

        // Prune activity logs older than 90 days
        $schedule->command('app:prune-logs')
            ->daily()
            ->at('04:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
