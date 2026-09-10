<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vigilo') }}</title>

        {{-- Aplica o tema salvo antes do render e reaplica após cada navegação SPA
             (ver comentário no layout app.blade.php). --}}
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

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300..800&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
        {{-- display=block: evita mostrar o texto-fonte da ligatura antes do ícone carregar. --}}
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..0&display=block" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="/" wire:navigate class="mb-7 flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-[12px] text-lg font-bold text-white shadow-card"
                      style="background:linear-gradient(135deg,var(--logo-a),var(--logo-b));">V</span>
                <span class="text-2xl font-semibold tracking-tight text-ink">Vigilo</span>
            </a>

            <div class="w-full overflow-hidden rounded-card border border-line bg-surface px-7 py-8 shadow-float sm:max-w-md">
                {{ $slot }}
            </div>

            <p class="mt-6 font-mono text-xs text-ink-muted">Due diligence contínua de CNPJ</p>
        </div>
    </body>
</html>
