<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use App\Models\QrisTransaction;
use App\Models\PaymentGatewayLog;
use Illuminate\Support\Str;

class QrisGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(?PaymentGatewayConfig $config = null)
    {
        $this->config = $config ?? PaymentGatewayConfig::where('gateway_code', 'qris')->active()->first();
    }

    public function process(Payment $payment): array
    {
        $transactionId = 'QRIS-' . strtoupper(Str::random(10));
        $referenceId = 'REF-' . strtoupper(Str::random(10));

        $nmid = $this->config?->qr_merchant_id ?? 'ID10202100001';
        $merchantName = $this->config?->merchant_name ?? 'CoffeePOS Merchant';

        $qrCode = sprintf(
            '00020101021126570011ID.CO.QRIS.WWW011893600523%015d%010d102010303UMI51440014ID.CO.QRIS.WWW0215%s010303UMI5204541153033605802ID5906%s6005CITY6105123456304%s',
            $payment->id,
            intval($payment->amount * 100),
            $nmid,
            $merchantName,
            strtoupper(Str::random(4))
        );

        QrisTransaction::create([
            'payment_id' => $payment->id,
            'transaction_id' => $transactionId,
            'reference_id' => $referenceId,
            'qr_code' => $qrCode,
            'status' => 'pending',
        ]);

        $this->logApiCall($payment, 'generate_qris', [
            'transaction_id' => $transactionId,
            'amount' => $payment->amount,
        ], ['qr_code' => $qrCode, 'status' => 'pending']);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'reference_id' => $referenceId,
            'qr_code' => $qrCode,
            'qr_url' => route('qris.image', $transactionId),
            'status' => 'pending',
            'payment' => $payment->load('qrisTransaction'),
        ];
    }

    public function checkStatus(Payment $payment): array
    {
        $qris = $payment->qrisTransaction;
        if (!$qris) {
            return ['success' => false, 'status' => 'not_found', 'message' => 'No QRIS transaction found'];
        }
        return [
            'success' => true,
            'status' => $qris->status,
            'transaction_id' => $qris->transaction_id,
            'reference_id' => $qris->reference_id,
            'message' => $qris->status === 'completed' ? 'Payment confirmed' : 'Awaiting payment',
        ];
    }

    public function refund(Payment $payment, float $amount = null): array
    {
        if ($qris = $payment->qrisTransaction) {
            $qris->update(['status' => 'refunded']);
        }
        $payment->update(['status' => 'refunded']);
        $this->logApiCall($payment, 'refund_qris', ['amount' => $amount ?? $payment->amount], ['status' => 'refunded']);
        return ['success' => true, 'status' => 'refunded', 'message' => 'Payment refunded'];
    }

    protected function logApiCall(Payment $payment, string $endpoint, array $request, array $response, int $code = 200): void
    {
        PaymentGatewayLog::create([
            'payment_gateway_config_id' => $this->config?->id,
            'payment_id' => $payment->id,
            'endpoint' => $endpoint,
            'method' => 'POST',
            'request_body' => json_encode($request),
            'response_body' => json_encode($response),
            'response_code' => $code,
            'status' => $code < 400 ? 'success' : 'failed',
        ]);
    }
}