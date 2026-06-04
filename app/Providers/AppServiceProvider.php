<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\StockAdjustment;
use App\Models\StockOpname;
use App\Observers\StockAdjustmentObserver;
use App\Observers\StockOpnameObserver;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Stringable;
use Illuminate\Support\Str;

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

        // Fix for filament-language-switch compatibility with older Laravel versions or specific configurations
        if (! Stringable::hasMacro('doesntContain')) {
            Stringable::macro('doesntContain', function ($needles) {
                return ! Str::contains($this->value, $needles);
            });
        }

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
