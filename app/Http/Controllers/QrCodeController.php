<?php

namespace App\Http\Controllers;

use App\Models\QrisTransaction;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Cache;

class QrCodeController extends Controller
{
    public function generate($transactionId)
    {
        $cacheKey = "qris_image_{$transactionId}";

        $png = Cache::remember($cacheKey, 3600, function () use ($transactionId) {
            $transaction = QrisTransaction::where('transaction_id', $transactionId)->firstOrFail();

            return Builder::create()
                ->writer(new PngWriter())
                ->data($transaction->qr_code)
                ->encoding(new Encoding('UTF-8'))
                ->size(400)
                ->margin(10)
                ->build()
                ->getString();
        });

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
