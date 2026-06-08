<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Resources\Sales\SalesResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SalesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All'))
                ->icon('heroicon-o-receipt-percent'),

            'today' => Tab::make(__('Today'))
                ->icon('heroicon-o-calendar')
                ->badge(fn() => static::getModel()::whereDate('created_at', now()->toDateString())->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', now()->toDateString())),

            'completed' => Tab::make(__('Completed'))
                ->icon('heroicon-o-check-circle')
                ->badge(fn() => static::getModel()::where('status', 'completed')->whereDate('created_at', now()->toDateString())->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed')),

            'pending' => Tab::make(__('Pending'))
                ->icon('heroicon-o-clock')
                ->badge(fn() => static::getModel()::where('status', 'pending')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),

            'cancelled' => Tab::make(__('Cancelled'))
                ->icon('heroicon-o-x-circle')
                ->badge(fn() => static::getModel()::where('status', 'cancelled')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
