<?php

declare(strict_types=1);

namespace App\DTO\Graph;

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
    ) {}
}
