<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#4f46e5">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-mesh flex flex-col items-center justify-center p-4 sm:p-6">
            <div class="w-full max-w-md animate-fade-in">
                <div class="text-center mb-8">
                    <a href="/" class="inline-flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-2xl shadow-lg shadow-indigo-200 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gradient">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>

                <div class="bg-white/80 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl shadow-slate-200/50 p-6 sm:p-8">
                    {{ $slot }}
                </div>

                <p class="text-center mt-6 text-xs text-slate-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}
                </p>
            </div>
        </div>
    </body>
</html>
