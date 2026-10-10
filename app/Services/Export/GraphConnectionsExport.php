<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\DTO\Graph\GraphConnection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Gera uma planilha .xlsx das ligações do grafo (sócios, grupo, filiais e
 * vizinhos de endereço) num arquivo temporário e devolve o caminho — o chamador
 * stream-download e apaga depois. Sem I/O de rede nem autorização aqui: o
 * Livewire já autorizou a empresa de origem.
 */
final class GraphConnectionsExport
{
    /** @var list<string> */
    private const HEADINGS = ['Nome / Razão social', 'CNPJ / Documento', 'Tipo de ligação', 'Situação'];

    /**
     * @param  iterable<int, GraphConnection>  $connections
     * @return string caminho do .xlsx temporário
     */
    public function build(iterable $connections): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ligações');

        $sheet->fromArray(self::HEADINGS, null, 'A1');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $rowNumber = 2;

        foreach ($connections as $connection) {
            $sheet->fromArray([
                $connection->nome,
                $connection->documento ?? '',
                $connection->tipo,
                $connection->situacao ?? '',
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // tempnam cria o arquivo; o writer de xlsx grava nele independentemente da
        // extensão. O nome .xlsx do download é definido na resposta HTTP.
        $path = (string) tempnam(sys_get_temp_dir(), 'ligacoes_');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return $path;
    }
}
