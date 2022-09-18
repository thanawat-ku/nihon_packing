<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\File;
use App\Domain\Pack\Service\PackFinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
/**
 * Action.
 */
final class ExportReportMfgLotAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;



    public function __construct(Responder $responder,PackFinder $finder)
    {
        $this->responder = $responder;
        $this->finder = $finder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $params = (array)$request->getQueryParams();

        $reader = new Reader\Xlsx();
        $spreadsheet = $reader->load("upload/excel/report/mfg_lot.xlsx");
        $sheet = $spreadsheet->getSheetByName('mfgLot');

        $details=$this->finder->findMfgLots($params);

        $cell = $sheet->getCell('A2');
        $cell->setValue("From ".$params['from_date']." to ".$params['to_date']);

        $rowNo=5;
        for($i=0;$i<count($details);$i++)
        {
            
            // $cell = $sheet->getCell('A'.($rowNo+$i));
            // $cell->setValue($i+1); 
            // $cell = $sheet->getCell('B'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["spare_part_code"]);
            // //$cell->setValueExplicit($details[$i]["spare_part_code"], DataType::TYPE_STRING);
            // $cell = $sheet->getCell('C'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["spare_part_name"]);
            // $cell = $sheet->getCell('D'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["store_id"]==1?"NSTA":"NSTK");
            // $cell = $sheet->getCell('E'.($rowNo+$i)); 
            // $cell->setValue($details[$i]["category_name"]);
            // if($details[$i]["operation_type"]=="RECV"){
            //     $cell = $sheet->getCell('F'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["inv_no"]);
            //     $cell = $sheet->getCell('G'.($rowNo+$i)); 
            //     $cell->setValue("purchase");
            //     $cell = $sheet->getCell('H'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["update_qty"]);
            //     $cell = $sheet->getCell('I'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]);
            //     $cell = $sheet->getCell('J'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]*$details[$i]["update_qty"]);
            // }else if($details[$i]["operation_type"]=="ISSUE"){
            //     $cell = $sheet->getCell('F'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["spare_part_issue_no"]);
            //     $cell = $sheet->getCell('G'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["issue_user"]);
            //     $cell = $sheet->getCell('Q'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["update_qty"]*-1);
            //     $cell = $sheet->getCell('R'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]);
            //     $cell = $sheet->getCell('S'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]*$details[$i]["update_qty"]*-1);
            // }else if($details[$i]["operation_type"]=="CHANGE"){
            //     $cell = $sheet->getCell('F'.($rowNo+$i)); 
            //     $cell->setValue("-");
            //     $cell = $sheet->getCell('G'.($rowNo+$i)); 
            //     $cell->setValue("change");
            //     $cell = $sheet->getCell('H'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["update_qty"]);
            //     $cell = $sheet->getCell('I'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]);
            //     $cell = $sheet->getCell('J'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]*$details[$i]["update_qty"]);
            // }else if($details[$i]["operation_type"]=="COUNT"){
            //     $cell = $sheet->getCell('F'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["count_stock_no"]);
            //     $cell = $sheet->getCell('G'.($rowNo+$i)); 
            //     $cell->setValue("count");
            //     $cell = $sheet->getCell('H'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["update_qty"]);
            //     $cell = $sheet->getCell('I'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]);
            //     $cell = $sheet->getCell('J'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["last_price"]*$details[$i]["update_qty"]);
            // }else if($details[$i]["operation_type"]=="TRANSFER"){
            //     $cell = $sheet->getCell('F'.($rowNo+$i)); 
            //     $cell->setValue($details[$i]["transfer_no"]);
            //     if($details[$i]["update_qty"]>0){
            //         $cell = $sheet->getCell('G'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["transfer_receive_user"]);
            //         $cell = $sheet->getCell('K'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["update_qty"]);
            //         $cell = $sheet->getCell('L'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["last_price"]);
            //         $cell = $sheet->getCell('M'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["last_price"]*$details[$i]["update_qty"]);                    
            //     }else{
            //         $cell = $sheet->getCell('G'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["transfer_user"]);
            //         $cell = $sheet->getCell('N'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["update_qty"]*-1);
            //         $cell = $sheet->getCell('O'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["last_price"]);
            //         $cell = $sheet->getCell('P'.($rowNo+$i)); 
            //         $cell->setValue($details[$i]["last_price"]*$details[$i]["update_qty"]*-1);    
            //     }
            // }
            
        }

        $excelWriter = new Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(File::sysGetTempDir(), 'phpxltmp');
        $tempFile = $tempFile ?: __DIR__ . '/temp.xlsx';
        $excelWriter->save($tempFile);
        $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="spare_part_movement-'.
            $params["from_date"]."-".$params["to_date"].'.xlsx"');

        $stream = fopen($tempFile, 'r+');
        return $response->withBody(new Stream($stream));
    }
}
