<?php

namespace App\Action\Api;

use App\Domain\Invoice\Service\InvoiceFinder;
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
    private $tagUpdater;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(InvoiceFinder $invoiceFinder, TagUpdater $tagUpdater, Responder $responder)
    {

        $this->invoiceFinder = $invoiceFinder;
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

        $data['status'] = 'INVOICE';
        for ($i = 0; $i < count($rtInvoice); $i++) {
            $packID = $rtInvoice[$i]['pack_id'];
            $this->tagUpdater->updateTagPrintFromPackIDApi($packID, $data, $user_id);
        }

        $rtdata['message'] = "Get Invoice Successful";
        $rtdata['error'] = false;
        $rtdata['invoices'] = $rtInvoice;
        $rtdata['invoice_details'] = $this->invoiceFinder->findInvoiceDetails($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
