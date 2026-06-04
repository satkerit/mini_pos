<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\SaleItem;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class KitchenDisplay extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected string $view = 'filament.admin.pages.kitchen-display';

    protected static ?string $navigationLabel = 'Kitchen / Barista';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SaleItem::query()
                    ->where('is_prepared', false)
                    ->whereHas('sale', fn($q) => $q->where('status', 'completed'))
                    ->latest()
            )
            ->columns([
                TextColumn::make('sale.order_number')
                    ->label('Order #')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Item')
                    ->weight('bold'),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->badge(),
                TextColumn::make('sale.created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Action::make('complete')
                    ->label('Done')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(function (SaleItem $record) {
                        $record->update(['is_prepared' => true]);

                        Notification::make()
                            ->title('Item marked as done')
                            ->success()
                            ->send();
                    }),
            ])
            ->poll('5s');
    }
}
