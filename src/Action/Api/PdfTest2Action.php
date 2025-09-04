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
final class PdfTest2Action
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var IssueUpdater
     */
    private $packFinder;


    public function __construct(Responder $responder, PackFinder $packFinder)
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
        $pdf->SetMargins(0, 0, 0);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetAutoPageBreak(TRUE, 0);

        // set style for barcode
        $style = array(
            'border' => 2,
            'vpadding' => '1',
            'hpadding' => '1',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        $mt = 1;
        $ml = 16;
        $w = 60;
        $h = 29.5;

        // Rounded rectangle
        // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

        $start = isset($params["start"]) ? $params["start"] : 1;
        $rs = $this->packFinder->findTagSerial($params);
        $max = count($rs);
        $pdf->AddPage('P', 'A4');
        $i = 0;
        while ($i < $max) {
            for ($r = 0; $r < 10; $r++) {
                for ($c = 0; $c < 3; $c++) {
                    if ($c + $r * 3 < $start) {
                        continue;
                    }
                    if ($i < $max) {
                        $code = $rs[$i]['part_no'];
                        $name = $rs[$i]['part_name'];
                        $qty = $rs[$i]['quantity'];
                        $unit = "PCS";
                        $po = $rs[$i]['po_no'];
                        $date = date_format(date_create($rs[$i]['pack_date']), "d/m/Y");
                        $suplier_code = "N0017";
                        $serial = $rs[$i]['serial_no'];
                        $qr_text = "$code $po $qty $suplier_code $serial";
                        $pdf->write2DBarcode($qr_text, 'QRCODE,L', $ml + 5 + $c * $w, $mt + 6 + $r * $h, 16, 16, $style, 'N');
                        $pdf->SetFont('freeserif ', 'B', 6, '', 'false');
                        $pdf->Text($ml + 22 + $c * $w, $mt + 7 + $r * $h, $code);
                        $pdf->Text($ml + 22 + $c * $w, $mt + 12 + $r * $h, substr($name, 0, 25));
                        $pdf->Text($ml + 22 + $c * $w, $mt + 17 + $r * $h, "$qty $unit");
                        $i++;
                    }
                }
            }
            $start = 0;
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
