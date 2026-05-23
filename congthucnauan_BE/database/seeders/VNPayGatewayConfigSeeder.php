<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;

class VNPayGatewayConfigSeeder extends Seeder
{
    public function run(): void
    {
        $returnUrl = rtrim((string) env('APP_URL', 'http://127.0.0.1:8000'), '/') . '/payments/vnpay-return';

        $configs = [
            'tmn_code' => [
                'value' => env('VNPAY_TMN_CODE', 'TGQ78RUP'),
                'is_encrypted' => false,
            ],
            'hash_secret' => [
                'value' => env('VNPAY_HASH_SECRET', '1D4XCMDC467EABY7KFV8MH8OYOY2HN4Y'),
                'is_encrypted' => true,
            ],
            'url' => [
                'value' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
                'is_encrypted' => false,
            ],
            'return_url' => [
                'value' => env('VNPAY_RETURN_URL', $returnUrl),
                'is_encrypted' => false,
            ],
        ];

        foreach ($configs as $configKey => $config) {
            PaymentGatewayConfig::query()->updateOrCreate(
                [
                    'gateway' => 'vnpay',
                    'config_key' => $configKey,
                ],
                [
                    'config_value' => $config['value'],
                    'is_encrypted' => $config['is_encrypted'],
                    'is_active' => true,
                ]
            );
        }
    }
}
