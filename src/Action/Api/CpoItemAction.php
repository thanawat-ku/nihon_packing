<?php

namespace App\Action\Api;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemAction
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
    public function __construct(CpoItemFinder $productFinder,Responder $responder, Session $session)
    {
        
        $this->productFinder=$productFinder;
        $this->responder = $responder;
        $this->session=$session;
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
        
        $rtdata['message']="Get CpoItem Successful";
        $rtdata['error']=false;
        $rtdata['cpo_item']=$this->productFinder->findCpoItem($params);    
        
        // for ($i=0; $i < count($rtdata['cpo_item']); $i++) { 
        //     // $a = $rtdata['cpo_item'][$i]['Quantity'];
        //     // $b = $rtdata['cpo_item'][$i]['PackingQtity'];
        //    if($rtdata['cpo_item'][$i]['Quantity'] != $rtdata['cpo_item'][$i]['PackingQty']){
        //         $cpodata = $rtdata['cpo_item'][$i];
        //         $cpoitemdata['message']="Get CpoItem Successful";
        //         $cpoitemdata['error']=false;
        //         $cpoitemdata['cpo_item']=$cpodata; 
        //    }
        // }

        return $this->responder->withJson($response, $rtdata);
    }
}