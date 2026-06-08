<?php

use App\Http\Controllers\Api\PaymentGatewayController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->middleware('throttle:60,1')->group(function () {
    Route::get('/methods', [PaymentGatewayController::class, 'methods'])->name('api.payments.methods');
    Route::post('/process', [PaymentGatewayController::class, 'process'])->name('api.payments.process');
    Route::get('/{paymentId}/status', [PaymentGatewayController::class, 'status'])->name('api.payments.status');
    Route::post('/{paymentId}/refund', [PaymentGatewayController::class, 'refund'])->name('api.payments.refund');
});

Route::prefix('qris')->middleware('throttle:30,1')->group(function () {
    Route::post('/generate', [PaymentGatewayController::class, 'generateQris'])->name('api.qris.generate');
    Route::get('/{transactionId}/status', [PaymentGatewayController::class, 'qrisStatus'])->name('api.qris.status');
});

Route::post('/payments/callback', [PaymentGatewayController::class, 'callback'])->name('api.payments.callback');
