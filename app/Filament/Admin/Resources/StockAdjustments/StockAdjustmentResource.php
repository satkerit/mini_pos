<?php

namespace App\Filament\Admin\Resources\StockAdjustments;

use App\Filament\Admin\Resources\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Admin\Resources\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\Admin\Resources\StockAdjustments\Pages\ListStockAdjustments;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('Stock Adjustments');
    }

    public static function getModelLabel(): string
    {
        return __('Stock Adjustment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Stock Adjustments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->label(__('Branch'))
                    ->relationship('branch', 'name')
                    ->required(),
                Select::make('ingredient_id')
                    ->label(__('Ingredient'))
                    ->relationship('ingredient', 'name')
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->required()
                    ->helperText(__('Use positive for stock in, negative for stock out')),
                Textarea::make('reason')
                    ->label(__('Reason')),
                Select::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime()->sortable(),
                TextColumn::make('branch.name')
                    ->label(__('Branch')),
                TextColumn::make('ingredient.name')
                    ->label(__('Ingredient')),
                TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric(),
                TextColumn::make('user.name')
                    ->label(__('User')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
        ];
    }
}
