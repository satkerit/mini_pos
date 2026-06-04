<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\QrisTransaction;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function methods(): JsonResponse
    {
        $methods = PaymentMethod::active()->get()->map(function ($method) {
            return [
                'id' => $method->id,
                'code' => $method->code,
                'name' => $method->name,
                'type' => $method->type,
                'icon' => $method->icon,
                'min_amount' => (float) $method->min_amount,
                'max_amount' => (float) $method->max_amount,
                'fee_percentage' => (float) $method->fee_percentage,
                'fee_fixed' => (float) $method->fee_fixed,
                'instructions' => $method->instructions,
            ];
        });

        return response()->json(['success' => true, 'data' => $methods]);
    }

    public function process(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'payment_method' => 'required|string|in:cash,qris,e-wallet,va,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $payment = $this->paymentService->processPayment(
                $request->sale_id, $request->amount, $request->payment_method, $request->payment_method_id
            );
            return response()->json(['success' => true, 'message' => 'Payment processed', 'data' => $payment->load(['qrisTransaction', 'paymentMethod'])]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Payment processing failed: ' . $e->getMessage()], 500);
        }
    }

    public function status($paymentId): JsonResponse
    {
        $payment = Payment::with('qrisTransaction', 'paymentMethod')->find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }
        $result = $this->paymentService->checkPaymentStatus($payment);
        return response()->json(array_merge(['success' => true, 'payment_id' => $payment->id, 'amount' => $payment->amount, 'method' => $payment->payment_method], $result));
    }

    public function refund(Request $request, $paymentId): JsonResponse
    {
        $payment = Payment::find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }
        return response()->json($this->paymentService->refundPayment($payment, $request->amount));
    }

    public function generateQris(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $payment = $this->paymentService->processPayment($request->sale_id, $request->amount, 'qris');
            return response()->json([
                'success' => true, 'message' => 'QRIS generated',
                'data' => [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->qrisTransaction?->transaction_id,
                    'reference_id' => $payment->qrisTransaction?->reference_id,
                    'qr_code' => $payment->qrisTransaction?->qr_code,
                    'qr_url' => $payment->qrisTransaction ? route('qris.image', $payment->qrisTransaction->transaction_id) : null,
                    'amount' => $payment->amount, 'status' => $payment->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'QRIS generation failed: ' . $e->getMessage()], 500);
        }
    }

    public function qrisStatus($transactionId): JsonResponse
    {
        $qris = QrisTransaction::where('transaction_id', $transactionId)->first();
        if (!$qris) {
            return response()->json(['success' => false, 'message' => 'QRIS transaction not found'], 404);
        }
        $result = $this->paymentService->checkPaymentStatus($qris->payment);
        return response()->json(array_merge(['success' => true, 'transaction_id' => $qris->transaction_id, 'reference_id' => $qris->reference_id, 'amount' => $qris->payment->amount], $result));
    }

    public function callback(Request $request): JsonResponse
    {
        $paymentId = $request->payment_id;
        $status = $request->status ?? 'completed';

        if (!$paymentId) {
            return response()->json(['success' => false, 'message' => 'Invalid callback data'], 400);
        }

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $payment->update(['status' => $status]);
        if ($payment->qrisTransaction) {
            $payment->qrisTransaction->update(['status' => $status]);
        }

        return response()->json(['success' => true, 'message' => 'Callback processed']);
    }
}