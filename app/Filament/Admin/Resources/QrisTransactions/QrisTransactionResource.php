<?php

namespace App\Filament\Admin\Resources\QrisTransactions;

use App\Filament\Admin\Resources\QrisTransactions\Pages\ListQrisTransactions;
use App\Models\QrisTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QrisTransactionResource extends Resource
{
    protected static ?string $model = QrisTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    public static function getNavigationLabel(): string
    {
        return __('QRIS Transactions');
    }

    public static function getModelLabel(): string
    {
        return __('QRIS Transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('QRIS Transactions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Payments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('payment.sale.order_number')->label(__('Order #'))->searchable(),
                TextColumn::make('transaction_id')->label(__('Transaction ID'))->searchable(),
                TextColumn::make('reference_id')->label(__('Reference ID')),
                TextColumn::make('status')->label(__('Status'))->badge()
                    ->color(fn($state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label(__('Date'))->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQrisTransactions::route('/'),
        ];
    }
}
