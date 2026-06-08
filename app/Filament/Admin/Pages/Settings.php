<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.admin.pages.settings';

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('Application Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    public function mount(): void
    {
        $this->form->fill(Setting::getMany([
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'company_logo',
            'tax_rate',
            'tax_name',
            'tax_inclusive',
            'receipt_header',
            'receipt_footer',
            'currency_symbol',
            'currency_code',
            'default_language',
        ]));
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('settings')
                    ->tabs([
                        Tabs\Tab::make(__('Company'))
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label(__('Company Name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('company_address')
                                    ->label(__('Company Address'))
                                    ->rows(3)
                                    ->maxLength(1000),
                                Forms\Components\TextInput::make('company_phone')
                                    ->label(__('Company Phone'))
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('company_email')
                                    ->label(__('Company Email'))
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('company_logo')
                                    ->label(__('Company Logo'))
                                    ->image()
                                    ->directory('company')
                                    ->maxSize(2048)
                                    ->disk('public'),
                            ])->columns(2),

                        Tabs\Tab::make(__('Tax'))
                            ->icon('heroicon-o-receipt-percent')
                            ->schema([
                                Forms\Components\TextInput::make('tax_name')
                                    ->label(__('Tax Name'))
                                    ->default('PPN')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label(__('Tax Rate (%)'))
                                    ->numeric()
                                    ->default(11)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.1),
                                Forms\Components\Toggle::make('tax_inclusive')
                                    ->label(__('Tax Inclusive (price includes tax)'))
                                    ->default(false),
                            ])->columns(2),

                        Tabs\Tab::make(__('Receipt'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Textarea::make('receipt_header')
                                    ->label(__('Receipt Header'))
                                    ->rows(3)
                                    ->placeholder(__('Text shown at the top of the receipt'))
                                    ->maxLength(500),
                                Forms\Components\Textarea::make('receipt_footer')
                                    ->label(__('Receipt Footer'))
                                    ->rows(3)
                                    ->placeholder(__('Text shown at the bottom of the receipt'))
                                    ->maxLength(500),
                            ])->columns(1),

                        Tabs\Tab::make(__('General'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Select::make('currency_code')
                                    ->label(__('Currency'))
                                    ->options([
                                        'IDR' => 'IDR - Rupiah Indonesia',
                                        'USD' => 'USD - US Dollar',
                                        'MYR' => 'MYR - Malaysian Ringgit',
                                        'SGD' => 'SGD - Singapore Dollar',
                                    ])
                                    ->default('IDR')
                                    ->required(),
                                Forms\Components\TextInput::make('currency_symbol')
                                    ->label(__('Currency Symbol'))
                                    ->default('Rp')
                                    ->maxLength(10)
                                    ->required(),
                                Forms\Components\Select::make('default_language')
                                    ->label(__('Default Language'))
                                    ->options([
                                        'id' => 'Bahasa Indonesia',
                                        'en' => 'English',
                                    ])
                                    ->default('id')
                                    ->required(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setMany($data);

        Notification::make()
            ->title(__('Settings saved successfully'))
            ->success()
            ->send();
    }
}
