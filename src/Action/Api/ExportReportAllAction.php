<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\File;
use App\Domain\ReportAllData\Service\ReportAlldataFinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
/**
 * Action.
 */
final class ExportReportAllAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $reportAllDataFinder;
    
    public function __construct(Responder $responder, ReportAlldataFinder $reportAllDataFinder)
    {
        $this->responder = $responder;
        $this->reportAllDataFinder = $reportAllDataFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $params = (array)$request->getQueryParams();

        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load("upload/excel/report/report_all.xlsx");
        $sheet = $spreadsheet->getSheetByName('reportAll');

        $details=$this->reportAllDataFinder->getReportAllData($params);

        $cell = $sheet->getCell('A1');
        $cell->setValue("From ".$params['from_date']." to ".$params['to_date']);

        $rowNo=4;
        for($i=0;$i<count($details);$i++)
        {
            $cell = $sheet->getCell('A'.($rowNo+$i));
            $cell->setValue($details[$i]["PD.part_code"]); 
            $cell = $sheet->getCell('B'.($rowNo+$i)); 
            $cell->setValue($details[$i]["PD.part_no"]);
            $cell = $sheet->getCell('D'.($rowNo+$i)); 
            $cell->setValue($details[$i]["IV.invoice_no"]);
            $cell = $sheet->getCell('D'.($rowNo+$i)); 
            $cell->setValue($details[$i]["packs.pack_date"]);
            $cell = $sheet->getCell('E'.($rowNo+$i)); 
            $cell->setValue($details[$i]["PCI.cpo_item_id"]);
            $cell = $sheet->getCell('F'.($rowNo+$i)); 
            $cell->setValue($details[$i]["LT.lot_no"]);
            $cell = $sheet->getCell('G'.($rowNo+$i)); 
            $cell->setValue($details[$i]["packs.pack_no"]);
            $cell = $sheet->getCell('G'.($rowNo+$i)); 
            $cell->setValue($details[$i]["L.label_no"]);
            $cell = $sheet->getCell('G'.($rowNo+$i)); 
            $cell->setValue($details[$i]["L.quantity"]);
        }

        $excelWriter = new Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="report_all-'.
            $params["from_date"]."-".$params["to_date"].'.xlsx"');

        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new Stream($stream));
    }
}
