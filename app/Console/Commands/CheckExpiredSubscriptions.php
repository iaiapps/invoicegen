<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check expired subscriptions and auto-downgrade after 7 days grace period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking expired subscriptions...');

        // Find users with expired subscriptions that passed grace period
        $usersToDowngrade = User::where('subscription_plan', '!=', 'free')
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<', now()->subDays(7))
            ->get();

        $count = 0;

        foreach ($usersToDowngrade as $user) {
            // Auto downgrade to free
            $user->update([
                'subscription_plan' => 'free',
                'invoice_limit' => 30,
                'subscription_ends_at' => null,
            ]);

            $this->line("✓ Downgraded user: {$user->email} to FREE plan");
            $count++;

            // TODO: Send email notification (optional)
            // Mail::to($user->email)->send(new SubscriptionDowngraded($user));
        }

        if ($count > 0) {
            $this->info("✓ Successfully downgraded {$count} user(s) to FREE plan");
        } else {
            $this->info('✓ No users to downgrade');
        }

        return Command::SUCCESS;
    }
}
