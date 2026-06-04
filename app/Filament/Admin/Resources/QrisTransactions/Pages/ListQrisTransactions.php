<?php

namespace App\Filament\Admin\Resources\QrisTransactions\Pages;

use App\Filament\Admin\Resources\QrisTransactions\QrisTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListQrisTransactions extends ListRecords
{
    protected static string $resource = QrisTransactionResource::class;
}
