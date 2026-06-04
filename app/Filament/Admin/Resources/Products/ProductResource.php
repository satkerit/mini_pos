<?php

namespace App\Filament\Admin\Resources\Products;

use App\Filament\Admin\Resources\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('sku')
                    ->label(__('SKU'))
                    ->required()->unique(ignoreRecord: true),
                TextInput::make('barcode')
                    ->label(__('Barcode'))
                    ->unique(ignoreRecord: true),
                TextInput::make('price')
                    ->label(__('Price'))
                    ->numeric()->prefix('IDR')->required(),
                TextInput::make('cost_price')
                    ->label(__('Cost Price'))
                    ->numeric()->prefix('IDR')->required(),
                FileUpload::make('image')
                    ->label(__('Image'))
                    ->image()->directory('products'),
                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Image')),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label(__('Category')),
                TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('IDR'),
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
