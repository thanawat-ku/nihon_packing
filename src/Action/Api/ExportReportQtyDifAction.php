<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\File;
use App\Domain\ReportQtyDif\Service\ReportQtyDifFinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
/**
 * Action.
 */
final class ExportReportQtyDifAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $reportQtyDifFinder;
    
    public function __construct(Responder $responder, ReportQtyDifFinder $reportQtyDifFinder)
    {
        $this->responder = $responder;
        $this->reportQtyDifFinder = $reportQtyDifFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $params = (array)$request->getQueryParams();

        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load("upload/excel/report/report_qty_dif.xlsx");
        $sheet = $spreadsheet->getSheetByName('reportQtyDif');

        // filter data from input in report qty diff
        if ($params['is_pick_date'] == "Y") {
            $dataReportQTYDif['startDate'] = $params['startDate'];
            $dataReportQTYDif['endDate'] = $params['endDate'];
        }
        $dataReportQTYDif['lot_no'] = $params['lot_no'];
        $dataReportQTYDif['part_id'] = $params['part_id'];

        $details=$this->reportQtyDifFinder->getReportQtyDif($dataReportQTYDif);

        $cell = $sheet->getCell('A2');
        $cell->setValue("From ".$params['startDate']." to ".$params['endDate']);

        $rowNo=4;
        for($i=0;$i<sizeof($details);$i++)
        {
            $cell = $sheet->getCell('A'.($rowNo+$i));
            $cell->setValue($details[$i]["part_code"]); 
            $cell = $sheet->getCell('B'.($rowNo+$i)); 
            $cell->setValue($details[$i]["part_no"]);
            $cell = $sheet->getCell('C'.($rowNo+$i)); 
            $cell->setValue($details[$i]["issue_date"]);
            $cell = $sheet->getCell('D'.($rowNo+$i)); 
            $cell->setValue($details[$i]["lot_no"]);
            $cell = $sheet->getCell('E'.($rowNo+$i)); 
            $cell->setValue($details[$i]["quantity"]); 
            $cell = $sheet->getCell('F'.($rowNo+$i)); 
            $cell->setValue($details[$i]["real_lot_qty"]);
            $cell = $sheet->getCell('G'.($rowNo+$i));
            $diff = (int)$details[$i]["quantity"] - (int)$details[$i]["real_lot_qty"]; //create variable and add value to $cell
            $cell->setValue($diff);
        }

        $excelWriter = new Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="report_qty_dif-'.
            $params["startDate"]."-".$params["endDate"].'.xlsx"');

        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new Stream($stream));
    }
}
