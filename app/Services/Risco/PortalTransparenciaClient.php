<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\Enums\FonteSancao;
use App\Services\Risco\Exceptions\PortalUnavailable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP fino da API de Dados do Portal da Transparência (CGU). Cuida só
 * do transporte: autentica com o header `chave-api-dados`, busca a 1ª página de
 * um cadastro de sanção filtrando pelo CNPJ (ou uma página de contratos por
 * fornecedor) e devolve os registros crus. A
 * regra (revalidar o CNPJ, mapear, cachear) fica em quem consome: o
 * {@see RiscoService} e o serviço de contratos públicos (App\Services\Contratos).
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
        return $this->lista('/'.$fonte->endpoint(), [
            $fonte->parametroSancionado() => $cnpjDigits,
            'pagina' => 1,
        ], $fonte->sigla());
    }

    /**
     * Páginas (15 registros cada) dos contratos do Poder Executivo Federal em que
     * o CPF/CNPJ é o FORNECEDOR, buscadas EM PARALELO (a API não ordena por data
     * e não diz o total — quem consome lê em lotes até a última página). Só
     * transporta: o consumidor revalida o fornecedor.
     * Qualquer página com falha → {@see PortalUnavailable} (nunca uma lista
     * parcial como se fosse completa).
     *
     * @param  list<int>  $paginas
     * @return array<int, list<array<string, mixed>>> página => registros
     *
     * @throws PortalUnavailable
     */
    public function contratosPorFornecedorPaginas(string $cnpjDigits, array $paginas): array
    {
        if (! $this->isConfigured()) {
            throw PortalUnavailable::notConfigured();
        }

        $paginas = array_values(array_unique(array_map(fn (int $p): int => max(1, $p), $paginas)));

        $respostas = Http::pool(fn (Pool $pool): array => array_map(
            fn (int $p) => $this->configurar($pool->as((string) $p))
                ->get('/contratos/cpf-cnpj', ['cpfCnpj' => $cnpjDigits, 'pagina' => $p]),
            $paginas,
        ));

        $out = [];

        foreach ($paginas as $p) {
            $resposta = $respostas[(string) $p] ?? null;

            if (! $resposta instanceof Response) {
                throw PortalUnavailable::unreachable('contratos', $resposta instanceof \Throwable ? $resposta->getMessage() : 'sem resposta');
            }

            if ($resposta->failed()) {
                throw PortalUnavailable::requestFailed('contratos', $resposta->status());
            }

            $json = $resposta->json();

            // 200 com corpo que não é lista (erro em JSON, HTML de manutenção) não
            // é "nenhum contrato": vira indisponível (e não vai para o cache).
            if (! is_array($json) || ! array_is_list($json)) {
                throw PortalUnavailable::unreachable('contratos', 'resposta que não é uma lista (HTTP '.$resposta->status().')');
            }

            $out[$p] = $this->registros($json);
        }

        return $out;
    }

    /**
     * GET de um recurso que devolve uma LISTA de registros (o padrão da API).
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     *
     * @throws PortalUnavailable
     */
    private function lista(string $path, array $query, string $alvo): array
    {
        try {
            $response = $this->request()->get($path, $query);
        } catch (ConnectionException $e) {
            throw PortalUnavailable::unreachable($alvo, $e->getMessage());
        }

        if ($response->failed()) {
            throw PortalUnavailable::requestFailed($alvo, $response->status());
        }

        return $this->registros($response->json());
    }

    /**
     * Os registros (objetos) de uma resposta-lista da API; o resto é ignorado.
     *
     * @return list<array<string, mixed>>
     */
    private function registros(mixed $decoded): array
    {
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

        return $this->configurar(Http::createPendingRequest());
    }

    /** Base, JSON, timeout e chave — iguais na chamada simples e nas do pool. */
    private function configurar(PendingRequest $request): PendingRequest
    {
        return $request->baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->withHeaders(['chave-api-dados' => (string) $this->apiKey]);
    }
}
