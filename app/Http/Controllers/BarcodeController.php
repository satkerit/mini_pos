<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeController extends Controller
{
    public function generate($productId)
    {
        $product = Product::findOrFail($productId);
        $barcodeValue = $product->barcode ?: $product->sku;

        $generator = new BarcodeGeneratorPNG();
        $image = $generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128, 2, 60);

        return response($image, 200, ['Content-Type' => 'image/png']);
    }
}
