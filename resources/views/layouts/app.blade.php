<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased min-h-[100dvh] bg-bg-base-secondary overflow-x-hidden">
    <div class="md:hidden min-h-[100dvh] flex items-center justify-center p-6">
        <div class="flex gap-6 p-6 bg-bg-base-default border border-bor-base-default rounded-lg text-center max-w-sm">
            <p class="font-semibold text-txt-base-default">Tampilan mobile belum didukung.</p>
            <p class="text-sm text-txt-base-secondary">
                Silakan gunakan tablet, laptop, atau desktop.
            </p>
        </div>
    </div>

    <div class="hidden md:flex min-h-[100dvh] w-full">
        @include('layouts.navigation')

        <div class="flex flex-col flex-1 min-w-0">
            <main class="flex-1 w-full">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
