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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
                Select::make('ingredient_id')
                    ->relationship('ingredient', 'name')
                    ->required(),
                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->helperText('Use positive for stock in, negative for stock out'),
                Textarea::make('reason'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('branch.name'),
                TextColumn::make('ingredient.name'),
                TextColumn::make('quantity')->numeric(),
                TextColumn::make('user.name'),
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
