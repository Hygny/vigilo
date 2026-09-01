<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CnpjLookupController;
use Illuminate\Support\Facades\Route;

/*
| GET /api/cnpj/{cnpj} — consulta cadastral de um CNPJ da carteira do token.
| Auth: Bearer (Sanctum). Rate limit: 'cnpj-api' (por dono do token).
| Contrato do consumidor: 14 dígitos sem máscara; não-2xx = "pendente".
*/
Route::middleware(['auth:sanctum', 'throttle:cnpj-api'])
    ->get('/cnpj/{cnpj}', CnpjLookupController::class)
    ->name('api.cnpj.show');
