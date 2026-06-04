<?php

namespace App\Http\Controllers;

use App\Models\QrisTransaction;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeController extends Controller
{
    public function generate($transactionId)
    {
        $transaction = QrisTransaction::where('transaction_id', $transactionId)->firstOrFail();

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($transaction->qr_code)
            ->encoding(new Encoding('UTF-8'))
            ->size(400)
            ->margin(10)
            ->build();

        return response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
