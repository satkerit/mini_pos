<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\SaleItem;
use App\Models\Branch;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\Product;
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
                Product::query()
                    ->select(
                        'products.id',
                        'products.name',
                        DB::raw('SUM(sale_items.quantity) as total_qty'),
                        DB::raw('SUM(sale_items.total_price) as total_revenue'),
                        DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'),
                        DB::raw('SUM(sale_items.total_price - (sale_items.quantity * products.cost_price)) as total_profit')
                    )
                    ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                    ->groupBy('products.id', 'products.name')
            )
            ->defaultSort('total_profit', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_qty')
                    ->label('Qty Sold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('total_profit')
                    ->label('Gross Profit')
                    ->money('IDR')
                    ->color('success')
                    ->weight('bold')
                    ->sortable(),
            ]);
    }
}
