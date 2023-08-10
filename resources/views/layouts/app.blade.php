<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        @filamentStyles
        @vite('resources/css/app.css')
    </head>

    <body class="antialiased bg-gray-100">
        <div class="max-w-7xl mx-auto">
            {{ $slot }}
        </div>

        @filamentScripts
        @vite('resources/js/app.js')
        @livewire('notifications')
    </body>
</html>
