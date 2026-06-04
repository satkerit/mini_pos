<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale');

        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } elseif (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        } elseif ($request->cookie('filament_language_switch_locale')) {
            $locale = $request->cookie('filament_language_switch_locale');
        }

        if (in_array($locale, ['en', 'id'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
