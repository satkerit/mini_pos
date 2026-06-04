<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function adjustStock(int $branchId, int $ingredientId, float $quantity, string $type, ?string $referenceType = null, ?int $referenceId = null, ?string $notes = null)
    {
        return DB::transaction(function () use ($branchId, $ingredientId, $quantity, $type, $referenceType, $referenceId, $notes) {
            $ingredient = Ingredient::findOrFail($ingredientId);

            // Create transaction record
            StockTransaction::create([
                'branch_id' => $branchId,
                'ingredient_id' => $ingredientId,
                'quantity' => $quantity,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);

            // Update ingredient stock
            if ($type === 'in' || ($type === 'adjustment' && $quantity > 0)) {
                $ingredient->increment('stock', abs($quantity));
            } elseif ($type === 'out' || ($type === 'adjustment' && $quantity < 0) || $type === 'opname') {
                // For opname, quantity passed should be the difference
                $ingredient->increment('stock', $quantity);
            }

            return $ingredient;
        });
    }

    public function processOpname(int $branchId, int $ingredientId, float $actualStock, int $userId, ?string $notes = null)
    {
        return DB::transaction(function () use ($branchId, $ingredientId, $actualStock, $userId, $notes) {
            $ingredient = Ingredient::findOrFail($ingredientId);
            $systemStock = $ingredient->stock;
            $difference = $actualStock - $systemStock;

            if ($difference != 0) {
                $this->adjustStock(
                    $branchId,
                    $ingredientId,
                    $difference,
                    'opname',
                    'StockOpname',
                    null, // Will update after opname record created
                    $notes
                );
            }

            return [
                'system_stock' => $systemStock,
                'actual_stock' => $actualStock,
                'difference' => $difference,
            ];
        });
    }
}
