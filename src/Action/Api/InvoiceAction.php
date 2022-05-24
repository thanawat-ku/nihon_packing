<?php

namespace App\Action\Api;

use App\Domain\Invoice\Service\InvoiceFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class InvoiceAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $invoiceFinder;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(InvoiceFinder $invoiceFinder,Responder $responder)
    {
        
        $this->invoiceFinder=$invoiceFinder;
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

        if(!isset($params['search_customer_id'])){
            $params['search_customer_id'] = 1;
        }
        if(!isset($params['search_invoice_status'])){
            $params['search_invoice_status']='ALL';
        }
        
        $rtdata['message']="Get Invoice Successful";
        $rtdata['error']=false;
        $rtdata['invoices']=$this->invoiceFinder->findInvoicePackings($params);

        return $this->responder->withJson($response, $rtdata);

    }
}