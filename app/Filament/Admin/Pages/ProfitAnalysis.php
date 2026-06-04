<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\SaleItem;
use App\Models\Branch;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;

class ProfitAnalysis extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected string $view = 'filament.admin.pages.profit-analysis';

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SaleItem::query()
                    ->select(
                        'product_id',
                        DB::raw('SUM(quantity) as total_qty'),
                        DB::raw('SUM(total_price) as total_revenue'),
                        DB::raw('SUM(quantity * products.cost_price) as total_cost'),
                        DB::raw('SUM(total_price - (quantity * products.cost_price)) as total_profit')
                    )
                    ->join('products', 'sale_items.product_id', '=', 'products.id')
                    ->groupBy('product_id')
            )
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_qty')
                    ->label('Qty Sold')
                    ->numeric(),
                TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('IDR'),
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('IDR'),
                TextColumn::make('total_profit')
                    ->label('Gross Profit')
                    ->money('IDR')
                    ->color('success')
                    ->weight('bold'),
            ]);
    }
}
