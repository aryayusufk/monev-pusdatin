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

<body class="font-sans antialiased">
    <div class="flex md:hidden min-h-dvh">
        <div class="m-auto text-center p-6 text-slate-600">
            <p class="font-semibold">Tampilan mobile tidak didukung.</p>
            <p class="text-sm">Silakan gunakan tablet/laptop/desktop.</p>
        </div>
    </div>

    <div class="hidden md:flex h-dvh w-full overflow-hidden">
        @include('layouts.navigation')

        <div class="flex flex-col flex-1 min-w-0 min-h-0 overflow-hidden">
            <main class="flex-1 w-full min-h-0 overflow-hidden">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
