<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\QrisTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        $qris = $payment->qrisTransaction;
        if ($qris && $qris->status === 'pending') {
            $qris->update(['status' => 'completed']);
            $payment->update(['status' => 'completed']);
        }

        return response()->json([
            'status' => $payment->status,
            'message' => $payment->status === 'completed' ? 'Payment confirmed' : 'Awaiting payment',
        ]);
    }
}
