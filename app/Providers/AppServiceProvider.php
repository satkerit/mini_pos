<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\StockAdjustment;
use App\Models\StockOpname;
use App\Observers\StockAdjustmentObserver;
use App\Observers\StockOpnameObserver;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        StockAdjustment::observe(StockAdjustmentObserver::class);
        StockOpname::observe(StockOpnameObserver::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'id'])
                ->labels([
                    'en' => 'English',
                    'id' => 'Indonesia',
                ])
                ->circular();
        });
    }
}
