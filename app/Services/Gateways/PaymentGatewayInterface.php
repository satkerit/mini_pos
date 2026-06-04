<?php

namespace App\Services\Gateways;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function process(Payment $payment): array;
    public function checkStatus(Payment $payment): array;
    public function refund(Payment $payment, float $amount = null): array;
}