<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Google Fonts: Playfair Display + Inter --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Renova Locação') }}</title>
        </x-inertia::head>

        {{-- Tracking codes - HEAD position --}}
        @if(isset($page['props']['trackingCodes']['head']))
            @foreach($page['props']['trackingCodes']['head'] as $code)
                {!! $code['code'] !!}
            @endforeach
        @endif
    </head>
    <body class="font-sans antialiased">
        {{-- Tracking codes - BODY START position --}}
        @if(isset($page['props']['trackingCodes']['body_start']))
            @foreach($page['props']['trackingCodes']['body_start'] as $code)
                {!! $code['code'] !!}
            @endforeach
        @endif

        <x-inertia::app />

        {{-- Tracking codes - BODY END position --}}
        @if(isset($page['props']['trackingCodes']['body_end']))
            @foreach($page['props']['trackingCodes']['body_end'] as $code)
                {!! $code['code'] !!}
            @endforeach
        @endif
    </body>
</html>
