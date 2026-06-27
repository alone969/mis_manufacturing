<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class CleanupOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired OTP records from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = Otp::where('expires_at', '<', now())->delete();

        $this->info("Cleaned up {$deleted} expired OTP records.");

        return Command::SUCCESS;
    }
}
