<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Portfólios</h1>
            <p class="text-sm text-slate-500">Agrupe os CNPJs que você monitora (clientes, fornecedores, franqueados).</p>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="create" class="flex flex-col gap-3 rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="name" class="block text-sm font-medium text-slate-700">Novo portfólio</label>
                <input wire:model="name" id="name" type="text" placeholder="Ex.: Rede Yázigi"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit"
                    class="inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                Criar
            </button>
        </form>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <ul role="list" class="divide-y divide-slate-100">
                @forelse ($portfolios as $portfolio)
                    <li class="flex items-center justify-between gap-4 px-5 py-4">
                        @if ($editingId === $portfolio->id)
                            <div class="flex flex-1 items-center gap-2">
                                <input wire:model="editingName" type="text"
                                       class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <button wire:click="saveEdit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">Salvar</button>
                                <button wire:click="cancelEdit" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancelar</button>
                            </div>
                            @error('editingName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        @else
                            <div class="min-w-0">
                                <a href="{{ route('portfolios.show', $portfolio) }}" wire:navigate
                                   class="text-base font-medium text-indigo-700 hover:underline">{{ $portfolio->name }}</a>
                                <p class="text-sm text-slate-500">{{ $portfolio->monitored_companies_count }} empresa(s)</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2 text-sm">
                                <button wire:click="queueRefresh({{ $portfolio->id }})"
                                        class="rounded-md px-3 py-1.5 font-medium text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50">
                                    Atualizar
                                </button>
                                <button wire:click="startEdit({{ $portfolio->id }})"
                                        class="rounded-md px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-100">Renomear</button>
                                <button wire:click="delete({{ $portfolio->id }})"
                                        wire:confirm="Remover este portfólio e todas as empresas nele?"
                                        class="rounded-md px-3 py-1.5 font-medium text-red-600 hover:bg-red-50">Excluir</button>
                            </div>
                        @endif
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Nenhum portfólio ainda. Crie o primeiro acima.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
