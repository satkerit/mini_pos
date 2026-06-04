<?php

namespace App\Filament\Admin\Resources\PaymentGatewayConfigs;

use App\Filament\Admin\Resources\PaymentGatewayConfigs\Pages\ManagePaymentGatewayConfigs;
use App\Models\PaymentGatewayConfig;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentGatewayConfigResource extends Resource
{
    protected static ?string $model = PaymentGatewayConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    public static function getNavigationLabel(): string
    {
        return __('Payment Gateway Config');
    }

    public static function getModelLabel(): string
    {
        return __('Payment Gateway Config');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Payment Gateway Configs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Payments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('gateway_code')
                    ->label(__('Gateway Code'))
                    ->required()
                    ->unique(PaymentGatewayConfig::class, 'gateway_code', ignoreRecord: true),
                TextInput::make('gateway_name')
                    ->label(__('Gateway Name'))
                    ->required(),
                TextInput::make('merchant_id')
                    ->label(__('Merchant ID')),
                TextInput::make('merchant_name')
                    ->label(__('Merchant Name')),
                TextInput::make('api_key')
                    ->label(__('API Key'))
                    ->password(),
                TextInput::make('api_secret')
                    ->label(__('API Secret'))
                    ->password(),
                TextInput::make('api_endpoint')
                    ->label(__('API Endpoint')),
                TextInput::make('callback_url')
                    ->label(__('Callback URL')),
                TextInput::make('qr_merchant_id')
                    ->label(__('QR Merchant ID')),
                Textarea::make('qr_merchant_key')
                    ->label(__('QR Merchant Key')),
                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(false),
                Toggle::make('is_sandbox')
                    ->label(__('Sandbox'))
                    ->default(true),
                KeyValue::make('extra_config')
                    ->label(__('Extra Config')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gateway_code')
                    ->label(__('Gateway Code'))
                    ->searchable(),
                TextColumn::make('gateway_name')
                    ->label(__('Gateway Name'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                IconColumn::make('is_sandbox')
                    ->label(__('Sandbox'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d/m/Y'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePaymentGatewayConfigs::route('/'),
        ];
    }
}
