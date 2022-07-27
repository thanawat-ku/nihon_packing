<?php

namespace App\Action\Api;

use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class InvoiceDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $invoiceFinder;
    private $tagFinder;
    private $tagUpdater;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(InvoiceFinder $invoiceFinder, TagFinder $tagFinder, TagUpdater $tagUpdater, Responder $responder)
    {

        $this->invoiceFinder = $invoiceFinder;
        $this->tagFinder = $tagFinder;
        $this->tagUpdater = $tagUpdater;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getQueryParams();
        $user_id = $params['user_id'];

        $params['findPack'] = true;
        $rtInvoice = $this->invoiceFinder->findInvoicePackings($params);

        //
        $data['status'] = 'INVOICED';
        for ($i = 0; $i < count($rtInvoice); $i++) {
            //ค้นหา tag จาก pack id
            $packID = $rtInvoice[$i]['pack_id'];
            $findTag['pack_id'] = $rtInvoice[$i]['pack_id'];
            $rtTags = $this->tagFinder->findTags($findTag);

            //for loop เพื่อหา status ของ tag 
            for ($j = 0; $j < count($rtTags); $j++) {
                //update status เป็น INVOICE เมื่อ status ของ tag นั้นๆ มี status เป็น BOXED
                if ($rtTags[$i]['status'] == "BOXED") {
                    $this->tagUpdater->updateTagPrintFromPackIDApi($packID, $data, $user_id);
                }
            }
        }

        $rtdata['message'] = "Get Invoice Successful";
        $rtdata['error'] = false;
        $rtdata['invoices'] = $rtInvoice;
        $rtdata['invoice_details'] = $this->invoiceFinder->findInvoiceDetails($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
