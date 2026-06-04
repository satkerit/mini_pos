<?php

namespace App\Filament\Admin\Resources\Ingredients;

use App\Filament\Admin\Resources\Ingredients\Pages\ManageIngredients;
use App\Models\Ingredient;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('Ingredients');
    }

    public static function getModelLabel(): string
    {
        return __('Ingredient');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Ingredients');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('unit')
                    ->label(__('Unit'))
                    ->required(),
                TextInput::make('stock')
                    ->label(__('Stock'))
                    ->numeric()->default(0),
                TextInput::make('min_stock')
                    ->label(__('Min Stock'))
                    ->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('unit')
                    ->label(__('Unit')),
                TextColumn::make('stock')
                    ->label(__('Stock'))
                    ->numeric(),
                TextColumn::make('min_stock')
                    ->label(__('Min Stock'))
                    ->numeric(),
            ])
            ->filters([
                //
            ])
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
            'index' => ManageIngredients::route('/'),
        ];
    }
}
