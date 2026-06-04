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
                TextInput::make('actual_stock')
                    ->numeric()
                    ->required(),
                Textarea::make('notes'),
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
                TextColumn::make('system_stock')->numeric(),
                TextColumn::make('actual_stock')->numeric(),
                TextColumn::make('difference')->numeric(),
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
            'index' => ListStockOpnames::route('/'),
            'create' => CreateStockOpname::route('/create'),
            'edit' => EditStockOpname::route('/{record}/edit'),
        ];
    }
}
