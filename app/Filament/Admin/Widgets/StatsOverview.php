<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Branch;

use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $branchId = $this->filters['branch_id'] ?? null;

        $saleQuery = Sale::whereDate('created_at', now());
        if ($branchId) {
            $saleQuery->where('branch_id', $branchId);
        }

        return [
            Stat::make('Total Sales Today', 'IDR ' . number_format($saleQuery->sum('final_amount'), 0, ',', '.')),
            Stat::make('Total Products', Product::count()),
            Stat::make('Active Branches', Branch::where('is_active', true)->count()),
        ];
    }
}
