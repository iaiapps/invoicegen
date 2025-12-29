<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('platform_settings')->insert([
            // Free Plan - Perfect untuk UMKM Mikro
            [
                'key' => 'free_invoice_limit',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Limit invoice untuk paket Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_product_limit',
                'value' => '20',
                'type' => 'integer',
                'description' => 'Limit produk untuk paket Free',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Basic Plan - UMKM Kecil yang Berkembang
            [
                'key' => 'basic_price',
                'value' => '25000',
                'type' => 'integer',
                'description' => 'Harga langganan paket Basic per bulan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'basic_invoice_limit',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Limit invoice untuk paket Basic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'basic_product_limit',
                'value' => '40',
                'type' => 'integer',
                'description' => 'Limit produk untuk paket Basic',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pro Plan - UMKM yang Lebih Besar
            [
                'key' => 'pro_price',
                'value' => '49000',
                'type' => 'integer',
                'description' => 'Harga langganan paket Pro per bulan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pro_invoice_limit',
                'value' => '120',
                'type' => 'integer',
                'description' => 'Limit invoice untuk paket Pro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pro_product_limit',
                'value' => '80',
                'type' => 'integer',
                'description' => 'Limit produk untuk paket Pro',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Payment Info
            [
                'key' => 'bank_accounts',
                'value' => json_encode([
                    [
                        'bank' => 'BTN',
                        'account_number' => '3001500678918',
                        'account_name' => 'Ikromudin Al Islami',
                    ],
                    [
                        'bank' => 'BSI',
                        'account_number' => '7494991220',
                        'account_name' => 'Ikromudin Al Islami',
                    ],
                ]),
                'type' => 'json',
                'description' => 'Daftar rekening bank untuk pembayaran manual',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_admin',
                'value' => '6285232213939',
                'type' => 'string',
                'description' => 'Nomor WhatsApp admin untuk konfirmasi pembayaran',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Platform Settings
            [
                'key' => 'platform_name',
                'value' => 'InvoiceGen',
                'type' => 'string',
                'description' => 'Nama platform',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'grace_period_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Masa tenggang setelah subscription berakhir (hari)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
