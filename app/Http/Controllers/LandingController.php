<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Pricing plans
        $plans = [
            [
                'name' => 'Free',
                'price' => 0,
                'duration' => 'Selamanya',
                'features' => [
                    '30 invoice/bulan',
                    'Unlimited customer',
                    'Unlimited produk',
                    'Invoice PDF',
                    'Kirim WhatsApp',
                ],
                'popular' => false,
            ],
            [
                'name' => 'Basic',
                'price' => 99000,
                'duration' => 'per bulan',
                'features' => [
                    '60 invoice/bulan',
                    'Unlimited customer',
                    'Unlimited produk',
                    'Invoice PDF',
                    'Kirim WhatsApp',
                    'Custom logo toko',
                ],
                'popular' => true,
            ],
            [
                'name' => 'Pro',
                'price' => 199000,
                'duration' => 'per bulan',
                'features' => [
                    '120 invoice/bulan',
                    'Unlimited customer',
                    'Unlimited produk',
                    'Invoice PDF',
                    'Kirim WhatsApp',
                    'Custom logo toko',
                    'Priority support',
                ],
                'popular' => false,
            ],
        ];

        return view('landing', compact('plans'));
    }
}
