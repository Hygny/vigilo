<?php

declare(strict_types=1);

namespace App\DTO\Graph;

use App\DTO\Risco\PgfnDebtSummary;

/**
 * Uma linha do relatório de ligações exibido abaixo do grafo: uma entidade
 * conectada ao centro (sócio, empresa do grupo, filial ou vizinha de endereço),
 * já pronta para a tabela, o texto de "Copiar" e a exportação em Excel.
 */
final readonly class GraphConnection
{
    public function __construct(
        public string $nome,
        public ?string $documento, // CNPJ formatado ou CPF mascarado
        public string $tipo,       // Sócio | Grupo econômico | Filial | Mesmo endereço | Empresa do sócio
        public ?string $situacao,
        // Dívida ativa (PGFN) da empresa ligada — só para PJ com CNPJ; filial
        // herda a da própria empresa (mesma raiz), então não repete.
        public ?PgfnDebtSummary $divida = null,
        // A PGFN se aplica a esta linha (empresa com CNPJ, fora as filiais)?
        public bool $consultaPgfn = false,
        // CNPJ em dígitos (empresa/filial/sócio PJ) — p/ linkar o perfil; null p/ pessoa.
        public ?string $cnpj = null,
    ) {}

    /**
     * Texto da coluna "Dívida ativa (PGFN)" — fonte única da tabela, do Copiar e
     * do Excel: null quando não se aplica (pessoa física, filial); "não
     * consultada" quando a PGFN estava fora (nunca vazio, que pareceria "não
     * deve"); "" quando consultada e sem dívida.
     */
    public function dividaTexto(bool $pgfnDisponivel): ?string
    {
        if (! $this->consultaPgfn) {
            return null;
        }

        if (! $pgfnDisponivel) {
            return 'não consultada';
        }

        return $this->divida?->resumo() ?? '';
    }
}
