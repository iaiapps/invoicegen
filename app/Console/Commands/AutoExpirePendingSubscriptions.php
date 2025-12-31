<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class AutoExpirePendingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-expire pending subscriptions older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking pending subscriptions older than 7 days...');

        // Get auto-expire days from platform settings (default 7 days)
        $expireDays = (int) \App\Models\PlatformSetting::get('pending_expire_days', 7);

        // Find pending subscriptions older than configured days
        $expiredPending = Subscription::where('payment_status', 'pending')
            ->where('created_at', '<', now()->subDays($expireDays))
            ->get();

        $count = 0;

        foreach ($expiredPending as $subscription) {
            $subscription->update([
                'payment_status' => 'expired',
                'payment_response' => "Auto-expired: No payment received within {$expireDays} days",
            ]);

            $this->line("✓ Expired pending subscription: {$subscription->payment_reference} (User: {$subscription->user->email})");
            $count++;
        }

        if ($count > 0) {
            $this->info("✓ Successfully expired {$count} pending subscription(s)");
        } else {
            $this->info('✓ No pending subscriptions to expire');
        }

        return Command::SUCCESS;
    }
}
