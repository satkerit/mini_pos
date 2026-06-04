<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\QrisTransaction;
use Illuminate\Support\Str;

class PaymentService
{
    public function generateQris(int $paymentId, float $amount)
    {
        // Mock QRIS Generation
        $transactionId = 'TRX-' . Str::upper(Str::random(10));
        $referenceId = 'REF-' . Str::upper(Str::random(10));
        $qrCode = '00020101021126570011ID.CO.QRIS.WWW011893600523000000000102030010303UMI51440014ID.CO.QRIS.WWW0215ID10202100000010303UMI5204541153033605802ID59016POS%20SYSTEM6005CITY6105123456304ABCD';

        return QrisTransaction::create([
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'reference_id' => $referenceId,
            'qr_code' => $qrCode,
            'status' => 'pending',
        ]);
    }

    public function processPayment(int $saleId, float $amount, string $method)
    {
        $payment = Payment::create([
            'sale_id' => $saleId,
            'payment_method' => $method,
            'amount' => $amount,
            'status' => $method === 'cash' ? 'completed' : 'pending',
        ]);

        if ($method === 'qris') {
            $this->generateQris($payment->id, $amount);
        }

        return $payment;
    }
}
