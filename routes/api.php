<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CnpjGraphController;
use App\Http\Controllers\Api\CnpjLookupController;
use Illuminate\Support\Facades\Route;

/*
| GET /api/cnpj/{cnpj}        — consulta cadastral de um CNPJ da carteira do token.
| GET /api/cnpj/{cnpj}/grafo  — grafo societário (Camada 1) de um CNPJ da carteira.
| Auth: Bearer (Sanctum). Rate limit: 'cnpj-api' (por dono do token).
| Contrato do consumidor: 14 dígitos sem máscara; não-2xx = "pendente".
*/
Route::middleware(['auth:sanctum', 'throttle:cnpj-api'])->group(function (): void {
    Route::get('/cnpj/{cnpj}', CnpjLookupController::class)->name('api.cnpj.show');
    Route::get('/cnpj/{cnpj}/grafo', CnpjGraphController::class)->name('api.cnpj.grafo');
});
