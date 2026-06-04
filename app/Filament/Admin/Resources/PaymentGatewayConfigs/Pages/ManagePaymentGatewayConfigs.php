<?php

namespace App\Filament\Admin\Resources\PaymentGatewayConfigs\Pages;

use App\Filament\Admin\Resources\PaymentGatewayConfigs\PaymentGatewayConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePaymentGatewayConfigs extends ManageRecords
{
    protected static string $resource = PaymentGatewayConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
