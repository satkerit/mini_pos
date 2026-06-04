<?php

namespace App\Filament\Admin\Resources\Products\Pages;

use App\Filament\Admin\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('print_barcodes')
                ->label(__('Print Barcodes'))
                ->icon('heroicon-o-printer')
                ->url(route('admin.barcodes.print'))
                ->openUrlInNewTab(),
        ];
    }
}
