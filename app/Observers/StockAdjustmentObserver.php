<?php

namespace App\Observers;

use App\Models\StockAdjustment;
use App\Services\InventoryService;

class StockAdjustmentObserver
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function created(StockAdjustment $stockAdjustment): void
    {
        $this->inventoryService->adjustStock(
            $stockAdjustment->branch_id,
            $stockAdjustment->ingredient_id,
            $stockAdjustment->quantity,
            'adjustment',
            'StockAdjustment',
            $stockAdjustment->id,
            $stockAdjustment->reason
        );
    }
}
