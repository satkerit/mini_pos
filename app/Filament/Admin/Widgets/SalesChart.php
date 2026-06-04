<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

use Filament\Widgets\Concerns\InteractsWithPageFilters;

class SalesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Sales Overview';

    protected function getData(): array
    {
        $branchId = $this->filters['branch_id'] ?? null;

        $query = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(final_amount) as total'))
            ->where('created_at', '>=', now()->subDays(7));
            
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $data = $query->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sales (IDR)',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
