{{-- PGFN fica fora daqui (mostrarPgfn = false): o perfil tem card próprio de dívida ativa. --}}
<div>
    <x-ui.card class="p-[22px]">
        @if ($assessment === null)
            <div class="mb-1 flex items-center gap-2">
                <x-ui.icon name="gavel" :size="20" class="text-accent" />
                <h2 class="text-[15px] font-semibold text-ink">Risco</h2>
            </div>
            <p class="text-sm text-ink-muted">Avaliação de risco indisponível no momento.</p>
            <div class="mt-5 border-t border-line pt-4">
                @include('partials.risco-ao-redor', ['aoRedor' => $aoRedor])
            </div>
        @else
            @include('partials.risco-resumo', [
                'report' => $assessment->report,
                'nivel' => $assessment->nivel,
                'motivo' => $assessment->motivo,
                'listaSuja' => $assessment->listaSuja,
                'pgfn' => $assessment->pgfn,
                'pgfnDisponivel' => $assessment->pgfnDisponivel,
                'mostrarPgfn' => false,
                'copiaLocal' => $copiaLocal,
                'comAoRedor' => true,
                'aoRedor' => $aoRedor,
            ])
        @endif
    </x-ui.card>
</div>
