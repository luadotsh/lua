<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => (request()->cookie('appearance', 'system')) == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script>
            (function() {
                const appearance = '{{ request()->cookie('appearance', 'system') }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- No <title> here: @inertiaHead renders it, and with SSR on a
             static one would come through as a second tag. The default comes
             from the `title` callback in app.ts. --}}

        {{-- The .ico is a PNG in disguise, and Herd's nginx has an exact-match
             location for /favicon.ico that answers 404. Both are why the SVG and
             the PNG are listed: browsers prefer them and they load from an
             ordinary path. --}}
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon.png" type="image/png" sizes="278x278">
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/favicon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400..800&family=JetBrains+Mono:wght@400;500&family=Inter:wght@300..700&display=swap" rel="stylesheet">

        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
