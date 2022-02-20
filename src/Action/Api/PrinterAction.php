<?php

namespace App\Action\Api;

use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PrinterAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $printerFinder;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(PrinterFinder $printerFinder,Responder $responder)
    {
        
        $this->printerFinder=$printerFinder;
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
        
        $rtdata['message']="Get Printer Successful";
        $rtdata['error']=false;
        $rtdata['printers']=$this->printerFinder->findPrinters($params);

        return $this->responder->withJson($response, $rtdata);

    }
}