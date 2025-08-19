<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use TCPDF;

use App\Domain\Pack\Service\PackFinder;

/**
 * Action.
 */
final class PdfTestAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var IssueUpdater
     */
    private $packFinder;


    public function __construct(Responder $responder,PackFinder $packFinder)
    {
        $this->responder = $responder;
        $this->packFinder = $packFinder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array $args The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(10, 10, 10);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set style for barcode
        $style = array(
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        // Rounded rectangle
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        
        $rs = $this->packFinder->findTagSerial($params);
        $max = count($rs);
        for ($item = 0; $item < $max; $item++) {
            $code = $rs[$item]['part_no'];
            $name=$rs[$item]['part_name'];
            $qty=$rs[$item]['quantity'];
            $unit="PCS";
            $po=$rs[$item]['po_no'];
            $date=date_format(date_create($rs[$item]['pack_date']),"d/m/Y");
            $suplier_code="N0017";
            $serial=$rs[$item]['serial_no'];


            if ($item % 8 == 0) {
                // add a page
                $pdf->AddPage();
            }
            $x = ($item % 2) * 100;
            $y = floor(($item % 8) / 2) * 70;

            $pdf->Rect($x + 10, $y + 10, 90, 60);
            $pdf->Rect($x + 10, $y + 10, 90, 10, 'DF', null, array(180, 180, 180));

            // QRCODE,L : QR-CODE Low error correction
            $pdf->write2DBarcode("$code $qty $suplier_code $serial", 'QRCODE,L', $x + 80, $y + 50, 15, 15, $style, 'N');
            // $pdf->Text(20, 30, 'QRCODE L');

            // set font
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Text($x + 28, $y + 12, 'NIHON SEIKI THAI LIMITED');

            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Text($x + 12, $y + 22, 'Code:');
            $pdf->Text($x + 12, $y + 30, 'Name:');
            $pdf->Text($x + 12, $y + 38, 'Qty:');
            $pdf->Text($x + 60, $y + 38, 'Box:');
            $pdf->Text($x + 12, $y + 46, 'PO:');
            $pdf->Text($x + 12, $y + 54, 'Delivery Date:');

            $pdf->SetFont('helvetica', '', 10);
            $pdf->Text($x + 28, $y + 22, $code);
            $pdf->Text($x + 28, $y + 30, $name);
            $pdf->Text($x + 28, $y + 38, $qty." ".$unit);
            $pdf->Text($x + 76, $y + 38, ($item + 1).' of '.$max);
            $pdf->Text($x + 28, $y + 46, $po);
            $pdf->Text($x + 40, $y + 54, $date);
        }


        $filename = 'example_050.pdf';
        $content = $pdf->output($filename, 'I');
        //$response = $response->withType('application/pdf');
        $response = $response->withHeader('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
        $stream = fopen('php://memory', 'w+');
        fwrite($stream, $content);
        rewind($stream);

        return $response->withBody(new Stream($stream));
    }
}
