<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Branch;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales Today', 'IDR ' . number_format(Sale::whereDate('created_at', now())->sum('final_amount'), 0, ',', '.')),
            Stat::make('Total Products', Product::count()),
            Stat::make('Active Branches', Branch::where('is_active', true)->count()),
        ];
    }
}
