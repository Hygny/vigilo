<?php

declare(strict_types=1);

use App\Enums\RefreshStatus;

// Guarda de contrato: o filtro de status em Portfolios\Show casa por estes
// valores. Renomear um value do enum sem atualizar o filtro (e os dados no
// banco) deixaria a lista vazia com contador > 0, sem erro. Este teste quebra
// antes disso acontecer.
it('keeps the backing values the status filter and stored data rely on', function () {
    expect(RefreshStatus::Ok->value)->toBe('ok')
        ->and(RefreshStatus::NotFound->value)->toBe('nao_encontrado')
        ->and(RefreshStatus::Error->value)->toBe('erro');
});
