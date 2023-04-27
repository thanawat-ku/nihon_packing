<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\File;
use App\Domain\Lot\Service\LotFinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
/**
 * Action.
 */
final class ExportReportLotLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $lotFinder;
    
    public function __construct(Responder $responder, LotFinder $lotFinder)
    {
        $this->responder = $responder;
        $this->lotFinder = $lotFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $params = (array)$request->getQueryParams();

        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load("upload/excel/report/report_lot_labels.xlsx");
        $sheet = $spreadsheet->getSheetByName('report');

        // filter data from input in report stock pack
        $data['lot_no'] = $params['lot_no'];

        $details=$this->lotFinder->findLotLabels($data);
        $cell = $sheet->getCell('A2');
        $cell->setValue("Lot No:".$data['lot_no']);
        $rowNo=4;
        for($i=0;$i<count($details);$i++)
        {
            $cell = $sheet->getCell('A2');
            $cell->setValue("Lot No:".$details[$i]["lot_no"]." Gen Lot:".$details[$i]["generate_lot_no"]);
            $cell = $sheet->getCell('A'.($rowNo+$i));
            $cell->setValue($details[$i]["label_no"]); 
            $cell = $sheet->getCell('B'.($rowNo+$i)); 
            $cell->setValue($details[$i]["lot_no"]);
            $cell = $sheet->getCell('C'.($rowNo+$i)); 
            $cell->setValue($details[$i]["quantity"]);
            $cell = $sheet->getCell('D'.($rowNo+$i)); 
            if($details[$i]["status"]!="VOID"){
                $cell->setValue($details[$i]["status"]);
            }else if($details[$i]["merge_lot_no"]!=""){
                $cell->setValue($details[$i]["status"] . " (" . $details[$i]["merge_lot_no"] . ")");
            }else{
                $cell->setValue($details[$i]["status"]);
            }
            
            $cell = $sheet->getCell('E'.($rowNo+$i)); 
            $cell->setValue($details[$i]["invoice_no"]);
            $cell = $sheet->getCell('F'.($rowNo+$i)); 
            $cell->setValue($details[$i]["po_no"]);
            $cell = $sheet->getCell('G'.($rowNo+$i)); 
            $cell->setValue($details[$i]["pack_date"]);
            $cell = $sheet->getCell('H'.($rowNo+$i)); 
            $cell->setValue($details[$i]["pack_status"]);
        }

        $excelWriter = new Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="report_lot_labels-'.
            $params["lot_no"].'.xlsx"');

        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new Stream($stream));
    }
}
