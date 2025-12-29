<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customers
        Customer::create([
            'user_id' => '2',
            'name' => 'PT. Maju Jaya',
            'phone' => '082198765432',
            'email' => 'majujaya@example.com',
            'address' => 'Jl. Sudirman No. 45, Jakarta',
        ]);

        Customer::create([
            'user_id' => '2',
            'name' => 'CV. Berkah Abadi',
            'phone' => '081234567891',
            'email' => 'berkah@example.com',
            'address' => 'Jl. Gatot Subroto No. 88, Jakarta',
        ]);
    }
}
