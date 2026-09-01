<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('portfolios.show', $company->portfolio_id) }}" wire:navigate class="text-sm text-slate-500 hover:underline">&larr; Voltar ao portfólio</a>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-semibold text-slate-800">{{ $latest?->razao_social ?? $company->label ?? 'Empresa monitorada' }}</h1>
                    @if ($company->label !== null && $latest?->razao_social !== null)
                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-sm font-medium text-slate-600 ring-1 ring-inset ring-slate-200" title="Seu rótulo para esta unidade">
                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            {{ $company->label }}
                        </span>
                    @endif
                </div>
                <p class="font-mono text-sm text-slate-500">{{ $company->formattedCnpj() }}</p>
            </div>
            <button wire:click="queueRefresh"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                Atualizar agora
            </button>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        {{-- Current cadastral data --}}
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="mb-4 text-base font-semibold text-slate-800">Dados atuais</h2>
            @if ($latest === null)
                <p class="text-sm text-slate-500">Ainda não há dados. Clique em “Atualizar agora” para buscar na Receita.</p>
            @else
                <dl class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2">
                    <div><dt class="text-xs uppercase text-slate-400">Situação cadastral</dt><dd class="text-sm text-slate-700">{{ $latest->situacao_cadastral }} @if ($latest->situacao_data)<span class="text-slate-400">({{ $latest->situacao_data->format('d/m/Y') }})</span>@endif</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Nome fantasia</dt><dd class="text-sm text-slate-700">{{ $latest->nome_fantasia ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">CNAE principal</dt><dd class="text-sm text-slate-700">{{ $latest->cnae_principal ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Porte</dt><dd class="text-sm text-slate-700">{{ $latest->porte ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Natureza jurídica</dt><dd class="text-sm text-slate-700">{{ $latest->natureza_juridica ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-slate-400">Endereço</dt><dd class="text-sm text-slate-700">{{ collect([$latest->logradouro, $latest->municipio, $latest->uf])->filter()->implode(' · ') ?: '—' }}</dd></div>
                </dl>

                @if ($latest->partners->isNotEmpty())
                    <div class="mt-5">
                        <h3 class="mb-2 text-xs font-semibold uppercase text-slate-400">Quadro societário</h3>
                        <ul class="divide-y divide-slate-100 rounded-md ring-1 ring-slate-100">
                            @foreach ($latest->partners as $partner)
                                <li class="flex items-center justify-between px-4 py-2 text-sm">
                                    <span class="text-slate-700">{{ $partner->nome }}</span>
                                    <span class="text-slate-400">{{ $partner->qualificacao ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif
        </div>

        {{-- Timeline of change events --}}
        <div class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="text-base font-semibold text-slate-800">Histórico de mudanças</h2></div>
            <ul role="list" class="divide-y divide-slate-100">
                @forelse ($events as $event)
                    <li class="flex items-start justify-between gap-4 px-5 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700">{{ $event->type->label() }}</p>
                            <p class="text-sm text-slate-500">
                                <span class="text-slate-400">{{ $event->old_value ?? '—' }}</span> &rarr; <span class="text-slate-600">{{ $event->new_value ?? '—' }}</span>
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $event->severity->badgeClasses() }}">{{ $event->severity->label() }}</span>
                            <span class="text-xs text-slate-400">{{ $event->detected_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Nenhuma mudança registrada. A primeira coleta serve de linha de base.</li>
                @endforelse
            </ul>
        </div>

        {{-- Snapshot history --}}
        <div class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="text-base font-semibold text-slate-800">Coletas ({{ $snapshots->count() }})</h2></div>
            <ul role="list" class="divide-y divide-slate-100">
                @foreach ($snapshots as $snapshot)
                    <li class="flex items-center justify-between px-5 py-2 text-sm">
                        <span class="text-slate-600">{{ $snapshot->situacao_cadastral }} — {{ $snapshot->razao_social }}</span>
                        <span class="text-xs text-slate-400">{{ $snapshot->captured_at->format('d/m/Y H:i') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
