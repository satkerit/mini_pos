<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

class SalesService
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'branch_id' => $data['branch_id'],
                'user_id' => $data['user_id'],
                'customer_name' => $data['customer_name'] ?? null,
                'order_number' => $this->generateOrderNumber($data['branch_id']),
                'total_amount' => $data['total_amount'],
                'discount' => $data['discount'] ?? 0,
                'final_amount' => $data['final_amount'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'received_amount' => $data['received_amount'] ?? 0,
                'change_amount' => $data['change_amount'] ?? 0,
                'status' => 'completed',
            ]);

            foreach ($data['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Deduct stock based on recipe
                $this->deductStockFromRecipe($sale->branch_id, $item['product_id'], $item['quantity'], $sale->id);
            }

            return $sale;
        });
    }

    protected function deductStockFromRecipe(int $branchId, int $productId, int $quantity, int $saleId)
    {
        $product = Product::with('recipe.details')->find($productId);

        if ($product && $product->recipe) {
            foreach ($product->recipe->details as $detail) {
                $totalDeduction = $detail->amount * $quantity;
                $this->inventoryService->adjustStock(
                    $branchId,
                    $detail->ingredient_id,
                    -$totalDeduction,
                    'out',
                    'Sale',
                    $saleId,
                    "Sale item deduction for product: {$product->name}"
                );
            }
        }
    }

    protected function generateOrderNumber(int $branchId)
    {
        $date = now()->format('Ymd');
        $count = Sale::where('branch_id', $branchId)->whereDate('created_at', now())->count() + 1;
        return "POS-{$branchId}-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
