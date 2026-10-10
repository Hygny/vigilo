<?php

declare(strict_types=1);

use App\Http\Middleware\OsintAuditLog;
use App\Models\ApiAccessLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Garante que um listener de teste (ex.: o que força a falha de gravação) não
// vaze para outros testes.
afterEach(function (): void {
    ApiAccessLog::flushEventListeners();
});

it('records the audit trail even when the downstream throws', function () {
    $request = Request::create('/api/v1/vigilancia/empresas/por-socio', 'POST');

    $call = fn () => (new OsintAuditLog)->handle($request, function (): void {
        throw new RuntimeException('falha inesperada no controller');
    });

    expect($call)->toThrow(RuntimeException::class); // a exceção original propaga

    $this->assertDatabaseHas('api_access_logs', [
        'path' => 'api/v1/vigilancia/empresas/por-socio',
        'method' => 'POST',
        'status' => 500, // não-HttpException → 500
    ]);
});

it('records the HTTP status of a thrown HttpException', function () {
    $request = Request::create('/api/v1/vigilancia/cnpjs/123', 'GET');

    $call = fn () => (new OsintAuditLog)->handle($request, function (): void {
        abort(503, 'base indisponível');
    });

    expect($call)->toThrow(HttpException::class);

    $this->assertDatabaseHas('api_access_logs', [
        'path' => 'api/v1/vigilancia/cnpjs/123',
        'status' => 503,
    ]);
});

it('never lets a logging failure break the response', function () {
    // Simula o banco indisponível no momento de gravar a auditoria.
    ApiAccessLog::creating(function (): void {
        throw new RuntimeException('db indisponível ao gravar auditoria');
    });

    $request = Request::create('/api/v1/vigilancia/cnpjs/123', 'GET');
    $response = (new OsintAuditLog)->handle($request, fn () => response('ok', 200));

    // A resposta volta íntegra (não vira 500) e nada é propagado.
    expect($response->getStatusCode())->toBe(200)
        ->and(ApiAccessLog::query()->count())->toBe(0);
});
