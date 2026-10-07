<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CnpjGraphController;
use App\Http\Controllers\Api\CnpjLookupController;
use App\Http\Controllers\Api\Vigilancia\CnpjController as VigilanciaCnpjController;
use App\Http\Middleware\EnsureOsintAbility;
use App\Http\Middleware\OsintAuditLog;
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

/*
| API OSINT de vigilância (/api/v1/vigilancia) — LEITURA da base CNPJ inteira
| (sem escopo de carteira), para o workflow externo (n8n). Exige token com a
| ability `vigilancia:osint` (`vigilo:osint-token`) e audita cada chamada.
| Ordem dos middlewares: auth → throttle → auditoria → ability (o 403 por
| falta de permissão é auditado; o 429 de rate limit não polui o log).
*/
Route::middleware([
    'auth:sanctum',
    'throttle:vigilancia-api',
    OsintAuditLog::class,
    EnsureOsintAbility::class,
])->prefix('v1/vigilancia')->group(function (): void {
    Route::get('/cnpjs/{cnpj}', VigilanciaCnpjController::class)->name('api.vigilancia.cnpj');
});
