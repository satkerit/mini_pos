<?php

namespace App\Filament\Admin\Resources\PaymentGatewayLogs;

use App\Filament\Admin\Resources\PaymentGatewayLogs\Pages\ListPaymentGatewayLogs;
use App\Models\PaymentGatewayLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentGatewayLogResource extends Resource
{
    protected static ?string $model = PaymentGatewayLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationLabel(): string
    {
        return __('Gateway Logs');
    }

    public static function getModelLabel(): string
    {
        return __('Gateway Log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Gateway Logs');
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
                TextColumn::make('config.gateway_code')->label(__('Gateway')),
                TextColumn::make('endpoint')->label(__('Endpoint'))->limit(40),
                TextColumn::make('method')->label(__('Method'))->badge(),
                TextColumn::make('response_code')->label(__('Response Code'))->badge()
                    ->color(fn($state): string => $state && $state < 400 ? 'success' : 'danger'),
                TextColumn::make('status')->label(__('Status'))->badge()
                    ->color(fn($state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
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
            'index' => ListPaymentGatewayLogs::route('/'),
        ];
    }
}
