<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Models\StockOpname;
use App\Services\InventoryService;

class StockOpnameObserver
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function creating(StockOpname $stockOpname): void
    {
        $ingredient = Ingredient::find($stockOpname->ingredient_id);
        if ($ingredient) {
            $stockOpname->system_stock = $ingredient->stock;
            $stockOpname->difference = $stockOpname->actual_stock - $stockOpname->system_stock;
        }
    }

    public function created(StockOpname $stockOpname): void
    {
        if ($stockOpname->difference != 0) {
            $this->inventoryService->adjustStock(
                $stockOpname->branch_id,
                $stockOpname->ingredient_id,
                $stockOpname->difference,
                'opname',
                'StockOpname',
                $stockOpname->id,
                $stockOpname->notes
            );
        }
    }
}
