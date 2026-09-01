<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ChangeEventData;
use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use Illuminate\Support\Collection;

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
        // First observation is the baseline — nothing to compare against yet.
        if ($previous === null) {
            return [];
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
     * Compare partners by document (falling back to name when the document is
     * absent), emitting an event for each added or removed partner.
     *
     * @return list<ChangeEventData>
     */
    private function comparePartners(CompanySnapshot $previous, CompanySnapshot $current): array
    {
        $before = $this->keyPartners($previous->partners);
        $after = $this->keyPartners($current->partners);

        $events = [];

        foreach ($after as $key => $partner) {
            if (! isset($before[$key])) {
                $events[] = new ChangeEventData(ChangeType::PartnerAdded, 'socio', null, $this->partnerLabel($partner), Severity::High);
            }
        }

        foreach ($before as $key => $partner) {
            if (! isset($after[$key])) {
                $events[] = new ChangeEventData(ChangeType::PartnerRemoved, 'socio', $this->partnerLabel($partner), null, Severity::High);
            }
        }

        return $events;
    }

    /**
     * Emit a single change event when two scalar values differ.
     *
     * @return list<ChangeEventData>
     */
    private function compareScalar(?string $old, ?string $new, ChangeType $type, string $field, Severity $severity): array
    {
        if ($this->normalize($old) === $this->normalize($new)) {
            return [];
        }

        return [new ChangeEventData($type, $field, $old, $new, $severity)];
    }

    /**
     * @param  Collection<int, CompanyPartner>  $partners
     * @return array<string, CompanyPartner>
     */
    private function keyPartners(Collection $partners): array
    {
        $keyed = [];

        foreach ($partners as $partner) {
            $documento = $partner->documento !== null ? trim($partner->documento) : '';

            $key = $documento !== ''
                ? 'doc:'.$documento
                : 'nome:'.mb_strtolower(trim($partner->nome));

            $keyed[$key] = $partner;
        }

        return $keyed;
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
}
