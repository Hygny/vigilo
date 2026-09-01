<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CompanyData;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class SnapshotService
{
    /**
     * Persist a new snapshot (and its partners) for a monitored company,
     * atomically. The whole write is wrapped in a transaction so a snapshot
     * never exists without its partners.
     */
    public function store(MonitoredCompany $company, CompanyData $data, ?Carbon $capturedAt = null): CompanySnapshot
    {
        return DB::transaction(function () use ($company, $data, $capturedAt): CompanySnapshot {
            $snapshot = $company->snapshots()->create([
                'captured_at' => $capturedAt ?? Carbon::now(),
                'razao_social' => $data->razaoSocial,
                'nome_fantasia' => $data->nomeFantasia,
                'situacao_cadastral' => $data->situacaoCadastral,
                'situacao_data' => $data->situacaoData,
                'cnae_principal' => $data->cnaePrincipal,
                'porte' => $data->porte,
                'natureza_juridica' => $data->naturezaJuridica,
                'logradouro' => $data->logradouro,
                'municipio' => $data->municipio,
                'uf' => $data->uf,
                'raw_json' => $data->raw,
            ]);

            foreach ($data->partners as $partner) {
                $snapshot->partners()->create([
                    'documento' => $partner->documento,
                    'nome' => $partner->nome,
                    'qualificacao' => $partner->qualificacao,
                ]);
            }

            return $snapshot->load('partners');
        });
    }
}
