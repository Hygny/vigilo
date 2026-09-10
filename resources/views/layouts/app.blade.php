<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vigilo') }}</title>

        {{-- Aplica o tema salvo antes do render (evita flash) e o reaplica após
             cada navegação SPA do Livewire: wire:navigate troca o DOM e o HTML
             novo vem sem data-theme, então reidratamos no evento livewire:navigated
             (que também dispara no load inicial). --}}
        <script>
            (function () {
                function applyTheme() {
                    try {
                        var t = localStorage.getItem('vigilo-theme');
                        document.documentElement.setAttribute('data-theme', t === 'dark' ? 'dark' : 'light');
                    } catch (e) {}
                }
                applyTheme();
                document.addEventListener('livewire:navigated', applyTheme);
            })();
        </script>

        {{-- Fontes: Geist (UI), Geist Mono (dados), Material Symbols (ícones) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300..800&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
        {{-- display=block: durante o carregamento a fonte fica invisível (não
             mostra o texto-fonte da ligatura, ex.: "folder_open") e troca pelo
             ícone assim que carrega. --}}
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..0&display=block" rel="stylesheet">

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
