<?php

namespace App\Filament\Admin\Pages;

use App\Models\Branch;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getTitle(): string
    {
        return __('Dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->label(__('Filter Branch'))
                    ->options(Branch::pluck('name', 'id'))
                    ->placeholder(__('All Branches')),
            ])
            ->columns(3);
    }
}
