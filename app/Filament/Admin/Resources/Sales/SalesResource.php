<?php

namespace App\Filament\Admin\Resources\Sales;

use App\Filament\Admin\Resources\Sales\Pages\ListSales;
use App\Models\Sale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class SalesResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    public static function getNavigationLabel(): string
    {
        return __('Sales History');
    }

    public static function getModelLabel(): string
    {
        return __('Sale');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Sales');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['items.product', 'payments', 'branch', 'user']))
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('Order #'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label(__('Branch'))
                    ->badge()
                    ->color('info'),

                TextColumn::make('user.name')
                    ->label(__('Cashier'))
                    ->searchable(),

                TextColumn::make('customer_name')
                    ->label(__('Customer'))
                    ->default('-')
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label(__('Items'))
                    ->counts('items')
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label(__('Subtotal'))
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('discount')
                    ->label(__('Discount'))
                    ->money('IDR')
                    ->sortable()
                    ->color(fn($state) => $state > 0 ? 'danger' : null),

                TextColumn::make('final_amount')
                    ->label(__('Total'))
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('payment_method')
                    ->label(__('Payment'))
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'cash' => 'success',
                        'qris' => 'info',
                        'e-wallet' => 'warning',
                        'va' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => strtoupper($state)),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label(__('Date Range'))
                    ->schema([
                        DatePicker::make('date_from')
                            ->label(__('From'))
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('date_to')
                            ->label(__('To'))
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['date_to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                SelectFilter::make('branch_id')
                    ->label(__('Branch'))
                    ->relationship('branch', 'name')
                    ->preload()
                    ->searchable(),

                SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options([
                        'cash' => __('Cash'),
                        'qris' => 'QRIS',
                        'e-wallet' => __('E-Wallet'),
                        'va' => 'Virtual Account',
                    ]),

                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'completed' => __('Completed'),
                        'pending' => __('Pending'),
                        'cancelled' => __('Cancelled'),
                    ]),

                Filter::make('today')
                    ->label(__('Today'))
                    ->query(fn(Builder $query) => $query->whereDate('created_at', now()->toDateString()))
                    ->toggle(),

                Filter::make('this_week')
                    ->label(__('This Week'))
                    ->query(fn(Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),

                Filter::make('this_month')
                    ->label(__('This Month'))
                    ->query(fn(Builder $query) => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
                    ->toggle(),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
        ];
    }
}
