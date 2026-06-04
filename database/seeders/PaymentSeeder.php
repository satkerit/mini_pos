<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $qrisConfig = PaymentGatewayConfig::create([
            'gateway_code' => 'qris',
            'gateway_name' => 'QRIS Payment Gateway',
            'merchant_id' => 'MCH-001',
            'merchant_name' => 'CoffeePOS',
            'api_key' => 'sk-dummy-qris-key-001',
            'api_secret' => 'ss-dummy-qris-secret-001',
            'api_endpoint' => 'https://api.sandbox.qris.example.com/v1',
            'callback_url' => url('api/qris/callback'),
            'qr_merchant_id' => 'QR-MCH-001',
            'qr_merchant_key' => 'QR-KEY-001',
            'is_active' => true,
            'is_sandbox' => true,
        ]);

        PaymentMethod::create([
            'code' => 'cash',
            'name' => 'Cash',
            'icon' => 'heroicon-o-banknotes',
            'type' => 'manual',
            'min_amount' => 0,
            'fee_percentage' => 0,
            'fee_fixed' => 0,
            'instructions' => "1. Receive cash from customer\n2. Enter the amount received\n3. Complete the transaction",
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PaymentMethod::create([
            'code' => 'qris',
            'name' => 'QRIS',
            'icon' => 'heroicon-o-qr-code',
            'type' => 'gateway',
            'payment_gateway_config_id' => $qrisConfig->id,
            'min_amount' => 0,
            'fee_percentage' => 0,
            'fee_fixed' => 0,
            'instructions' => "1. Scan the QR code using your e-wallet app\n2. Confirm the payment\n3. Wait for payment confirmation",
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $dummyEwalletConfig = PaymentGatewayConfig::create([
            'gateway_code' => 'dummy_ewallet',
            'gateway_name' => 'E-Wallet (Dummy)',
            'merchant_id' => 'MCH-002',
            'merchant_name' => 'CoffeePOS',
            'api_key' => 'sk-dummy-ewallet-key-001',
            'api_secret' => 'ss-dummy-ewallet-secret-001',
            'api_endpoint' => 'https://api.sandbox.ewallet.example.com/v1',
            'is_active' => true,
            'is_sandbox' => true,
        ]);

        PaymentMethod::create([
            'code' => 'e-wallet',
            'name' => 'E-Wallet',
            'icon' => 'heroicon-o-device-phone-mobile',
            'type' => 'gateway',
            'payment_gateway_config_id' => $dummyEwalletConfig->id,
            'min_amount' => 0,
            'fee_percentage' => 1.5,
            'fee_fixed' => 0,
            'instructions' => "1. Select E-Wallet as payment method\n2. You will be redirected to the payment page\n3. Complete the payment in your e-wallet app",
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $dummyVaConfig = PaymentGatewayConfig::create([
            'gateway_code' => 'dummy_va',
            'gateway_name' => 'Virtual Account (Dummy)',
            'merchant_id' => 'MCH-003',
            'merchant_name' => 'CoffeePOS',
            'api_key' => 'sk-dummy-va-key-001',
            'api_secret' => 'ss-dummy-va-secret-001',
            'api_endpoint' => 'https://api.sandbox.va.example.com/v1',
            'is_active' => true,
            'is_sandbox' => true,
        ]);

        PaymentMethod::create([
            'code' => 'va',
            'name' => 'V.A.',
            'icon' => 'heroicon-o-building-library',
            'type' => 'gateway',
            'payment_gateway_config_id' => $dummyVaConfig->id,
            'min_amount' => 0,
            'fee_percentage' => 2.0,
            'fee_fixed' => 2500,
            'instructions' => "1. Select V.A. as payment method\n2. A virtual account number will be generated\n3. Transfer the amount to the provided V.A. number",
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $this->command->info('Payment gateway configs and payment methods created successfully!');
    }
}
