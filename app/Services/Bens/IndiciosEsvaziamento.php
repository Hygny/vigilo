<?php

declare(strict_types=1);

namespace App\Services\Bens;

use App\DTO\Esvaziamento\EsvaziamentoReport;
use App\DTO\Esvaziamento\MovimentoSocio;
use App\DTO\Esvaziamento\PossivelSucessora;
use App\DTO\Esvaziamento\ReducaoCapital;
use App\DTO\Graph\Participacao;
use App\Services\Graph\OwnershipGraphService;
use App\Services\Risco\PgfnLookup;
use App\Support\AddressNumber;
use App\Support\Cnpj;
use App\Support\ConsultaComTimeout;
use App\Support\DataReceita;
use App\Support\Endereco;
use App\Support\RazaoSocial;
use App\Support\SituacaoCadastral;
use App\Support\Tentar;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;

/**
 * Indícios de esvaziamento patrimonial e sucessão (recuperação de crédito): o
 * que aconteceu em volta da devedora DEPOIS do marco da dívida — a 1ª inscrição
 * em dívida ativa da União (PGFN) ou a data que o usuário informar.
 *
 * - Possíveis sucessoras: empresas que surgiram depois do marco (abertas, com
 *   filial aberta no endereço da devedora, ou em que um dono da devedora entrou
 *   depois dele) e coincidem em 2+ sinais — mesmo endereço, mesmo ramo (classe
 *   CNAE), sócio em comum, mesmo telefone/e-mail. Só endereço+contato não lista
 *   (é o padrão do escritório de contabilidade).
 * - Encerramento (baixada/inapta/suspensa) depois do marco.
 * - Donos atuais (não a diretoria) que entraram depois do marco (quadro todo
 *   trocado = laranja?).
 * - Saídas de dono e reduções de capital (histórico cadastral — só desde que
 *   ele existe, ver `historicoDesde`).
 * - Participações em que a devedora entrou depois do marco.
 *
 * Só dado público (QSA, cadastro, PGFN). Indício, não prova. Cada bloco degrada
 * sozinho; o relatório só vai para o cache quando todos responderam. Corte de
 * leitura em qualquer bloco liga `parcial`.
 */
final class IndiciosEsvaziamento
{
    /** Possíveis sucessoras listadas (o total vem à parte). */
    public const SUCESSORAS = 20;

    /** Linhas lidas por varredura de endereço e de participações. */
    private const VARREDURA = 400;

    /** Linhas lidas na busca das empresas das pessoas (cada CPF mascarado traz homônimos). */
    private const VARREDURA_PESSOAS = 2000;

    /** Teto de candidatas avaliadas. */
    private const CANDIDATAS = 800;

    /** Documentos por consulta (IN) e raízes por lote de matrizes. */
    private const LOTE_DOCUMENTOS = 200;

    private const LOTE_MATRIZES = 500;

    /** Eventos do histórico lidos por campo. */
    private const HISTORICO = 500;

    /** Endereço com mais empresas que isso = prédio compartilhado: só a mesma sala conta. */
    private const ENDERECO_LOTADO = 10;

    /** Situação de encerramento (código da Receita): suspensa, inapta, baixada. */
    private const ENCERRADAS = ['03', '04', '08'];

    /**
     * Qualificações de quem NÃO é dono — só administra ou representa (conferidas
     * na tabela `qualificacoes_socios` da base): 05 administrador, 08 conselheiro,
     * 09 curador, 10 diretor, 11 interventor, 12 inventariante, 13 liquidante,
     * 14 mãe, 15 pai, 16 presidente, 17 procurador, 18 secretário, 19 síndico,
     * 32 tabelião, 33 tesoureiro, 35 tutor, 39 diplomata, 40 cônsul,
     * 41 representante de org. internacional, 42 oficial de registro,
     * 43 responsável, 46 ministro das Relações Exteriores, 51 candidato,
     * 60 cônsul honorário, 61 responsável indígena, 62 representante
     * extraterritorial, 63 cotas em tesouraria, 64 administrador judicial,
     * 70–73 administrador/conselheiro/diretor/presidente do exterior. Ficam fora
     * da troca de quadro e da busca de empresas das pessoas.
     */
    private const SEM_PARTICIPACAO = ['05', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '32', '33', '35', '39', '40', '41', '42', '43', '46', '51', '60', '61', '62', '63', '64', '70', '71', '72', '73'];

    /** Documento de sócio estrangeiro na base (não identifica ninguém). */
    private const DOC_ESTRANGEIRO = '00000000000000';

    private const ORDEM_MATRIZ = '0001';

    /** Colunas do estabelecimento usadas para os sinais. */
    private const COLUNAS_ESTABELECIMENTO = ['cnpj_basico', 'cnpj_ordem', 'situacao_cadastral', 'data_inicio_atividade', 'cnae_fiscal_principal', 'cep', 'numero', 'complemento', 'ddd_1', 'telefone_1', 'correio_eletronico'];

    public function __construct(
        private readonly PgfnLookup $pgfn,
        private readonly OwnershipGraphService $grafo,
        private readonly string $connection,
        private readonly int $timeoutMs = 8000,
        private readonly int $cacheMinutos = 60,
    ) {}

    /** @param  ?string  $marcoInformado  AAAA-MM-DD já validada (substitui a PGFN como marco) */
    public function para(string $cnpj, ?string $marcoInformado = null): EsvaziamentoReport
    {
        $digits = Cnpj::normalize($cnpj);
        $chave = 'esvaziamento:'.$digits.':'.($marcoInformado ?? EsvaziamentoReport::MARCO_PGFN);

        $cache = $this->cacheMinutos > 0 ? Cache::get($chave) : null;
        if ($cache instanceof EsvaziamentoReport) {
            return $cache;
        }

        $report = $this->montar($digits, substr($digits, 0, 8), $marcoInformado);

        if ($this->cacheMinutos > 0 && $report->completo()) {
            Cache::put($chave, $report, now()->addMinutes($this->cacheMinutos));
        }

        return $report;
    }

    private function montar(string $digits, string $basico, ?string $marcoInformado): EsvaziamentoReport
    {
        $divida = Tentar::ouNull(fn (): array => [
            'resumo' => $this->pgfn->para($digits),
            'primeira' => $this->pgfn->primeiraInscricao($digits),
        ]);

        $marco = $marcoInformado ?? $divida['primeira'] ?? null;
        $resumo = $divida['resumo'] ?? null;

        if ($marco === null) {
            return new EsvaziamentoReport(marco: null, origemMarco: null, pgfnConsultada: $divida !== null);
        }

        $db = DB::connection($this->connection);
        $matriz = Tentar::ouNull(fn (): ?object => $this->matriz($db, $basico));
        $quadro = Tentar::ouNull(fn (): array => $this->qsa($db, $basico));
        $qsa = $quadro['lista'] ?? null;
        $historico = Tentar::ouNull(fn (): array => $this->historico($db, $basico, $marco));
        // Só donos contam (sócio/titular): a troca de diretoria de uma S.A. é rotina.
        $donos = $qsa !== null ? $this->soDonos($qsa) : null;
        $saidas = $historico['saidas'] ?? null;
        // Os bindings da varredura têm CPF mascarado: falha reportada sem o SQL.
        $varredura = $matriz !== null && $donos !== null
            ? $this->semDadosNoLog(fn (): array => $this->sucessoras($db, $basico, $matriz, [...$donos, ...($saidas ?? [])], $marco))
            : null;
        $participacoes = Tentar::ouNull(fn (): array => $this->participacoesNovas($digits, $marco));

        $dataSituacao = DataReceita::ymd($matriz->data_situacao_cadastral ?? null);
        $encerrada = in_array((string) ($matriz->situacao_cadastral ?? ''), self::ENCERRADAS, true) && $dataSituacao !== null && $dataSituacao >= $marco;

        $entradas = $donos !== null
            ? array_values(array_filter($donos, fn (MovimentoSocio $s): bool => $s->data !== null && $s->data >= $marco))
            : null;

        return new EsvaziamentoReport(
            marco: $marco,
            origemMarco: $marcoInformado !== null ? EsvaziamentoReport::MARCO_INFORMADO : EsvaziamentoReport::MARCO_PGFN,
            pgfnConsultada: $divida !== null,
            dividaTotal: $resumo?->total,
            inscricoes: $resumo->inscricoes ?? 0,
            encerradaSituacao: $encerrada ? SituacaoCadastral::label($matriz->situacao_cadastral ?? null) : null,
            encerradaEm: $encerrada ? $dataSituacao : null,
            sucessoras: $varredura !== null ? array_slice($varredura['sucessoras'], 0, self::SUCESSORAS) : null,
            totalSucessoras: $varredura !== null ? count($varredura['sucessoras']) : 0,
            outrasLigadas: $varredura['outras'] ?? 0,
            parcial: ($varredura['parcial'] ?? false) || ($quadro['parcial'] ?? false) || ($historico['parcial'] ?? false) || ($participacoes['parcial'] ?? false),
            entradas: $entradas,
            quadroTodoAposMarco: $donos !== null && $donos !== [] && $entradas !== null && count($entradas) === count($donos),
            saidas: $saidas,
            reducoesCapital: $historico['reducoes'] ?? null,
            historicoDesde: $historico['desde'] ?? null,
            participacoes: $participacoes['lista'] ?? null,
        );
    }

    private function matriz(ConnectionInterface $db, string $basico): ?object
    {
        $row = $db->table('estabelecimentos')
            ->where('cnpj_basico', $basico)
            ->where('cnpj_ordem', self::ORDEM_MATRIZ)
            ->first(['data_situacao_cadastral', ...self::COLUNAS_ESTABELECIMENTO]);

        return is_object($row) ? $row : null;
    }

    /**
     * Quadro societário atual da devedora (ordem fixa; no teto, parcial).
     *
     * @return array{lista: list<MovimentoSocio>, parcial: bool}
     */
    private function qsa(ConnectionInterface $db, string $basico): array
    {
        $rows = $db->table('socios as s')
            ->leftJoin('qualificacoes_socios as q', 's.qualificacao_do_socio', '=', 'q.codigo')
            ->where('s.cnpj_basico', $basico)
            ->orderBy('s.nome_socio')
            ->orderBy('s.cnpj_cpf_do_socio')
            ->limit(self::VARREDURA)
            ->get(['s.nome_socio', 's.cnpj_cpf_do_socio', 's.data_entrada_sociedade', 's.qualificacao_do_socio', 'q.descricao']);

        $out = [];

        foreach ($rows as $r) {
            $nome = trim((string) ($r->nome_socio ?? ''));

            if ($nome !== '') {
                $out[] = new MovimentoSocio(
                    nome: $nome,
                    documento: trim((string) ($r->cnpj_cpf_do_socio ?? '')),
                    qualificacao: is_string($r->descricao ?? null) ? $r->descricao : null,
                    data: DataReceita::ymd($r->data_entrada_sociedade ?? null),
                    dono: $this->dono($r->qualificacao_do_socio ?? null),
                );
            }
        }

        return ['lista' => $out, 'parcial' => count($rows) >= self::VARREDURA];
    }

    /**
     * Saídas de DONO e reduções de capital depois do marco (histórico cadastral).
     *
     * @return array{saidas: list<MovimentoSocio>, reducoes: list<ReducaoCapital>, desde: ?string, parcial: bool}
     */
    private function historico(ConnectionInterface $db, string $basico, string $marco): array
    {
        $eventos = fn (string $campo) => $db->table('historico_cadastral')
            ->where('cnpj_basico', $basico)
            ->where('campo', $campo)
            ->where('detectado_em', '>=', $marco)
            ->orderByDesc('detectado_em')
            ->limit(self::HISTORICO)
            ->get(['valor_anterior', 'valor_novo', 'detectado_em']);

        $saidas = [];
        $linhasSaida = $eventos('socio_saida');

        foreach ($linhasSaida as $r) {
            $s = json_decode((string) $r->valor_anterior, true);
            $nome = is_array($s) ? trim((string) ($s['nome'] ?? '')) : '';

            if ($nome !== '' && $this->dono($s['qualificacao'] ?? null)) {
                $saidas[] = new MovimentoSocio($nome, trim((string) ($s['documento'] ?? '')), null, substr((string) $r->detectado_em, 0, 10));
            }
        }

        $reducoes = [];
        $linhasCapital = $eventos('capital_social');

        foreach ($linhasCapital as $r) {
            $de = is_numeric($r->valor_anterior) ? (float) $r->valor_anterior : null;
            $para = is_numeric($r->valor_novo) ? (float) $r->valor_novo : null;

            if ($de !== null && $para !== null && $para < $de) {
                $reducoes[] = new ReducaoCapital($de, $para, substr((string) $r->detectado_em, 0, 10));
            }
        }

        $desde = $db->table('historico_execucoes')->where('status', 'ok')->min('referencia');

        return [
            'saidas' => $saidas,
            'reducoes' => $reducoes,
            'desde' => is_string($desde) ? $desde : null,
            'parcial' => count($linhasSaida) >= self::HISTORICO || count($linhasCapital) >= self::HISTORICO,
        ];
    }

    /**
     * Empresas em que a devedora entrou como sócia depois do marco. A leitura é
     * ordenada por ativas/nome: no teto, a lista pode estar cortada → parcial.
     *
     * @return array{lista: list<Participacao>, parcial: bool}
     */
    private function participacoesNovas(string $digits, string $marco): array
    {
        $todas = $this->grafo->participations($digits, self::VARREDURA);

        return [
            'lista' => array_values(array_filter($todas, fn (Participacao $p): bool => $p->dataEntrada !== null && $p->dataEntrada >= $marco)),
            'parcial' => count($todas) >= self::VARREDURA,
        ];
    }

    /**
     * Possíveis sucessoras: candidatas no mesmo endereço + empresas das pessoas
     * (donos atuais e os que saíram depois do marco), filtradas pelo que
     * aconteceu depois do marco e pontuadas por sinais coincidentes.
     *
     * @param  list<MovimentoSocio>  $pessoas
     * @return array{sucessoras: list<PossivelSucessora>, outras: int, parcial: bool}
     */
    private function sucessoras(ConnectionInterface $db, string $basico, object $matriz, array $pessoas, string $marco): array
    {
        [$noEndereco, $lotado, $enderecoCheio] = $this->noMesmoEndereco($db, $basico, $matriz);
        [$emComum, $pessoasCheio] = $this->dasPessoas($db, $basico, $pessoas);

        $todas = array_values(array_unique(array_map('strval', [...array_keys($noEndereco), ...array_keys($emComum)])));
        $candidatas = array_slice($todas, 0, self::CANDIDATAS);
        $sucessoras = [];
        $outras = 0;

        foreach ($this->matrizes($db, $candidatas) as $b => $c) {
            $b = (string) $b; // raiz numérica vira chave int no array do PHP
            // Casou pelo endereço? Vale o estabelecimento de lá (filial nova de empresa antiga = trespasse).
            $local = $noEndereco[$b] ?? null;
            $inicio = DataReceita::ymd(($local ?? $c)->data_inicio_atividade ?? null);
            $entradas = array_values(array_filter($emComum[$b] ?? [], fn (?string $e): bool => $e !== null && $e >= $marco));
            $aberta = $inicio !== null && $inicio >= $marco;

            if (! $aberta && $entradas === []) {
                continue; // nada aconteceu depois da dívida
            }

            $sinais = $this->sinais($matriz, $c, $local, $lotado, isset($emComum[$b]));

            if (! $this->listavel($sinais)) {
                $outras++;

                continue;
            }

            $sucessoras[] = new PossivelSucessora(
                cnpj: Cnpj::matrizFromBasico($b) ?? $b,
                nome: RazaoSocial::exibir(is_string($c->razao_social ?? null) ? $c->razao_social : null) ?? $b,
                situacao: SituacaoCadastral::label($c->situacao_cadastral ?? null) ?: null,
                inicio: $inicio,
                sinais: $sinais,
                sociosEmComum: array_map('strval', array_keys($emComum[$b] ?? [])),
                abertaAposMarco: $aberta,
                entradaAposMarco: $entradas === [] ? null : max($entradas),
                filialNoEndereco: $local !== null && ($local->cnpj_ordem ?? self::ORDEM_MATRIZ) !== self::ORDEM_MATRIZ,
            );
        }

        // Mais sinais primeiro; empate: o que aconteceu mais recentemente (abertura ou entrada de sócio).
        usort($sucessoras, fn (PossivelSucessora $x, PossivelSucessora $y): int => [count($y->sinais), $y->quando() ?? ''] <=> [count($x->sinais), $x->quando() ?? '']);

        return ['sucessoras' => $sucessoras, 'outras' => $outras, 'parcial' => $enderecoCheio || $pessoasCheio || count($todas) > self::CANDIDATAS];
    }

    /**
     * Sinais em comum com a devedora. Endereço/CNAE/contato pelo estabelecimento
     * que casou no endereço ($local) e pela matriz da candidata.
     *
     * @return list<string>
     */
    private function sinais(object $devedora, object $matriz, ?object $local, bool $lotado, bool $socio): array
    {
        $estabelecimentos = array_filter([$local, $matriz]);
        $algum = fn (callable $teste): bool => array_filter($estabelecimentos, $teste) !== [];

        $endereco = $local !== null || (! $lotado && Endereco::mesmoLocal(
            $devedora->cep ?? null, $devedora->numero ?? null, $devedora->complemento ?? null,
            $matriz->cep ?? null, $matriz->numero ?? null, $matriz->complemento ?? null,
        ));

        return array_values(array_filter([
            $endereco ? PossivelSucessora::ENDERECO : null,
            $algum(fn (object $e): bool => $this->mesmaClasse($devedora->cnae_fiscal_principal ?? null, $e->cnae_fiscal_principal ?? null)) ? PossivelSucessora::CNAE : null,
            $socio ? PossivelSucessora::SOCIO : null,
            $algum(fn (object $e): bool => $this->mesmoContato($devedora, $e)) ? PossivelSucessora::CONTATO : null,
        ]));
    }

    /**
     * 3+ sinais (forte) ou 2 com sócio ou CNAE entre eles (médio). Endereço +
     * contato sozinhos = escritório de contabilidade: não lista.
     *
     * @param  list<string>  $sinais
     */
    private function listavel(array $sinais): bool
    {
        return count($sinais) >= 3
            || (count($sinais) === 2 && array_intersect($sinais, [PossivelSucessora::SOCIO, PossivelSucessora::CNAE]) !== []);
    }

    /**
     * Estabelecimentos de outras empresas no mesmo CEP + número (regras de
     * {@see Endereco}). Prédio lotado (> ENDERECO_LOTADO empresas no CEP+número,
     * contando todas as salas): só os da MESMA sala contam. Por raiz fica o
     * estabelecimento aberto mais recentemente.
     *
     * @return array{array<string, object>, bool, bool} [raiz => estabelecimento, lotado, bateu no teto]
     */
    private function noMesmoEndereco(ConnectionInterface $db, string $basico, object $matriz): array
    {
        $cep = Endereco::cepUtil($matriz->cep ?? null);
        $numero = AddressNumber::normalize((string) ($matriz->numero ?? ''));

        if ($cep === null || Endereco::numeroInespecifico($numero)) {
            return [[], false, false];
        }

        $rows = ConsultaComTimeout::executar($db, $this->timeoutMs, fn () => $db->table('estabelecimentos as e')
            ->where('e.cep', $cep)
            ->where('e.cnpj_basico', '!=', $basico)
            ->where(DB::raw(AddressNumber::sqlExpression('e.numero')), '=', $numero)
            ->limit(self::VARREDURA)
            ->get(array_map(fn (string $c): string => 'e.'.$c, self::COLUNAS_ESTABELECIMENTO)));

        $sala = Endereco::complemento($matriz->complemento ?? null);
        $ocupantes = [];
        $compativeis = [];
        $mesmaSala = [];
        // Por raiz, fica o estabelecimento aberto mais recentemente.
        $guardar = function (array &$lista, stdClass $r): void {
            $b = (string) $r->cnpj_basico;

            if (! isset($lista[$b]) || (string) ($r->data_inicio_atividade ?? '') > (string) ($lista[$b]->data_inicio_atividade ?? '')) {
                $lista[$b] = $r;
            }
        };

        foreach ($rows as $r) {
            $ocupantes[(string) $r->cnpj_basico] = true; // lotação conta o prédio todo, todas as salas
            $compl = Endereco::complemento($r->complemento ?? null);

            if ($sala !== '' && $compl !== '' && $compl !== $sala) {
                continue; // outra sala do mesmo prédio
            }

            $guardar($compativeis, $r);

            if ($sala !== '' && $compl === $sala) {
                $guardar($mesmaSala, $r);
            }
        }

        $lotado = count($ocupantes) > self::ENDERECO_LOTADO;

        return [$lotado ? $mesmaSala : $compativeis, $lotado, count($rows) >= self::VARREDURA];
    }

    /**
     * Outras empresas das pessoas — PF pelo CPF mascarado E nome (o mascarado
     * sozinho colide), PJ pela raiz.
     *
     * @param  list<MovimentoSocio>  $pessoas
     * @return array{array<string, array<string, ?string>>, bool} [raiz => [nome => entrada]], bateu no teto
     */
    private function dasPessoas(ConnectionInterface $db, string $basico, array $pessoas): array
    {
        $nomesPorDoc = [];

        foreach ($pessoas as $p) {
            if ($p->documento !== '' && $p->documento !== self::DOC_ESTRANGEIRO) {
                $nomesPorDoc[$p->documento][$p->nome] = true;
            }
        }

        $emComum = [];
        $cheio = false;

        foreach (array_chunk(array_map('strval', array_keys($nomesPorDoc)), self::LOTE_DOCUMENTOS) as $docs) {
            $rows = ConsultaComTimeout::executar($db, $this->timeoutMs, fn () => $db->table('socios')
                ->whereIn('cnpj_cpf_do_socio', $docs)
                ->where('cnpj_basico', '!=', $basico)
                ->limit(self::VARREDURA_PESSOAS)
                ->get(['cnpj_basico', 'cnpj_cpf_do_socio', 'nome_socio', 'data_entrada_sociedade']));

            $cheio = $cheio || count($rows) >= self::VARREDURA_PESSOAS;

            foreach ($rows as $r) {
                $doc = (string) $r->cnpj_cpf_do_socio;
                $nome = trim((string) $r->nome_socio);
                $pj = preg_match('/^\d{8}$/', $doc) === 1;

                if (! $pj && ! isset($nomesPorDoc[$doc][$nome])) {
                    continue; // mesmo CPF mascarado, outra pessoa
                }

                $exibido = $pj ? (string) array_key_first($nomesPorDoc[$doc] ?? [$nome => true]) : $nome;
                $entrada = DataReceita::ymd($r->data_entrada_sociedade ?? null);
                $antes = $emComum[(string) $r->cnpj_basico][$exibido] ?? null;
                // Mais de uma linha da mesma pessoa na empresa: vale a entrada mais antiga (independe da ordem).
                $emComum[(string) $r->cnpj_basico][$exibido] = $antes === null ? $entrada : ($entrada === null ? $antes : min($antes, $entrada));
            }
        }

        return [$emComum, $cheio];
    }

    /**
     * Matriz + razão social das candidatas.
     *
     * @param  list<string>  $basicos
     * @return array<string, object>
     */
    private function matrizes(ConnectionInterface $db, array $basicos): array
    {
        $out = [];

        foreach (array_chunk($basicos, self::LOTE_MATRIZES) as $lote) {
            $rows = $db->table('estabelecimentos as e')
                ->leftJoin('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
                ->whereIn('e.cnpj_basico', $lote)
                ->where('e.cnpj_ordem', self::ORDEM_MATRIZ)
                ->get([...array_map(fn (string $c): string => 'e.'.$c, self::COLUNAS_ESTABELECIMENTO), 'emp.razao_social']);

            foreach ($rows as $r) {
                $out[(string) $r->cnpj_basico] = $r;
            }
        }

        return $out;
    }

    /**
     * @param  list<MovimentoSocio>  $pessoas
     * @return list<MovimentoSocio>
     */
    private function soDonos(array $pessoas): array
    {
        return array_values(array_filter($pessoas, fn (MovimentoSocio $s): bool => $s->dono));
    }

    /** Sócio/titular (não só administra ou representa). Sem qualificação: conta como dono. */
    private function dono(mixed $codigo): bool
    {
        return ! in_array(str_pad(trim((string) $codigo), 2, '0', STR_PAD_LEFT), self::SEM_PARTICIPACAO, true);
    }

    /**
     * Como {@see Tentar::ouNull()}, mas a falha vai para o log SEM o SQL (os
     * bindings trazem CPF mascarado e nome de pessoa).
     *
     * @template T
     *
     * @param  callable(): T  $consulta
     * @return T|null
     */
    private function semDadosNoLog(callable $consulta): mixed
    {
        try {
            return $consulta();
        } catch (Throwable $e) {
            ConsultaComTimeout::reportarSemDados($e);

            return null;
        }
    }

    /** Mesma classe CNAE (5 primeiros dígitos da subclasse). */
    private function mesmaClasse(mixed $a, mixed $b): bool
    {
        $a = preg_replace('/\D/', '', (string) $a) ?? '';
        $b = preg_replace('/\D/', '', (string) $b) ?? '';

        return strlen($a) >= 5 && substr($a, 0, 5) === substr($b, 0, 5);
    }

    /** Mesmo telefone (DDD + número) ou mesmo e-mail — vazios e zerados não contam. */
    private function mesmoContato(object $a, object $b): bool
    {
        $ta = $this->telefone($a);
        $ea = mb_strtolower(trim((string) ($a->correio_eletronico ?? '')));

        return ($ta !== null && $ta === $this->telefone($b))
            || (str_contains($ea, '@') && $ea === mb_strtolower(trim((string) ($b->correio_eletronico ?? ''))));
    }

    private function telefone(object $e): ?string
    {
        $ddd = ltrim(preg_replace('/\D/', '', (string) ($e->ddd_1 ?? '')) ?? '', '0');
        $numero = preg_replace('/\D/', '', (string) ($e->telefone_1 ?? '')) ?? '';

        return strlen($numero) >= 8 && trim($numero, '0') !== '' ? $ddd.'-'.$numero : null;
    }
}
