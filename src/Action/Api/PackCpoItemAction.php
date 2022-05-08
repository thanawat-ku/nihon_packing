<?php

namespace App\Action\Api;

use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PackCpoItemAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $productFinder;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(PackCpoItemFinder $productFinder,Responder $responder)
    {
        
        $this->productFinder=$productFinder;
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
        
        $rtdata['message']="Get PackCpoItem Successful";
        $rtdata['error']=false;
        $rtdata['pack_cpo_items']=$this->productFinder->findPackCpoItems($params);        

        return $this->responder->withJson($response, $rtdata);
    }
}