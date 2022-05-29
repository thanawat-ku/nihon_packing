<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $findPack;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(PackFinder $findPack,Responder $responder)
    {
        
        $this->findPack = $findPack;
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

        $rtdata['message']="Get Pack Successful";
        $rtdata['error']=false;
        $rtdata['packs']=$this->findPack->findPacks($params);        

        return $this->responder->withJson($response, $rtdata);
    }
}