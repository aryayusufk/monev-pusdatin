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

<body class="font-sans text-txt-base-default antialiased">
    <div class="min-h-screen flex flex-col gap-6 justify-center items-center bg-bg-base-secondary">
        <div class="flex flex-col items-center justify-center text-center">
            <h1 class="text-xl font-bold text-txt-base-default">Laporan Kegiatan</h1>
            <h1 class="text-2xl font-bold text-acc-base-default">PUSDATIN KEMLU RI</h1>
        </div>
        <div
            class="w-full flex flex-col items-center justify-center sm:max-w-xs p-6 gap-6 bg-bg-base-default shadow-md overflow-hidden rounded-lg">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-txt-base-secondary" />
            </a>
            {{ $slot }}
        </div>

        @php
            $enabledRaw = \App\Models\Setting::where('key', 'ui.login_running_text.enabled')->value('value');
            $contentRaw = \App\Models\Setting::where('key', 'ui.login_running_text.content')->value('value');

            $enabled = filter_var(
                is_string($enabledRaw) ? $enabledRaw : json_encode($enabledRaw),
                FILTER_VALIDATE_BOOLEAN
            );

            $content = is_string($contentRaw) ? trim($contentRaw, '"') : trim((string) $contentRaw, '"');
        @endphp

        @if ($enabled && $content !== '')
            <div class="w-full sm:max-w-xs overflow-hidden bg-acc-base-default text-white rounded">
                <div class="marquee-track">
                    <span class="marquee-item font-extrabold text-lg tracking-wide">
                        {{ $content }}
                    </span>
                    <span class="marquee-item font-extrabold text-lg tracking-wide" aria-hidden="true">
                        {{ $content }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
