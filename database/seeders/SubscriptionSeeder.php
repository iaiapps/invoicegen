<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Subscription untuk basic user
        Subscription::create([
            'user_id' => 3, // Basic User
            'plan' => 'basic',
            'invoice_limit' => 60, // Sesuai platform settings
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMonth(),
            'amount' => 25000, // Sesuai platform settings
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'payment_reference' => 'TRX-' . Carbon::now()->format('YmdHis'),
            'paid_at' => Carbon::now(),
        ]);

        // Subscription expired (untuk testing)
        Subscription::create([
            'user_id' => 3, // Basic User - subscription lama yang expired
            'plan' => 'basic',
            'invoice_limit' => 60, // Sesuai platform settings
            'starts_at' => Carbon::now()->subMonths(2),
            'ends_at' => Carbon::now()->subMonth(),
            'amount' => 25000, // Sesuai platform settings
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'payment_reference' => 'TRX-OLD-001',
            'paid_at' => Carbon::now()->subMonths(2),
        ]);

        // Subscription pending (untuk testing)
        Subscription::create([
            'user_id' => 2, // Free User yang mau upgrade
            'plan' => 'pro',
            'invoice_limit' => 120, // Sesuai platform settings
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMonth(),
            'amount' => 49000, // Sesuai platform settings
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'payment_reference' => 'TRX-PENDING-001',
        ]);
    }
}
