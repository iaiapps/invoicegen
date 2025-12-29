<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@invoicegen.web.id',
            'password' => Hash::make('passwordinvoicegen1234'),
        ])->assignRole('admin');


        // Create Free User
        // $user = User::create([
        //     'name' => 'Free User',
        //     'email' => 'free@gmail.com',
        //     'password' => Hash::make('password'),
        //     'shop_name' => 'Toko Sejahtera',
        //     'shop_address' => 'Jl. Merdeka No. 123, Jakarta',
        //     'shop_phone' => '081234567891',
        //     'subscription_plan' => 'free',
        //     'subscription_ends_at' => null, // Free plan tidak ada expiry
        //     'invoice_limit' => 20, // Sesuai platform settings
        //     'product_limit' => 20, // Sesuai platform settings
        //     'invoice_count_this_month' => 0,
        //     'is_active' => true,
        // ])->assignRole('user');

        // Create Test User (Basic Plan)
        // $basicUser = User::create([
        //     'name' => 'Basic User',
        //     'email' => 'basic@gmail.com',
        //     'password' => Hash::make('password'),
        //     'shop_name' => 'Toko Maju Jaya',
        //     'shop_address' => 'Jl. Sudirman No. 45, Jakarta',
        //     'shop_phone' => '081234567892',
        //     'subscription_plan' => 'basic',
        //     'subscription_ends_at' => Carbon::now()->addMonth(),
        //     'invoice_limit' => 60, // Sesuai platform settings
        //     'product_limit' => 40, // Sesuai platform settings
        //     'invoice_count_this_month' => 5,
        //     'is_active' => true,
        // ])->assignRole('user');
    }
}
