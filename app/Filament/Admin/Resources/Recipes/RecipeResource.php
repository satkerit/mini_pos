<?php

namespace App\Filament\Admin\Resources\Recipes;

use App\Filament\Admin\Resources\Recipes\Pages\CreateRecipe;
use App\Filament\Admin\Resources\Recipes\Pages\EditRecipe;
use App\Filament\Admin\Resources\Recipes\Pages\ListRecipes;
use App\Models\Recipe;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('Recipes');
    }

    public static function getModelLabel(): string
    {
        return __('Recipe');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Recipes');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('Product'))
                    ->relationship('product', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true),
                Repeater::make('details')
                    ->label(__('Recipe Details'))
                    ->relationship()
                    ->schema([
                        Select::make('ingredient_id')
                            ->label(__('Ingredient'))
                            ->relationship('ingredient', 'name')
                            ->required(),
                        TextInput::make('amount')
                            ->label(__('Amount'))
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('Product'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
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
            'index' => ListRecipes::route('/'),
            'create' => CreateRecipe::route('/create'),
            'edit' => EditRecipe::route('/{record}/edit'),
        ];
    }
}
