<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\File;
use App\Domain\ReportAll\Service\ReportAllFinder;
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
    private $reportAllFinder;
    private $regrindSpareStoreFinder;



    public function __construct(Responder $responder,ReportAllFinder $reportAllFinder)
    {
        $this->responder = $responder;
        $this->reportAllFinder = $reportAllFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $params = (array)$request->getQueryParams();

        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load("upload/excel/report/regrind.xlsx");
        $sheet = $spreadsheet->getSheetByName('ReportAll');

        $details=$this->regrindSpareStoreFinder->getReportRegrind($params);
        $store=$this->reportAllFinder->findReportAll($params);

        $cell = $sheet->getCell('A2');
        $cell->setValue("Store: ".$store[0]['regrind_store_name']);
        $cell = $sheet->getCell('A3');
        $cell->setValue("From ".$params['from_date']." to ".$params['to_date']);

        $rowNo=5;
        for($i=0;$i<count($details);$i++)
        {
            $cell = $sheet->getCell('A'.($rowNo+$i));
            $cell->setValue($i+1); 
            $cell = $sheet->getCell('B'.($rowNo+$i)); 
            $cell->setValue($details[$i]["spare_part_code"]);
            $cell = $sheet->getCell('C'.($rowNo+$i)); 
            $cell->setValue($details[$i]["spare_part_name"]);
            $cell = $sheet->getCell('D'.($rowNo+$i)); 
            $cell->setValue($details[$i]["balance_qty"]);
            $cell = $sheet->getCell('E'.($rowNo+$i)); 
            $cell->setValue($details[$i]["req_qty"]);
            $cell = $sheet->getCell('F'.($rowNo+$i)); 
            $cell->setValue($details[$i]["ret_qty"]);
        }

        $excelWriter = new Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="regrind-'.
            $params["from_date"]."-".$params["to_date"].'.xlsx"');

        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new Stream($stream));
    }
}
