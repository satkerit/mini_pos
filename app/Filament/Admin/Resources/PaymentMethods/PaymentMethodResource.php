<?php

namespace App\Filament\Admin\Resources\PaymentMethods;

use App\Filament\Admin\Resources\PaymentMethods\Pages\ManagePaymentMethods;
use App\Models\PaymentMethod;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function getNavigationLabel(): string
    {
        return __('Payment Methods');
    }

    public static function getModelLabel(): string
    {
        return __('Payment Method');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Payment Methods');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Payments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('Code'))
                    ->required()
                    ->unique(PaymentMethod::class, 'code', ignoreRecord: true),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('icon')
                    ->label(__('Icon')),
                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'manual' => __('Manual'),
                        'gateway' => __('Gateway'),
                    ])
                    ->required(),
                Select::make('payment_gateway_config_id')
                    ->label(__('Payment Gateway Config'))
                    ->relationship('gatewayConfig', 'gateway_name')
                    ->nullable(),
                TextInput::make('min_amount')
                    ->label(__('Min Amount'))
                    ->numeric()
                    ->default(0),
                TextInput::make('max_amount')
                    ->label(__('Max Amount'))
                    ->numeric()
                    ->nullable(),
                TextInput::make('fee_percentage')
                    ->label(__('Fee (%)'))
                    ->numeric()
                    ->step(0.01)
                    ->default(0),
                TextInput::make('fee_fixed')
                    ->label(__('Fixed Fee'))
                    ->numeric()
                    ->default(0),
                Textarea::make('instructions')
                    ->label(__('Instructions')),
                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->sortable(),
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
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePaymentMethods::route('/'),
        ];
    }
}
