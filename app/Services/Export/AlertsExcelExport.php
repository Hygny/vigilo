<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Models\ChangeEvent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Gera uma planilha .xlsx dos alertas (ChangeEvent) num arquivo temporário e
 * devolve o caminho — o chamador stream-download e apaga depois. Sem I/O de
 * rede nem autorização aqui: o Livewire já escopou os alertas à organização.
 */
final class AlertsExcelExport
{
    /** @var list<string> */
    private const HEADINGS = [
        'Empresa', 'CNPJ', 'Tipo de mudança', 'Campo', 'De', 'Para',
        'Severidade', 'Detectado em', 'Reconhecido em',
    ];

    /**
     * @param  iterable<int, ChangeEvent>  $events  cada um com monitoredCompany carregado
     * @return string caminho do .xlsx temporário
     */
    public function build(iterable $events): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Alertas');

        $sheet->fromArray(self::HEADINGS, null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $rowNumber = 2;

        foreach ($events as $event) {
            $company = $event->monitoredCompany;

            $sheet->fromArray([
                $company->label ?? $company->formattedCnpj(),
                $company->formattedCnpj(),
                $event->type->label(),
                $event->field,
                $event->old_value ?? '',
                $event->new_value ?? '',
                $event->severity->label(),
                $event->detected_at->format('d/m/Y H:i'),
                $event->acknowledged_at?->format('d/m/Y H:i') ?? '',
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // tempnam cria o arquivo; o writer de xlsx grava nele independentemente da
        // extensão. O nome .xlsx do download é definido na resposta HTTP.
        $path = (string) tempnam(sys_get_temp_dir(), 'alertas_');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return $path;
    }
}
