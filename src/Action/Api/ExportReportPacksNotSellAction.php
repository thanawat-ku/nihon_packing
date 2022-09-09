<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\File;
use App\Domain\ReportPacksNotSell\Service\ReportPacksNotSellFinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
/**
 * Action.
 */
final class ExportReportPacksNotSellAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $reportPacksNotSellFinder;
    
    public function __construct(Responder $responder, ReportPacksNotSellFinder $reportPacksNotSellFinder)
    {
        $this->responder = $responder;
        $this->reportPacksNotSellFinder = $reportPacksNotSellFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $params = (array)$request->getQueryParams();

        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load("upload/excel/report/report_packs_not_sell.xlsx");
        $sheet = $spreadsheet->getSheetByName('reportPacksNotSell');

        $details=$this->reportPacksNotSellFinder->getReportPacksNotSell($params);

        $cell = $sheet->getCell('A2');
        $cell->setValue("From ".$params['startDate']." to ".$params['endDate']);

        $rowNo=4;
        for($i=0;$i<count($details);$i++)
        {
            $cell = $sheet->getCell('A'.($rowNo+$i));
            $cell->setValue($details[$i]["part_code"]); 
            $cell = $sheet->getCell('B'.($rowNo+$i)); 
            $cell->setValue($details[$i]["part_no"]);
            $cell = $sheet->getCell('C'.($rowNo+$i)); 
            $cell->setValue($details[$i]["lot_no"]);
            $cell = $sheet->getCell('D'.($rowNo+$i)); 
            $cell->setValue($details[$i]["label_no"]);
            $cell = $sheet->getCell('E'.($rowNo+$i)); 
            $cell->setValue($details[$i]["generate_lot_no"]);
            $cell = $sheet->getCell('F'.($rowNo+$i)); 
            $cell->setValue($details[$i]["quantity"]);
            // $cell = $sheet->getCell('G'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["pack_no"]);
            // $cell = $sheet->getCell('H'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["label_no"]);
            // $cell = $sheet->getCell('I'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["quantity"]);
        }

        $excelWriter = new Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="report_packs_not_sell'.
            $params["startDate"]."-".$params["endDate"].'.xlsx"');

        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new Stream($stream));
    }
}
