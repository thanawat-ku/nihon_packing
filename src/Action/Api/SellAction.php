<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SellAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $findSell;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(SellFinder $findSell,Responder $responder)
    {
        
        $this->findSell = $findSell;
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

        if (isset($params['start_date'])) {
            $params['startDate'] = $params['start_date'];
            $params['endDate'] = $params['end_date'];
        }
        
        $rtdata['message']="Get Sell Successful";
        $rtdata['error']=false;
        $rtdata['sells']=$this->findSell->findSells($params);        

        return $this->responder->withJson($response, $rtdata);
    }
}