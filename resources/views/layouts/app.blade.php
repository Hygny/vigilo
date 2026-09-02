<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vigilo') }}</title>

        {{-- Aplica o tema salvo antes do render (evita flash) --}}
        <script>
            (function () {
                try {
                    var t = localStorage.getItem('vigilo-theme');
                    if (t === 'dark' || t === 'light') document.documentElement.setAttribute('data-theme', t);
                } catch (e) {}
            })();
        </script>

        {{-- Fontes: Geist (UI), Geist Mono (dados), Material Symbols (ícones) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300..800&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..0" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen">
            <livewire:layout.navigation />

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
