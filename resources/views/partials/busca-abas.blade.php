{{-- Abas da busca: empresas × pessoas. Espera $ativa ('empresas'|'pessoas'). --}}
<nav class="inline-flex rounded-btn border border-line bg-surface p-0.5 text-[13px] font-medium shadow-card" aria-label="Tipo de busca">
    @foreach (['empresas' => ['empresas.busca', 'domain', 'Empresas'], 'pessoas' => ['pessoas.busca', 'person_search', 'Pessoas']] as $aba => [$rota, $icone, $rotulo])
        <a href="{{ route($rota) }}" wire:navigate @if ($ativa === $aba) aria-current="page" @endif
           @class([
               'inline-flex items-center gap-1.5 rounded-[8px] px-3 py-1.5 transition-colors',
               'bg-primary-soft text-accent' => $ativa === $aba,
               'text-ink-muted hover:text-ink-2' => $ativa !== $aba,
           ])>
            <x-ui.icon :name="$icone" :size="16" />{{ $rotulo }}
        </a>
    @endforeach
</nav>
