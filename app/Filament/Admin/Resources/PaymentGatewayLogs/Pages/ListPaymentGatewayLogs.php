<?php

namespace App\Filament\Admin\Resources\PaymentGatewayLogs\Pages;

use App\Filament\Admin\Resources\PaymentGatewayLogs\PaymentGatewayLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentGatewayLogs extends ListRecords
{
    protected static string $resource = PaymentGatewayLogResource::class;
}
