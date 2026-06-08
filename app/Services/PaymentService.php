<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Gateways\PaymentGatewayInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected array $gateways = [];

    public function processPayment(int $saleId, float $amount, string $method, ?int $paymentMethodId = null): Payment
    {
        $payment = Payment::create([
            'sale_id' => $saleId,
            'payment_method_id' => $paymentMethodId,
            'payment_method' => $method,
            'amount' => $amount,
            'status' => in_array($method, ['cash']) ? 'completed' : 'pending',
        ]);

        if ($method !== 'cash') {
            $gateway = $this->resolveGateway($method);
            if ($gateway) {
                $gateway->process($payment);
            }
        }

        return $payment->fresh()->load('qrisTransaction');
    }

    public function checkPaymentStatus(Payment $payment): array
    {
        if ($payment->payment_method === 'cash') {
            return ['success' => true, 'status' => $payment->status, 'message' => 'Cash payment completed'];
        }

        $gateway = $this->resolveGateway($payment->payment_method);
        if (!$gateway) {
            return ['success' => false, 'status' => 'error', 'message' => 'No gateway found'];
        }

        $qris = $payment->relationLoaded('qrisTransaction') ? $payment->qrisTransaction : $payment->load('qrisTransaction')->qrisTransaction;
        if ($qris && $qris->status === 'pending') {
            $qris->update(['status' => 'completed']);
            $payment->update(['status' => 'completed']);
        }

        return $gateway->checkStatus($payment);
    }

    public function refundPayment(Payment $payment, ?float $amount = null): array
    {
        $gateway = $this->resolveGateway($payment->payment_method);
        if (!$gateway) {
            $payment->update(['status' => 'refunded']);
            return ['success' => true, 'status' => 'refunded'];
        }
        $result = $gateway->refund($payment, $amount);
        $payment->update(['status' => 'refunded']);
        return $result;
    }

    public function resolveGateway(string $method): ?PaymentGatewayInterface
    {
        $gatewayMap = ['qris' => 'qris', 'e-wallet' => 'dummy', 'va' => 'dummy', 'bank_transfer' => 'dummy'];
        $gatewayCode = $gatewayMap[$method] ?? 'dummy';

        if (isset($this->gateways[$gatewayCode])) {
            return $this->gateways[$gatewayCode];
        }

        try {
            $gatewayClass = config("payment.gateways.{$gatewayCode}.class");
            if ($gatewayClass && class_exists($gatewayClass)) {
                $this->gateways[$gatewayCode] = App::make($gatewayClass);
                return $this->gateways[$gatewayCode];
            }
        } catch (\Exception $e) {
            Log::error("Failed to resolve gateway: {$gatewayCode}", ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function getAvailableMethods(): array
    {
        return Cache::remember('payment_methods_active', 3600, function () {
            return PaymentMethod::active()->get()->toArray();
        });
    }

    public function calculateFee(float $amount, PaymentMethod $method): float
    {
        return $amount * ($method->fee_percentage / 100) + $method->fee_fixed;
    }
}