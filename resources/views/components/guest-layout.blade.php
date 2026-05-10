<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'fujii') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-950">
    <div class="min-h-screen flex flex-col items-center justify-center pt-6 sm:pt-0">
        <div class="mb-6">
            <a href="/" class="text-3xl font-extrabold text-white tracking-tight">fujii</a>
        </div>
        <div class="w-full sm:max-w-md px-8 py-8 bg-gray-900 shadow-md rounded-2xl border border-gray-800">
            {{ $slot }}
        </div>
    </div>
</body>
</html>