<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\PaymentGatewayLog;
use Illuminate\Support\Str;

class DummyGateway implements PaymentGatewayInterface
{
    public function process(Payment $payment): array
    {
        $referenceId = 'DUM-' . strtoupper(Str::random(12));

        PaymentGatewayLog::create([
            'payment_id' => $payment->id,
            'endpoint' => 'dummy/process',
            'method' => 'POST',
            'request_body' => json_encode(['payment_id' => $payment->id, 'amount' => $payment->amount]),
            'response_body' => json_encode(['reference_id' => $referenceId, 'status' => 'completed']),
            'response_code' => 200,
            'status' => 'success',
        ]);

        $payment->update(['status' => 'completed', 'reference_number' => $referenceId]);
        return ['success' => true, 'reference_id' => $referenceId, 'status' => 'completed', 'message' => 'Payment processed successfully'];
    }

    public function checkStatus(Payment $payment): array
    {
        return ['success' => true, 'status' => $payment->status, 'reference_id' => $payment->reference_number];
    }

    public function refund(Payment $payment, float $amount = null): array
    {
        $payment->update(['status' => 'refunded']);
        return ['success' => true, 'status' => 'refunded', 'message' => 'Payment refunded'];
    }
}