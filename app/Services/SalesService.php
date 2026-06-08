<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\CashShift;
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
            $saleStatus = $data['payment_method'] === 'cash' ? 'completed' : 'pending';

            $cashShiftId = CashShift::where('branch_id', $data['branch_id'])
                ->where('user_id', $data['user_id'])
                ->open()
                ->value('id');

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
                'status' => $saleStatus,
                'cash_shift_id' => $cashShiftId,
            ]);

            $productIds = array_column($data['items'], 'product_id');
            $productsWithRecipes = Product::with('recipe.details.ingredient')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $stockTransactions = [];
            $ingredientUpdates = [];

            foreach ($data['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                $product = $productsWithRecipes->get($item['product_id']);
                if ($product && $product->recipe) {
                    foreach ($product->recipe->details as $detail) {
                        $totalDeduction = $detail->amount * $item['quantity'];

                        $stockTransactions[] = [
                            'branch_id' => $data['branch_id'],
                            'ingredient_id' => $detail->ingredient_id,
                            'quantity' => -$totalDeduction,
                            'type' => 'out',
                            'reference_type' => 'Sale',
                            'reference_id' => $sale->id,
                            'notes' => "Sale deduction: {$product->name}",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $ingredientUpdates[$detail->ingredient_id] = ($ingredientUpdates[$detail->ingredient_id] ?? 0) - $totalDeduction;
                    }
                }
            }

            if (!empty($stockTransactions)) {
                \App\Models\StockTransaction::insert($stockTransactions);
            }

            foreach ($ingredientUpdates as $ingredientId => $adjustment) {
                \App\Models\Ingredient::where('id', $ingredientId)->increment('stock', $adjustment);
            }

            return $sale;
        });
    }

    protected function generateOrderNumber(int $branchId): string
    {
        $date = now()->format('Ymd');
        $prefix = "POS-{$branchId}-{$date}";

        $lastSale = Sale::where('order_number', 'like', "{$prefix}-%")
            ->orderByDesc('order_number')
            ->value('order_number');

        $nextNumber = 1;
        if ($lastSale && preg_match('/-(\d{4})$/', $lastSale, $m)) {
            $nextNumber = (int) $m[1] + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function cancelSale(Sale $sale): void
    {
        if ($sale->status === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($sale) {
            $sale->update(['status' => 'cancelled']);

            $sale->payments()->update(['status' => 'failed']);

            $stockTransactions = \App\Models\StockTransaction::where('reference_type', 'Sale')
                ->where('reference_id', $sale->id)
                ->where('type', 'out')
                ->get();

            foreach ($stockTransactions as $st) {
                \App\Models\Ingredient::where('id', $st->ingredient_id)->increment('stock', abs($st->quantity));
            }

            $stockTransactions->each->delete();
        });
    }

    public function completeSale(Sale $sale): void
    {
        $sale->update(['status' => 'completed']);
    }
}
