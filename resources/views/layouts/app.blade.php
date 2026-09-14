<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vigilo') }}</title>

        @include('partials.theme-bootstrap')

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
            @if (session('impersonator_id'))
                <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 bg-hl px-4 py-2 text-center text-[13px] font-medium text-hl-ink">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="msym" style="font-size:17px;" aria-hidden="true">visibility</span>
                        Você está personificando <strong>{{ auth()->user()->name }}</strong>.
                    </span>
                    <form method="POST" action="{{ route('impersonate.stop') }}">
                        @csrf
                        <button type="submit" class="font-semibold underline underline-offset-2 hover:opacity-80">Voltar ao admin</button>
                    </form>
                </div>
            @endif

            <livewire:layout.navigation />

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
