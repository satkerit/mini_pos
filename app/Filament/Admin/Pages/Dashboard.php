<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Models\Branch;

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

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('branch_id')
                    ->label(__('Filter Branch'))
                    ->options(Branch::pluck('name', 'id'))
                    ->placeholder(__('All Branches')),
            ])
            ->columns(3);
    }
}
