<?php

namespace App\Filament\Admin\Resources\StockOpnames;

use App\Filament\Admin\Resources\StockOpnames\Pages\CreateStockOpname;
use App\Filament\Admin\Resources\StockOpnames\Pages\EditStockOpname;
use App\Filament\Admin\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Models\StockOpname;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('Stock Opnames');
    }

    public static function getModelLabel(): string
    {
        return __('Stock Opname');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Stock Opnames');
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
                TextInput::make('actual_stock')
                    ->label(__('Actual Stock'))
                    ->numeric()
                    ->required(),
                Textarea::make('notes')
                    ->label(__('Notes')),
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
            ->modifyQueryUsing(fn($query) => $query->with(['branch', 'ingredient', 'user']))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime()->sortable(),
                TextColumn::make('branch.name')
                    ->label(__('Branch')),
                TextColumn::make('ingredient.name')
                    ->label(__('Ingredient')),
                TextColumn::make('system_stock')
                    ->label(__('System Stock'))
                    ->numeric(),
                TextColumn::make('actual_stock')
                    ->label(__('Actual Stock'))
                    ->numeric(),
                TextColumn::make('difference')
                    ->label(__('Difference'))
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
            'index' => ListStockOpnames::route('/'),
            'create' => CreateStockOpname::route('/create'),
            'edit' => EditStockOpname::route('/{record}/edit'),
        ];
    }
}
