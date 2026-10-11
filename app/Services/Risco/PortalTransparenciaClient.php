<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\Enums\FonteSancao;
use App\Services\Risco\Exceptions\PortalUnavailable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP fino da API de Dados do Portal da Transparência (CGU). Cuida só
 * do transporte: autentica com o header `chave-api-dados`, busca a 1ª página de
 * um cadastro de sanção filtrando pelo CNPJ e devolve os registros crus. A
 * regra (revalidar o CNPJ, mapear, cachear) fica no {@see RiscoService}.
 *
 * @see https://portaldatransparencia.gov.br/api-de-dados
 */
final class PortalTransparenciaClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey,
        private readonly int $timeout,
    ) {}

    /**
     * Sem a chave de API a integração fica inativa — a UI degrada com elegância.
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== null && $this->apiKey !== '';
    }

    /**
     * Página 1 de um cadastro de sanção (CEIS/CNEP/CEPIM) filtrando pelo CNPJ.
     *
     * O filtro por sancionado da API é NOTORIAMENTE pouco confiável (há relatos
     * de que ela ignora o parâmetro e devolve a primeira página do cadastro
     * inteiro). Por isso o {@see RiscoService} SEMPRE revalida cada registro pelo
     * CNPJ — este cliente só transporta.
     *
     * @return list<array<string, mixed>>
     *
     * @throws PortalUnavailable
     */
    public function cadastro(FonteSancao $fonte, string $cnpjDigits): array
    {
        try {
            $response = $this->request()->get('/'.$fonte->endpoint(), [
                $fonte->parametroSancionado() => $cnpjDigits,
                'pagina' => 1,
            ]);
        } catch (ConnectionException $e) {
            throw PortalUnavailable::unreachable($fonte, $e->getMessage());
        }

        if ($response->failed()) {
            throw PortalUnavailable::requestFailed($fonte, $response->status());
        }

        $decoded = $response->json();

        if (! is_array($decoded)) {
            return [];
        }

        /** @var list<array<string, mixed>> $records */
        $records = [];

        foreach ($decoded as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $records[] = $item;
            }
        }

        return $records;
    }

    private function request(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw PortalUnavailable::notConfigured();
        }

        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->withHeaders(['chave-api-dados' => (string) $this->apiKey]);
    }
}
