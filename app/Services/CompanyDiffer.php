<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ChangeEventData;
use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use App\Support\NomeSocio;

/**
 * The heart of Vigilo: a pure function that compares two snapshots of a
 * company's cadastral data and reports what changed.
 *
 * It performs no I/O and has no side effects — given the same two snapshots it
 * always returns the same list of {@see ChangeEventData}. Persisting those
 * events (timestamps, foreign keys, notifications) is the caller's job.
 */
final class CompanyDiffer
{
    /**
     * Cadastral statuses that represent a degradation and warrant a critical alert.
     */
    private const NEGATIVE_SITUACOES = ['BAIXADA', 'INAPTA', 'SUSPENSA', 'NULA'];

    /**
     * @return list<ChangeEventData>
     */
    public function diff(?CompanySnapshot $previous, CompanySnapshot $current): array
    {
        // Primeira observação é o baseline — normalmente sem alerta. Exceção: se
        // a empresa já entra em situação negativa (baixada/inapta/etc.), isso é um
        // achado de due diligence e vira alerta crítico já na entrada.
        if ($previous === null) {
            return $this->initialNegativeStatus($current);
        }

        return [
            ...$this->compareSituacao($previous, $current),
            ...$this->comparePartners($previous, $current),
            ...$this->compareScalar($previous->municipio, $current->municipio, ChangeType::AddressChanged, 'municipio', Severity::Medium),
            ...$this->compareScalar($previous->uf, $current->uf, ChangeType::AddressChanged, 'uf', Severity::Medium),
            ...$this->compareScalar($previous->logradouro, $current->logradouro, ChangeType::AddressChanged, 'logradouro', Severity::Medium),
            ...$this->compareScalar($previous->cnae_principal, $current->cnae_principal, ChangeType::CnaeChanged, 'cnae_principal', Severity::Medium),
            ...$this->compareScalar($previous->porte, $current->porte, ChangeType::PorteChanged, 'porte', Severity::Medium),
            ...$this->compareScalar($previous->razao_social, $current->razao_social, ChangeType::NameChanged, 'razao_social', Severity::Low),
            ...$this->compareScalar($previous->nome_fantasia, $current->nome_fantasia, ChangeType::NameChanged, 'nome_fantasia', Severity::Low),
        ];
    }

    /**
     * Alerta crítico quando a empresa entra no monitoramento já em situação
     * negativa (baixada/inapta/suspensa/nula). Sem valor anterior — a aresta
     * "— → BAIXADA" comunica "entrou já assim". Situação ativa/vazia = sem alerta.
     *
     * @return list<ChangeEventData>
     */
    private function initialNegativeStatus(CompanySnapshot $current): array
    {
        $situacao = trim($current->situacao_cadastral);

        if ($situacao === '' || ! $this->isNegativeSituacao($situacao)) {
            return [];
        }

        return [new ChangeEventData(ChangeType::SituacaoChanged, 'situacao_cadastral', null, $situacao, Severity::Critical)];
    }

    /**
     * @return list<ChangeEventData>
     */
    private function compareSituacao(CompanySnapshot $previous, CompanySnapshot $current): array
    {
        $old = trim($previous->situacao_cadastral);
        $new = trim($current->situacao_cadastral);

        if (strcasecmp($old, $new) === 0) {
            return [];
        }

        $severity = $this->isNegativeSituacao($new) ? Severity::Critical : Severity::High;

        return [new ChangeEventData(ChangeType::SituacaoChanged, 'situacao_cadastral', $old, $new, $severity)];
    }

    /**
     * Compara o quadro de sócios e emite um evento por sócio adicionado/removido.
     *
     * Casa cada sócio do snapshot anterior com um do novo por **pareamento
     * guloso em 3 passadas**, da identidade mais forte para a mais fraca:
     * (1) mesmo documento normalizado + mesmo nome; (2) mesmo documento;
     * (3) mesmo nome normalizado ({@see NomeSocio::norm}). Assim:
     *  - um documento que aparece/some entre dumps (comum em PF) casa pelo nome e
     *    NÃO vira um falso "saiu + entrou";
     *  - uma correção de nome com o mesmo documento casa pelo documento;
     *  - dois sócios com o MESMO documento mascarado mas nomes diferentes são
     *    pareados um a um (a passada por doc+nome e depois por doc preserva a
     *    cardinalidade), então a saída de um deles é detectada.
     * Os não pareados viram os eventos. Sócio sem nome e sem documento é ignorado.
     *
     * @return list<ChangeEventData>
     */
    private function comparePartners(CompanySnapshot $previous, CompanySnapshot $current): array
    {
        $before = array_values($previous->partners->all());
        $after = array_values($current->partners->all());

        /** @var array<int, true> $beforeMatched */
        $beforeMatched = [];
        /** @var array<int, true> $afterMatched */
        $afterMatched = [];

        $passes = [
            fn (CompanyPartner $a, CompanyPartner $b): bool => $this->sameDocument($a, $b) && $this->sameName($a, $b),
            fn (CompanyPartner $a, CompanyPartner $b): bool => $this->sameDocument($a, $b),
            fn (CompanyPartner $a, CompanyPartner $b): bool => $this->sameName($a, $b),
        ];

        foreach ($passes as $matches) {
            foreach ($before as $bi => $bp) {
                if (isset($beforeMatched[$bi])) {
                    continue;
                }

                foreach ($after as $ai => $ap) {
                    if (isset($afterMatched[$ai])) {
                        continue;
                    }

                    if ($matches($bp, $ap)) {
                        $beforeMatched[$bi] = true;
                        $afterMatched[$ai] = true;

                        break;
                    }
                }
            }
        }

        $events = [];

        foreach ($after as $ai => $partner) {
            if (! isset($afterMatched[$ai]) && $this->hasIdentity($partner)) {
                $events[] = new ChangeEventData(ChangeType::PartnerAdded, 'socio', null, $this->partnerLabel($partner), Severity::High);
            }
        }

        foreach ($before as $bi => $partner) {
            if (! isset($beforeMatched[$bi]) && $this->hasIdentity($partner)) {
                $events[] = new ChangeEventData(ChangeType::PartnerRemoved, 'socio', $this->partnerLabel($partner), null, Severity::High);
            }
        }

        return $events;
    }

    /** Mesmo documento normalizado (ambos precisam ter documento). */
    private function sameDocument(CompanyPartner $a, CompanyPartner $b): bool
    {
        $da = $this->documentKey($a);

        return $da !== null && $da === $this->documentKey($b);
    }

    /** Mesmo nome normalizado (ambos precisam ter nome). */
    private function sameName(CompanyPartner $a, CompanyPartner $b): bool
    {
        $na = $this->nameKey($a);

        return $na !== null && $na === $this->nameKey($b);
    }

    private function hasIdentity(CompanyPartner $partner): bool
    {
        return $this->documentKey($partner) !== null || $this->nameKey($partner) !== null;
    }

    /**
     * Emit a single change event when two scalar values differ.
     *
     * @return list<ChangeEventData>
     */
    private function compareScalar(?string $old, ?string $new, ChangeType $type, string $field, Severity $severity): array
    {
        // Comparação case-insensitive (como compareSituacao): mudança só de caixa
        // ("ACME LTDA" → "Acme Ltda") não é uma mudança real. O evento guarda os
        // valores originais.
        if ($this->normalizeForCompare($old) === $this->normalizeForCompare($new)) {
            return [];
        }

        return [new ChangeEventData($type, $field, $old, $new, $severity)];
    }

    /** Documento só com dígitos e `*` (máscara de CPF), ou null se vazio. */
    private function documentKey(CompanyPartner $partner): ?string
    {
        $doc = $partner->documento === null ? '' : (preg_replace('/[^0-9*]/', '', $partner->documento) ?? '');

        return $doc === '' ? null : $doc;
    }

    /** Nome normalizado (sem acento, caixa alta, espaços colapsados), ou null. */
    private function nameKey(CompanyPartner $partner): ?string
    {
        // (string) protege de um nome ausente em modelos em memória — NomeSocio::norm
        // exige string e a coluna é NOT NULL, mas "sem nome" deve virar null, não erro.
        $name = NomeSocio::norm((string) $partner->nome);

        return $name === '' ? null : $name;
    }

    private function partnerLabel(CompanyPartner $partner): string
    {
        $nome = trim($partner->nome);

        if ($nome !== '') {
            return $nome;
        }

        return $partner->documento !== null && trim($partner->documento) !== ''
            ? trim($partner->documento)
            : '—';
    }

    private function isNegativeSituacao(string $situacao): bool
    {
        return in_array(mb_strtoupper(trim($situacao)), self::NEGATIVE_SITUACOES, true);
    }

    private function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /** Normaliza para COMPARAÇÃO (trim + caixa baixa); null = vazio. */
    private function normalizeForCompare(?string $value): ?string
    {
        $normalized = $this->normalize($value);

        return $normalized === null ? null : mb_strtolower($normalized);
    }
}
